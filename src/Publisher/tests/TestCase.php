<?php

namespace AlazziAz\LaravelDaprPublisher\Tests;

use AlazziAz\LaravelDapr\ServiceProvider as BaseProvider;
use AlazziAz\LaravelDaprPublisher\ServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

abstract class TestCase extends Orchestra
{
    protected function getPackageProviders($app)
    {
        return [
            BaseProvider::class,
            ServiceProvider::class,
        ];
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        \Mockery::close();
    }
}
