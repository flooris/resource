<?php


namespace Flooris\Resource;


use JsonSerializable;
use Illuminate\Http\Request;
use InvalidArgumentException;
use Flooris\Resource\Traits\Make;
use Flooris\Resource\Collections\Fields;
use Illuminate\Contracts\Support\Arrayable;
use Flooris\Resource\Interfaces\Makeable;
use Illuminate\Contracts\Support\Responsable;

class Resource implements JsonSerializable, Arrayable, Makeable
{
    use Make;

    public static string $subjectClass;

    public mixed $subject;
    protected Request $request;

    protected Fields $fields;
    protected array $additional = [];

    public function __construct(mixed $subject = null, ?Request $request = null)
    {
        $this->initializeSubject($subject)->initializeRequest($request)->initializeFields();
    }

    protected function initializeSubject(mixed $subject): static
    {
        $this->subject = $subject ?? (isset(static::$subjectClass)
                ? new static::$subjectClass
                : throw new InvalidArgumentException('Either subject or subjectClass should be set'));

        return $this;
    }

    protected function initializeRequest(?Request $request): static
    {
        $this->request = $request ?? app(Request::class);

        return $this;
    }

    protected function initializeFields(): static
    {
        $this->fields = Fields::make($this->fields());

        return $this;
    }

    public function fields(): array
    {
        return [];
    }

    public function additional(array $additional): static
    {
        $this->additional = $additional;

        return $this;
    }

    public function toArray(): array
    {
        return array_merge([
            'resource' => $this->subject,
            'fields'   => $this->fields->resolve($this->subject)->keyByName()->toArray(),
            'form'     => $this->fields->toForm()->toArray(),
        ], $this->additional);
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