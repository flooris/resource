<?php

namespace Flooris\Resource\Interfaces;

interface Makeable
{
    public static function make(...$arguments): static;
}
