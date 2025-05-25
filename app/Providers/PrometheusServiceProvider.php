<?php

namespace App\Providers;

use App\Services\PrometheusService;
use Illuminate\Support\ServiceProvider;
use Prometheus\CollectorRegistry;
use Prometheus\Storage\Adapter;
use Prometheus\Storage\InMemory;

class PrometheusServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton(Adapter::class, fn () => new InMemory);

        $this->app->singleton(CollectorRegistry::class, function ($app) {
            return new CollectorRegistry($app->make(Adapter::class));
        });

        $this->app->singleton(PrometheusService::class, function ($app) {
            return new PrometheusService($app->make(CollectorRegistry::class));
        });
    }

    public function boot()
    {
        // no-op
    }
}
