<?php

namespace Flooris\Resource\Filters;

use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\Filters\Filter as AllowedFilterCustom;

abstract class CustomFilter extends AbstractFilter implements AllowedFilterCustom
{
    protected function allowedFilter(): AllowedFilter
    {
     return AllowedFilter::custom($this->name, $this, $this->internalName);
    }
}
