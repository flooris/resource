<?php

namespace Flooris\Resource\Filters;

use JsonSerializable;
use Illuminate\Support\Collection;
use Flooris\Resource\Traits\Make;
use Spatie\QueryBuilder\AllowedFilter;
use Illuminate\Contracts\Support\Arrayable;
use Flooris\Resource\Interfaces\Makeable;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\QueryBuilderRequest;

abstract class AbstractFilter implements Filter, JsonSerializable, Arrayable, Responsable, Makeable
{
    use Make;

    protected string $internalName;
    protected string $label;
    protected ?string $placeholder = null;
    protected mixed $value;

    protected string $component = 'SelectFilter';
    protected mixed $default = null;
    protected Collection $ignored;
    protected array $options = [];

    public function __construct(protected string $name, ?string $internalName = null, ?string $label = null)
    {
        $this->internalName = $internalName ?? $name;
        $this->label        = $label ?: ucfirst($name);
        $this->ignored      = Collection::make();
    }

    abstract protected function allowedFilter(): AllowedFilter;

    public function component(string $component): static
    {
        $this->component = $component;

        return $this;
    }

    public function default(mixed $default): static
    {
        $this->default = $default;

        return $this;
    }

    public function ignored(...$ignored): static
    {
        $this->ignored = $this->ignored
            ->merge($ignored)
            ->flatten();

        return $this;
    }

    public function options(array $options): static
    {
        $this->options = $options;

        return $this;
    }

    public function placeholder(string $placeholder): static
    {
        $this->placeholder = $placeholder;

        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getLabel(): string
    {
        return $this->label;
    }

    public function getComponent(): string
    {
        return $this->component;
    }

    public function getDefault(): mixed
    {
        return $this->default;
    }

    public function getIgnored(): Collection
    {
        return $this->ignored;
    }

    public function getOptions(): array
    {
        return $this->options;
    }

    public function getPlaceholder(): ?string
    {
        return $this->placeholder;
    }

    public function getAllowedFilter(): AllowedFilter
    {
        $allowedFilter = $this->allowedFilter();

        if ($this->default) {
            $allowedFilter->default($this->default);
        }

        $allowedFilter->ignore($this->ignored->toArray());

        return $allowedFilter;
    }

    public function resolve(Request $request): void
    {
        $this->resolveValue($request);
    }

    protected function resolveValue(Request $request)
    {
        $this->value = QueryBuilderRequest::fromRequest($request)
            ->filters()
            ->get($this->name, $this->default);
    }

    public function toArray(): array
    {
        return [
            'component'   => $this->component,
            'name'        => $this->name,
            'label'       => $this->label,
            'default'     => $this->default,
            'ignored'     => $this->ignored,
            'options'     => $this->options,
            'placeholder' => $this->placeholder,
            'value'       => $this->value ?? $this->default,
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
