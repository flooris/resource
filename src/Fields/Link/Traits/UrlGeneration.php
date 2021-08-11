<?php

namespace Flooris\Resource\Fields\Link\Traits;

use Closure;
use ReflectionClass;
use Illuminate\Routing\Route;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Routing\UrlRoutable;

trait UrlGeneration
{
    protected Route $route;
    protected ?Closure $callback = null;

    private function initializeCallback(?callable $callback = null): static
    {
        if ($callback) {
            $this->callback = Closure::fromCallable($callback);
        }

        return $this;
    }

    public function resolve(mixed $resource): void
    {
        $this->href = is_callable($this->callback)
            ? tap($this->resolveRoute($resource), fn ($value) => call_user_func($this->callback, $value, $resource))
            : $this->resolveRoute($resource);
        $this->method = $this->route->methods[0];
        $this->name   = $this->route->getActionMethod();
    }

    private function resolveRoute(mixed $resource): string
    {
        return app('url')->toRoute($this->route, $this->resolveRouteParameters($resource, $this->parameters), true);
    }

    private function resolveRouteParameters(mixed $resource, array $parameters = []): array
    {
        $routableParameterModels = [];

        foreach ($this->route->signatureParameters(UrlRoutable::class) as $routableParameter) {
            if (! array_key_exists($routableParameter->name, $parameters)) {
                $routableParameterModels[$routableParameter->name] = new ReflectionClass(
                    $routableParameter->getType()->getName()
                );
            }
        }

        $resolvedParameters = [];
        $relations          = $resource instanceof Model ? array_filter($resource->getRelations()) : [];

        foreach ($routableParameterModels as $key => $model) {
            //            if ($resource instanceof ListModel && $model->getName() === $resource->modelClass()) {
            //                $parameters[$key] = $resource->getRouteKey();
            //                continue;
            //            }

            if ($resource instanceof UrlRoutable && $model->isInstance($resource)) {
                $parameters[$key] = $resource->getRouteKey();
                continue;
            }

            foreach ($relations as $relation) {
                if ($relation instanceof UrlRoutable && $model->isInstance($relation)) {
                    $parameters[$key] = $relation->getRouteKey();
                }
            }
        }

        return array_merge($resolvedParameters, $parameters);
    }
}