<?php

namespace Flooris\Resource\Modals;

use Flooris\Resource\Traits\Make;
use Flooris\Resource\Interfaces\Makeable;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Responsable;
use JsonSerializable;

abstract class AbstractModal implements Arrayable, JsonSerializable, Responsable, Makeable, Modal
{
    use Make;

    protected string $component;
    protected ?string $title = null;
    protected ?string $description = null;

    public function title(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function description(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getComponent(): string
    {
        return $this->component;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function toArray(): array
    {
        return [
            'component'   => $this->component,
            'title'       => $this->title,
            'description' => $this->description,
        ];
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }

    public function toResponse($request): array
    {
        return $this->toArray();
    }
}