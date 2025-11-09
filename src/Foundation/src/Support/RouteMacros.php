<?php

namespace AlazziAz\LaravelDapr\Support;

use AlazziAz\LaravelDapr\Http\Controllers\HealthController;
use AlazziAz\LaravelDapr\Http\Controllers\SubscriptionController;
use Illuminate\Support\Facades\Route;

class RouteMacros
{
    public static function register(): void
    {

        if (! Route::hasMacro('daprHealth')) {
            Route::macro('daprHealth', function (): void {

                $middleware = config('dapr.health.middleware', []);
                Route::middleware($middleware)->group(function () {
                    Route::get("/healthz", HealthController::class)
                        ->name('dapr.health');
                });
            });
        }

        if (! Route::hasMacro('daprSubscriptions')) {
            Route::macro('daprSubscriptions', function (?callable $within = null, array $options = []) {
                $prefix = trim(config('dapr.http.prefix', 'dapr'), '/');
                $middleware = $options['middleware'] ?? [];

                return Route::prefix($prefix)
                    ->middleware($middleware)
                    ->group(function () use ($within) {
                        Route::get('/subscribe', SubscriptionController::class)
                            ->name('dapr.subscribe');

                        if ($within) {
                            $within();
                        }
                    });
            });
        }
    }
}
