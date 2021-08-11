<?php

namespace Flooris\Resource\Traits;

trait Make
{
    public static function make(...$arguments): static
    {
        return new static(...$arguments);
    }
}
