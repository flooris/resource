<?php

namespace Flooris\Resource\Fields;

class Boolean extends AbstractField
{
    public string $component = 'BooleanField';

    protected function resolveAttributeValue(mixed $resource): mixed
    {
        return parent::resolveAttributeValue($resource) ?? false;
    }
}