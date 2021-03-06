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
use Spatie\QueryBuilder\Enums\SortDirection;
use Flooris\Resource\Fields\Link\FieldAction;
use Illuminate\Contracts\Support\Arrayable;
use Flooris\Resource\Interfaces\Makeable;
use Illuminate\Contracts\Support\Responsable;
use Spatie\QueryBuilder\Exceptions\InvalidDirection;
use Spatie\QueryBuilder\Sorts\Sort;

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
    protected ?Closure $visibleCallback = null;

    protected string $component = '';
    protected bool $searchable = false;
    protected bool $sortable = false;
    protected mixed $default = null;
    protected null|string|bool $placeholder = null;
    protected bool $required = false;
    protected bool $visible = true;
    protected bool $disabled = false;

    protected mixed $value;
    protected array $sort = ['current' => null, 'next' => null];
    protected string $sortDefaultDirection = SortDirection::ASCENDING;
    protected bool $defaultSort = false;
    protected ?Sort $customSort = null;
    protected int $defaultSortPriority = 0;

    protected ?string $qualifiedAttribute = null;
    protected ?FieldLink $link = null;

    public function __construct(protected string $name, null|string|callable $attribute = null)
    {
        $this->initializeAttribute($attribute = $attribute ?: $name)
            ->initializeComponent();
    }

    private function initializeAttribute(string|callable $attribute): static
    {
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

        return $this;
    }

    private function initializeComponent(): static
    {
        $className = static::class;

        $this->component = config("resource.field_components.$className");

        return $this;
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

    public function sortable($defaultDirection = SortDirection::ASCENDING): static
    {
        if (! in_array($defaultDirection, [
            SortDirection::ASCENDING,
            SortDirection::DESCENDING,
        ], true)) {
            throw InvalidDirection::make($defaultDirection);
        }

        $this->sortable             = true;
        $this->sortDefaultDirection = $defaultDirection;

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

    public function link(null|string|callable|FieldUrl $href, string $name = '', string $method = 'GET'): static
    {
        if ($href === null) {
            $this->link = $href;

            return $this;
        }

        $this->link = $href instanceof FieldUrl ? $href : FieldUrl::make($href, $name, $method);

        return $this;
    }

    public function route(string|FieldRoute $name, callable|array $parameters = [], ?callable $callback = null): static
    {
        $this->link = $name instanceof FieldRoute ? $name : FieldRoute::make($name, $parameters, $callback);

        return $this;
    }

    public function action(string|array|FieldAction $name, callable|array $parameters = [], ?callable $callback = null): static
    {
        $this->link = $name instanceof FieldAction ? $name : FieldAction::make($name, $parameters, $callback);

        return $this;
    }

    public function visible(callable $callback): static
    {
        $this->visibleCallback = Closure::fromCallable($callback);

        return $this;
    }

    public function component(string $component): static
    {
        $this->component = $component;

        return $this;
    }

    public function defaultSort($priority = 0): static
    {
        $this->defaultSort         = true;
        $this->defaultSortPriority = $priority;

        return $this;
    }

    public function customSort(Sort $sort): static
    {
        $this->customSort = $sort;

        return $this;
    }

    public function disabled($disabled = true): static
    {
        $this->disabled = $disabled;

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

    public function getAllowedSort(): ?AllowedSort
    {
        if ($this->qualifiedAttribute === null) {
            return null;
        }

        if ($this->customSort === null) {
            return AllowedSort::field($this->name, $this->qualifiedAttribute)
                ->defaultDirection($this->sortDefaultDirection);
        }

        return AllowedSort::custom($this->name, $this->customSort, $this->qualifiedAttribute)
            ->defaultDirection($this->sortDefaultDirection);
    }

    public function getQualifiedAttribute(): ?string
    {
        return $this->qualifiedAttribute;
    }

    public function getVisible(): bool
    {
        return $this->visible;
    }

    public function getDefaultSort(): bool
    {
        return $this->defaultSort;
    }

    public function getDefaultSortPriority(): int
    {
        return $this->defaultSortPriority;
    }

    public function getDisabled(): bool
    {
        return $this->disabled;
    }

    public function resolve(mixed $resource): void
    {
        $this->resolveVisible($resource);

        if ($this->visible === true) {
            $this->resolveValue($resource);
            $this->resolveLink($resource);
            $this->resolveLabel($resource);
            $this->resolvePlaceholder($resource);
            $this->resolveSort();
        }
    }

    public function resolveVisible(mixed $resource): void
    {
        if ($this->visibleCallback !== null) {
            $this->visible = $this->value = call_user_func($this->visibleCallback, $resource);
        }
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
        if ($this->link === null) {
            return;
        }

        $this->link->resolve($resource);
    }

    public function resolveLabel(mixed $resource): void
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

                return;
            }
        }

        $this->placeholder = __("entities.general.fields.$this->name.placeholder");
    }


    protected function resolveSort(): void
    {
        if (! $this->sortable || ! $this->qualifiedAttribute) {
            return;
        }

        $request = QueryBuilderRequest::fromRequest(app('request'));
        $sort    = $request->sorts()->first(fn (string $sort) => str_ends_with($sort, $this->name));

        $this->sort = [
            'current' => $sort,
            'next'    => match (true) {
                $sort !== null && str_starts_with($sort, '-') => null,
                $sort !== null => '-' . $this->name,
                default => $this->name,
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
            'link'        => $this->link && $this->link->getHref() ? $this->link->toArray() : null,
            'value'       => $this->value instanceof Arrayable ? $this->value->toArray() : $this->value,
            'searchable'  => $this->searchable,
            'sortable'    => $this->sortable,
            'placeholder' => $this->placeholder,
            'required'    => $this->required,
            'sort'        => $this->sort,
            'disabled'    => $this->disabled,
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
