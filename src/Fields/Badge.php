<?php

namespace Flooris\Resource\Fields;

class Badge extends AbstractField
{
    protected string $component = 'BadgeField';
    protected array $labels = [];
    protected array $colors = [];

    public function labels(array $labels): static
    {
        $this->labels = $labels;

        return $this;
    }

    public function colors(array $colors): static
    {
        $this->colors = $colors;

        return $this;
    }

    public function toArray(): array
    {
        return array_merge(parent::toArray(), [
            'labels' => $this->labels,
            'colors' => $this->colors,
        ]);
    }
}
