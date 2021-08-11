<?php

namespace Flooris\Resource\Fields;

//TODO: Remove this field, take care of this function in vue.
class RadioSelect extends AbstractField
{
    public string $component = 'RadioSelect';

    public function getLabel(): string
    {
        return '';
    }
}
