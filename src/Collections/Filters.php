<?php

namespace Flooris\Resource\Collections;

use Illuminate\Support\Collection;
use Flooris\Resource\Filters\Filter;

class Filters extends Collection
{
    public function keyByName(): static
    {
        return $this->keyBy(fn (Filter $filter) => $filter->getName());
    }

    public function getAllowedFilters(): static
    {
        return $this->map(fn (Filter $filter) => $filter->getAllowedFilter());
    }
}
