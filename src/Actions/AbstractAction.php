<?php

namespace Flooris\Resource\Actions;

use JsonSerializable;
use Flooris\Resource\Traits\Make;
use Illuminate\Contracts\Support\Arrayable;

abstract class AbstractAction implements Action, JsonSerializable, Arrayable
{
    use Make;

    protected string $component;
    protected string $title;
    protected string $description;

    public function __construct(protected string $href)
    {
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getComponent(): string
    {
        return $this->component;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function toArray(): array
    {
        return [
            'component'   => $this->getComponent(),
            'title'       => $this->getTitle(),
            'description' => $this->getDescription(),
            'href'        => $this->href,
        ];
    }

    public function jsonSerialize(): array
    {
       return $this->toArray();
    }
}
