<?php

namespace AlazziAz\LaravelDapr\Contracts;

interface DaprHealthCheckerInterface
{
    public function isHealthy(): bool;

}