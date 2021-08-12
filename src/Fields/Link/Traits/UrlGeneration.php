<?php

namespace Flooris\Resource\Fields\Link\Traits;

use Closure;
use ReflectionClass;
use Illuminate\Routing\Route;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Routing\UrlRoutable;
use Illuminate\Routing\Exceptions\UrlGenerationException;

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
        if ($this->callback) {
            tap($this->resolveRoute($resource), function (?string $href) use ($resource) {
                $this->href = call_user_func($this->callback, $href, $resource);
            });
        } else {
            $this->href = $this->resolveRoute($resource);
        }

        $this->method = $this->route->methods[0];
        $this->name   = $this->route->getActionMethod();
    }

    private function resolveRoute(mixed $resource): ?string
    {
        try {
            return app('url')->toRoute($this->route, $this->resolveRouteParameters($resource, $this->parameters), true);
        } catch (UrlGenerationException $e) {
            return null;
        }
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