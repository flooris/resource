<?php

namespace Flooris\Resource\Fields;

class Password extends AbstractField
{
    public function __construct(string $name, ?string $label = null)
    {
        parent::__construct($name, fn () => $this->default, $label);
    }
}
