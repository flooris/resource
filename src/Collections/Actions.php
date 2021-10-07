<?php

namespace Flooris\Resource\Collections;

use Illuminate\Support\Collection;
use Flooris\Resource\Actions\Action;

class Actions extends Collection
{
    public function resolve(mixed $resource): static
    {
        return $this->each(function (Action $action) use ($resource): void {
            $action->resolve($resource);
        });
    }
}
