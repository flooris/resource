<?php

namespace Flooris\Resource\Fields;

use http\Exception\InvalidArgumentException;

class Select extends AbstractField
{
    public const MODE_SINGLE = 'single';
    public const MODE_MUTIPLE = 'mutiple';
    public const MODE_TAGS = 'tags';

    protected string $mode = 'single';
    protected array $options = [];
    protected bool $closeOnSelect = true;

    public function mode(string $mode = self::MODE_SINGLE): static
    {
        if (! in_array($mode, [static::MODE_SINGLE, static::MODE_MUTIPLE, static::MODE_TAGS])) {
            throw new InvalidArgumentException("Mode can only be 'single', 'mutiple' or 'tags'.");
        }

        $this->mode = $mode;

        return $this;
    }

    public function options(array $options): static
    {
        $this->options = $options;

        return $this;
    }

    public function closeOnSelect(bool $closeOnSelect = true): static
    {
        $this->closeOnSelect = $closeOnSelect;

        return $this;
    }

    public function getMode(): bool
    {
        return $this->mode;
    }

    public function getOptions(): array
    {
        return $this->options;
    }

    public function getCloseOnSelect(): bool
    {
        return $this->closeOnSelect;
    }

    public function toArray(): array
    {
        return array_merge(parent::toArray(), [
            'mode'          => $this->mode,
            'options'       => $this->options,
            'closeOnSelect' => $this->closeOnSelect,
        ]);
    }
}
