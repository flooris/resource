<?php

namespace Flooris\Resource\Collections;

use Illuminate\Support\Collection;
use Flooris\Resource\Fields\Field;
use Flooris\Resource\Fields\CountsRelationField;

class Fields extends Collection
{
    public function keyByName(): static
    {
        return $this->keyBy(fn (Field $field) => $field->getName());
    }

    public function toColumn(): static
    {
        return $this->map(fn (Field $field) => $field->toColumn());
    }

    public function toForm(): static
    {
        return $this->keyByName()->map(fn (Field $field) => $field->getValue());
    }

    public function resolve(mixed $resource): static
    {
        return $this->each(function (Field $field) use ($resource) {
            $field->resolve($resource);
        });
    }
    
    public function resolveLabels(mixed $resource): static
    {
        return $this->each(function (Field $field) use ($resource) {
            $field->resolveLabel($resource);
        });
    }

    public function resolveQualifiedAttributes(mixed $model): static
    {
        return $this->each(function (Field $field) use ($model) {
            $field->resolveQualifiedAttribute($model);
        });
    }

    public function getAttributes(): static
    {
        return $this->map(fn (Field $field) => $field->getAttribute())->unique();
    }

    public function getQualifiedAttributes(): static
    {
        return $this->map(fn (Field $field) => $field->getQualifiedAttribute())->filter()->unique();
    }

    public function getRelations(): static
    {
        return $this->map(fn (Field $field) => $field->getRelation())->filter()->unique();
    }

    public function getUniqueRelations(): static
    {
        return $this->getRelations()->reject(fn (string $relation, string $key) => $this->getRelations()
            ->contains(fn (string $oRelation, string $oKey) => $oKey !== $key && str_starts_with($oRelation, $relation)));
    }

    public function getRelationsToCount(): static
    {
        return $this->filter(fn (Field $field) => $field instanceof CountsRelationField)
            ->map(fn (CountsRelationField $field) => $field->getRelationToCount());
    }

    public function getSearchable(): static
    {
        return $this->filter(fn (Field $field) => $field->getSearchable());
    }

    public function getSortable(): static
    {
        return $this->filter(fn (Field $field) => $field->getSortable());
    }
    
    public function getAllowedSorts(): static
    {
        return $this->getSortable()->map(fn (Field $field) => $field->getAllowedSort())->filter();
    }
}
