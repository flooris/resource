<?php

namespace Flooris\Resource\Actions;

interface Action
{
    public function resolve(mixed $resource): void;
}
