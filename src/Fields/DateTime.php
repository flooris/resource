<?php

namespace Flooris\Resource\Fields;

use DateTimeInterface;

class DateTime extends AbstractField
{
    protected string $component = 'DateTimeField';
    protected string $format = "d-m-Y H:i:s";

    public function __construct(string $name, null|string|callable $attribute = null, ?string $label = null)
    {
        parent::__construct($name, $attribute, $label);
        $this->resolveCallback(fn (?DateTimeInterface $value) => $value ? $value->format($this->format) : $value);
    }

    public function format($format): static
    {
        $this->format = $format;

        return $this;
    }

    protected function getFormat(): string
    {
        return $this->format;
    }

    public function toArray(): array
    {
        return array_merge(parent::toArray(), [
            'format' => $this->format,
        ]);
    }
}
