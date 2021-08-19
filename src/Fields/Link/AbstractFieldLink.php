<?php

namespace Flooris\Resource\Fields\Link;

use JsonSerializable;
use Flooris\Resource\Modals\Modal;
use Flooris\Resource\Traits\Make;
use Flooris\Resource\Interfaces\Makeable;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Responsable;

abstract class AbstractFieldLink implements FieldLink, JsonSerializable, Arrayable, Responsable, Makeable
{
    use Make;

    protected ?string $href = null;
    protected string $method;
    protected string $name;
    protected ?Modal $modal = null;

    public function modal(Modal $modal): static
    {
        $this->modal = $modal;

        return $this;
    }

    public function getHref(): ?string
    {
        return $this->href;
    }

    public function getMethod(): string
    {
        return $this->method;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function resolve(mixed $resource): void
    {
        if ($this->modal !== null) {
            $this->modal->resolve($resource);
        }
    }

    public function toArray(): array
    {
        return [
            'href'   => $this->href,
            'method' => $this->method,
            'name'   => $this->name,
            'modal'  => $this->modal instanceof Arrayable ? $this->modal->toArray() : null,
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