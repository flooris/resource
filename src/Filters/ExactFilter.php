<?php

namespace Flooris\Resource\Filters;

use Spatie\QueryBuilder\AllowedFilter;

class ExactFilter extends AbstractFilter
{
    public function allowedFilter(): AllowedFilter
    {
        return AllowedFilter::exact($this->name, $this->internalName);
    }
}
