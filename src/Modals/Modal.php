<?php

namespace Flooris\Resource\Modals;

interface Modal
{
    public function resolve(mixed $resource): void;
}