<?php

namespace Flooris\Resource\Filters;

use Spatie\QueryBuilder\AllowedFilter;

class PartialFilter extends AbstractFilter
{
    public function allowedFilter(): AllowedFilter
    {
        return AllowedFilter::partial($this->name, $this->internalName);
    }
}
