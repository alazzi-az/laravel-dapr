<?php

namespace AlazziAz\LaravelDapr\Support;

use AlazziAz\LaravelDapr\Contracts\DaprHealthCheckerInterface;

class HealthCheckerInterface implements DaprHealthCheckerInterface
{
    public function isHealthy(): bool
    {
        return true;
    }

}