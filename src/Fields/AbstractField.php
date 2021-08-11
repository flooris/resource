<?php

namespace Flooris\Resource\Fields;

use Closure;
use JsonSerializable;
use Flooris\Resource\Traits\Make;
use Spatie\QueryBuilder\AllowedSort;
use Illuminate\Support\Facades\Lang;
use Illuminate\Database\Eloquent\Model;
use Flooris\Resource\Fields\Link\FieldUrl;
use Spatie\QueryBuilder\QueryBuilderRequest;
use Flooris\Resource\Fields\Link\FieldLink;
use Flooris\Resource\Fields\Link\FieldRoute;
use Flooris\Resource\Fields\Link\FieldAction;
use Illuminate\Contracts\Support\Arrayable;
use Flooris\Resource\Interfaces\Makeable;
use Illuminate\Contracts\Support\Responsable;

abstract class AbstractField implements Field, JsonSerializable, Arrayable, Responsable, Makeable
{
    use Make;

    public const COMPUTED_FIELD = 'ComputedField';

    protected string $attribute;
    protected string $label;
    protected ?string $column = null;
    protected ?string $relation = null;
    protected ?Closure $computedCallback = null;
    protected ?Closure $resolveCallback = null;

    protected string $component = '';
    protected bool $searchable = false;
    protected bool $sortable = false;
    protected mixed $default = null;
    protected null|string|boolean $placeholder = null;
    protected bool $required = false;
    protected bool $editable = true;

    protected mixed $value;
    protected array $sort = ['current' => null, 'next' => null];
    protected ?string $qualifiedAttribute = null;
    protected null|FieldLink|FieldRoute|FieldAction $link = null;

    public function __construct(protected string $name, null|string|callable $attribute = null)
    {
        $attribute = $attribute ?: $name;

        if (is_callable($attribute)) {
            $this->attribute        = static::COMPUTED_FIELD;
            $this->computedCallback = Closure::fromCallable($attribute);
        } else {
            $this->attribute = $attribute;

            if ((strpos($attribute, '.'))) {
                $this->column   = substr(strrchr($attribute, '.'), 1);
                $this->relation = substr($attribute, 0, strrpos($attribute, '.'));
            } else {
                $this->column = $attribute;
            }
        }
    }

    public function label(string $label): static
    {
        $this->label = $label;

        return $this;
    }

    public function resolveCallback(callable $callback): static
    {
        $this->resolveCallback = Closure::fromCallable($callback);

        return $this;
    }

    public function searchable(bool $searchable = true): static
    {
        $this->searchable = $searchable;

        return $this;
    }

    public function sortable(bool $sortable = true): static
    {
        $this->sortable = $sortable;

        return $this;
    }

    public function default(mixed $default): static
    {
        $this->default = $default;

        return $this;
    }

    public function placeholder(bool|string $placeholder = true): static
    {
        $this->placeholder = $placeholder;

        return $this;
    }

    public function required($required = true): static
    {
        $this->required = $required;

        return $this;
    }

    public function link(string|callable $href, string $name, string $method = 'GET'): static
    {
        $this->link = FieldUrl::make($href, $name, $method);

        return $this;
    }

    public function route(string $name, array $parameters = [], ?callable $callback = null): static
    {
        $this->link = FieldRoute::make($name, $parameters, $callback);

        return $this;
    }

