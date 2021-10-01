<?php

namespace Flooris\Resource\Fields;

class Boolean extends AbstractField
{
    protected function resolveAttributeValue(mixed $resource): mixed
    {
        return parent::resolveAttributeValue($resource) ?? false;
    }
}
