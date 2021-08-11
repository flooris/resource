<?php

namespace Flooris\Resource\Fields;

class Password extends AbstractField
{
    public string $component = 'PasswordField';

    public function __construct(string $name, ?string $label = null)
    {
        parent::__construct($name, fn () => $this->default, $label);
    }
}