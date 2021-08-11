<?php

namespace Flooris\Resource\Filters;

use Spatie\QueryBuilder\AllowedFilter;

class ScopeFilter extends AbstractFilter
{
    protected function allowedFilter(): AllowedFilter
    {
        return AllowedFilter::scope($this->name, $this->internalName);
    }
}
