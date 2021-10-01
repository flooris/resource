<?php

namespace Flooris\Resource;

use Illuminate\Support\ServiceProvider;

class ResourceServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->publishes([
            __DIR__.'/../config/resource.php.php' => config_path('resource.php'),
        ]);
    }

    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/resource.php', 'resource'
        );
    }
}
