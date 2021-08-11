<?php


namespace Flooris\Resource\Fields\Link;

use Illuminate\Routing\Router;
use Flooris\Resource\Fields\Link\Traits\UrlGeneration;

class FieldAction extends AbstractFieldLink
{
    use UrlGeneration;

    public function __construct(string|array $name, protected array $parameters = [], ?callable $callback = null)
    {
        $this->initializeRoute($name)->initializeCallback();
    }

    private function initializeRoute(string|array $name): static
    {
        $this->route = app(Router::class)->getRoutes()->getByAction($this->formatAction($name));

        return $this;
    }

    private function formatAction(string|array $name): string
    {
        return is_array($name) ? trim('\\' . implode('@', $name), '\\') : $name;
    }
}