<?php

namespace Flooris\Resource\Filters;

use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Builder;

class SearchFilter extends CustomFilter
{
    public function __construct(protected array $qualifiedAttributes = [], string $name = 'search', $internalName = null, ?string $label = null)
    {
        parent::__construct($name, $internalName, $label);
    }

    public function __invoke(Builder $query, mixed $value, string $property): void
    {
        $query->where(function ($query) use ($value) {
            foreach($this->qualifiedAttributes as $column) {
                $query->orWhere($column, $this->likeOperator($query) ,"%$value%");
            }
        });
    }

    private function likeOperator(Builder $query): string
    {
        return $query->getConnection()->getDriverName() === 'pgsql' ? 'ilike' : 'like';
    }
}
