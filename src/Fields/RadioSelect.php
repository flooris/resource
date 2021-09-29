<?php

namespace Flooris\Resource\Fields;

//TODO: Remove this field, take care of this function in vue.
class RadioSelect extends AbstractField
{
    protected string $component = 'RadioSelect';

    public function getLabel(): string
    {
        return '';
    }
}
