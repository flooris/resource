<?php

namespace Flooris\Resource\Fields;

use Illuminate\Support\Str;

class RelationCount extends AbstractField implements CountsRelationField
{
    protected string $component = 'RelationCountField';

    protected string $relationToCount;

    public function __construct(string $name, null|string $relation = null, ?string $label = null)
    {
        $relation = $relation ?: $name;

        $attribute = strpos($relation, '.')
            ? substr($relation, 0, strrpos($relation, '.')) . '.' . Str::snake(substr(strrchr($relation, '.'), 1)) . '_count'
            : Str::snake($relation) . '_count';

        parent::__construct($name, $attribute, $label);

        $this->relationToCount = $relation;
    }

    public function getRelationToCount(): string
    {
        return $this->relationToCount;
    }
}