<?php


namespace Flooris\Resource\Fields\Link;

use Closure;

class FieldUrl extends AbstractFieldLink
{
    protected ?Closure $callback = null;

    public function __construct(string|callable $href, protected string $name, protected string $method = 'GET')
    {
        if (is_callable($href)) {
            $this->callback = Closure::fromCallable($href);
        } else {
            $this->href = $href;
        }
    }

    public function resolve(mixed $resource): void
    {
        $this->href = $this->href ?? call_user_func($this->callback, $resource);
    }
}