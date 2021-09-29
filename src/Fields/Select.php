<?php

namespace Flooris\Resource\Fields;

class Select extends AbstractField
{
    protected string $component = 'SelectField';
    protected array $options = [];
    protected bool $multiple = false;

    public function options(array $options): static
    {
        $this->options = $options;

        return $this;
    }

    public function multiple(bool $multiple = true): static
    {
        $this->multiple = $multiple;

        return $this;
    }

    public function getOptions(): array
    {
        return $this->options;
    }

    public function getMultiple(): bool
    {
        return $this->multiple;
    }

    public function toArray(): array
    {
        return array_merge(parent::toArray(), [
            'options'  => $this->options,
            'multiple' => $this->multiple,
        ]);
    }
}
