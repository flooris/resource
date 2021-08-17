<?php

namespace Flooris\Resource\Fields\Link;

use Illuminate\Routing\Router;
use Flooris\Resource\Fields\Link\Traits\UrlGeneration;

class FieldRoute extends AbstractFieldLink
{
    use UrlGeneration;

    public function __construct(string $name, callable|array $parameters = [], ?callable $callback = null)
    {
        $this->initializeRoute($name)
            ->initizalizeParameters($parameters)
            ->initializeCallback($callback);
    }

    private function initializeRoute(string $name): static
    {
        $this->route = app(Router::class)->getRoutes()->getByName($name);

        return $this;
    }
}