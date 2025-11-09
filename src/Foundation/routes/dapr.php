<?php

use AlazziAz\LaravelDapr\Support\RouteMacros;
use Illuminate\Support\Facades\Route;

RouteMacros::register();

Route::daprSubscriptions();

if (config('dapr.health.enabled', true)) {
    Route::daprHealth();
}