<?php

namespace Flooris\Resource\Fields;

interface Field
{
    public function resolveCallback(callable $callback): static;

    public function searchable(bool $searchable = true): static;

    public function sortable(bool $sortable = true): static;

    public function getName(): string;

    public function getAttribute(): string;

    public function getColumn(): ?string;

    public function getRelation(): ?string;

    public function getSearchable(): bool;

    public function getSortable(): bool;

    public function getQualifiedAttribute(): ?string;

    public function resolve(mixed $resource): void;

    public function resolveQualifiedAttribute(mixed $model): void;
}