    public function action(string|array $name, array $parameters = [], ?callable $callback = null): static
    {
        $this->link = FieldAction::make($name, $parameters, $callback);

        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getAttribute(): string
    {
        return $this->attribute;
    }

    public function getLabel(): ?string
    {
        return $this->label;
    }

    public function getColumn(): ?string
    {
        return $this->column;
    }

    public function getRelation(): ?string
    {
        return $this->relation;
    }

    public function getSearchable(): bool
    {
        return $this->searchable;
    }

    public function getSortable(): bool
    {
        return $this->sortable;
    }

    public function getDefault(): mixed
    {
        return $this->default;
    }

    public function getValue(): mixed
    {
        return $this->value;
    }

    public function getComponent(): string
    {
        return $this->component;
    }

    public function getAllowedSort(): string|AllowedSort
    {
        return $this->getQualifiedAttribute();
    }

    public function getQualifiedAttribute(): ?string
    {
        return $this->qualifiedAttribute;
    }

    public function resolve(mixed $resource): void
    {
        $this->resolveValue($resource);
        $this->resolveLink($resource);
        $this->resolveLabel($resource);
        $this->resolvePlaceholder($resource);
        $this->resolveSort();
    }

    protected function resolveValue(mixed $resource): void
    {
        if ($this->attribute === static::COMPUTED_FIELD) {
            $this->value = call_user_func($this->computedCallback, $resource);
        } else if (! $this->resolveCallback) {
            $this->value = $this->resolveAttributeValue($resource);
        } elseif (is_callable($this->resolveCallback)) {
            tap($this->resolveAttributeValue($resource), function ($value) use ($resource) {
                $this->value = call_user_func($this->resolveCallback, $value, $resource);
            });
        }
    }

    protected function resolveLink(mixed $resource): void
    {
        if ($this->link) {
            $this->link->resolve($resource);
        }
    }

    protected function resolveLabel(mixed $resource): void
    {
        if (isset($this->label)) {
            return;
        }

        if (is_object($resource)) {
            $resourceClass = $resource::class;
            $resourceKey   = "entities.$resourceClass.fields.$this->name.label";

            if (Lang::has($resourceKey)) {
                $this->label = __($resourceKey);

                return;
            }

            $generalKey = "entities.general.fields.$this->name.label";

            if (Lang::has($generalKey)) {
                $this->label = __($generalKey);

                return;
            }

        }

        $this->label = ucfirst($this->name);
    }

    protected function resolvePlaceholder(mixed $resource): void
    {
        if ($this->placeholder === null || is_string($this->placeholder)) {
            return;
        }

        if ($this->placeholder === false) {
            $this->placeholder = null;

            return;
        }

        if (is_object($resource)) {
            $resourceClass = $resource::class;
            $resourceKey   = "entities.$resourceClass.fields.$this->name.placeholder";

            if (Lang::has($resourceKey)) {
                $this->placeholder = __($resourceKey);
            } else {
                $generalKey        = "entities.general.fields.$this->name.placeholder";
                $this->placeholder = __($generalKey);
            }
        }
    }


    protected function resolveSort(): void
    {
        if (! $this->sortable || ! $this->qualifiedAttribute) {
            return;
        }

        $request = QueryBuilderRequest::fromRequest(app('request'));
        $sort    = $request->sorts()->first(fn (string $sort) => str_ends_with($sort, $this->qualifiedAttribute));

        $this->sort = [
            'current' => $sort,
            'next'    => match (true) {
                $sort && str_starts_with($sort, '-') => null,
                $sort => '-' . $this->qualifiedAttribute,
                default => $this->qualifiedAttribute,
            },
        ];
    }

    protected function resolveAttributeValue(mixed $resource): mixed
    {
        return data_get($resource, $this->attribute);
    }

    public function resolveQualifiedAttribute(mixed $model): void
    {
        if ($this->attribute === static::COMPUTED_FIELD || ! $model instanceof Model) {
            $this->qualifiedAttribute = null;

            return;
        }

        if ($this->relation === null) {
            $this->qualifiedAttribute = $model->hasGetMutator($this->column) ? null : $model->qualifyColumn($this->column);
        } else {
            $qualifiedColumn = collect(explode('.', $this->relation))->map(function (string $relation) use (&$model) {
                    $model = $model::query()->getRelation($relation)->getModel();

                    return $model->getTable();
                })->last() . '.' . $this->column;

            $this->qualifiedAttribute = $model->hasGetMutator($this->column) ? null : $qualifiedColumn;
        }
    }

    public function toColumn(): array
    {
        return [
            'label'    => $this->label,
            'sortable' => $this->sortable,
            'sort'     => $this->sort,
        ];
    }

    public function toArray(): array
    {
        return [
            'component'   => $this->component,
            'name'        => $this->name,
            'attribute'   => $this->attribute,
            'label'       => $this->label,
            'link'        => $this->link,
            'value'       => $this->value,
            'searchable'  => $this->searchable,
            'sortable'    => $this->sortable,
            'placeholder' => $this->placeholder,
            'required'    => $this->required,
            'sort'        => $this->sort,
        ];
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }

    public function toResponse($request): array
    {
        return $this->toArray();
    }
}
