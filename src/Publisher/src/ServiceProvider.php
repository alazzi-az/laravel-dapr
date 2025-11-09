<?php

namespace AlazziAz\LaravelDaprPublisher;

use AlazziAz\LaravelDapr\Contracts\EventPublisher as EventPublisherContract;
use AlazziAz\LaravelDaprPublisher\Publishing\EventPipeline;
use Illuminate\Pipeline\Pipeline;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;

class ServiceProvider extends BaseServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/dapr.php', 'dapr');

        $this->app->singleton(EventPipeline::class, function ($app) {
            return new EventPipeline($app->make(Pipeline::class));
        });

        $this->app->singleton(EventPublisher::class);
        $this->app->alias(EventPublisher::class, EventPublisherContract::class);
    }

    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/dapr.php' => config_path('dapr.php'),
            ], 'dapr-config');
        }
    }
}
