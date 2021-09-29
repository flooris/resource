<?php

namespace Flooris\Resource\Fields;

class Boolean extends AbstractField
{
    protected string $component = 'BooleanField';

    protected function resolveAttributeValue(mixed $resource): mixed
    {
        return parent::resolveAttributeValue($resource) ?? false;
    }
}
