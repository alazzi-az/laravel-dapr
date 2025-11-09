<?php

namespace AlazziAz\LaravelDapr\Tests;

use AlazziAz\LaravelDapr\ServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

abstract class TestCase extends Orchestra
{
    protected function getPackageProviders($app)
    {
        return [
            ServiceProvider::class,
        ];
    }
}
