<?php

namespace Flooris\Resource\Collections;

use Illuminate\Support\Collection;
use Illuminate\Http\Request;
use Flooris\Resource\Filters\Filter;

class Filters extends Collection
{
    public function keyByName(): static
    {
        return $this->keyBy(fn (Filter $filter) => $filter->getName());
    }

    public function resolve(Request $request): static
    {
        return $this->each(function (Filter $filter) use ($request) {
            $filter->resolve($request);
        });
    }

    public function getAllowedFilters(): static
    {
        return $this->map(fn (Filter $filter) => $filter->getAllowedFilter());
    }
}
