<?php

namespace Flooris\Resource\Fields;

class Select extends AbstractField
{
    public string $component = 'SelectField';
    public array $options = [];

    public function options(array $options): static
    {
        $this->options = $options;

        return $this;
    }

    public function getOptions(): array
    {
        return $this->options;
    }

    public function toArray(): array
    {
        return array_merge(parent::toArray(), [
            'options' => $this->options,
        ]);
    }
}