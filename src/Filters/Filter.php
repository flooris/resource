<?php

namespace Flooris\Resource\Filters;

use Illuminate\Support\Collection;
use Spatie\QueryBuilder\AllowedFilter;

interface Filter
{
    public function component(string $component): static;

    public function options(array $options): static;

    public function default(mixed $default): static;

    public function ignored(...$ignored): static;

    public function placeholder(string $placeholder): static;

    public function getComponent(): string;

    public function getName(): string;

    public function getLabel(): string;

    public function getDefault(): mixed;

    public function getIgnored(): Collection;

    public function getOptions(): array;

    public function getPlaceholder(): ?string;

    public function getAllowedFilter(): AllowedFilter;
}
