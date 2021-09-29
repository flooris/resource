<?php

namespace Flooris\Resource\Fields;

class Number extends AbstractField
{
    protected string $component = 'NumberField';
    protected int|float|null $step = null;
    protected ?int $min = null;
    protected ?int $max = null;

    public function step(int|float|null $step = null): static
    {
        if (is_float($step)) {
            $step = round($step, 2);
        }

        $this->step = $step;

        return $this;
    }

    public function min(?int $min = null): static
    {
        $this->min = $min;

        return $this;
    }

    public function max(?int $max = null): static
    {
        $this->max = $max;

        return $this;
    }

    public function getStep(): int|float|null
    {
        return $this->step;
    }

    public function getMin(): ?int
    {
        return $this->min;
    }

    public function getMax(): ?int
    {
        return $this->max;
    }

    public function toArray(): array
    {
        return array_merge(parent::toArray(), [
            'min'  => $this->min,
            'max'  => $this->max,
            'step' => $this->step,
        ]);
    }
}
