<?php


namespace Flooris\Resource\Fields\Link;


use JsonSerializable;
use Flooris\Resource\Traits\Make;
use Flooris\Resource\Interfaces\Makeable;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Responsable;

abstract class AbstractFieldLink implements FieldLink, JsonSerializable, Arrayable, Responsable, Makeable
{
    use Make;

    protected ?string $href;
    protected string $method;
    protected string $name;

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

    public function toArray(): array
    {
        return [
            'href'   => $this->href,
            'method' => $this->method,
            'name'   => $this->name,
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