<?php

namespace AlazziAz\LaravelDaprPublisher\Middleware;

use AlazziAz\LaravelDaprPublisher\Publishing\EventContext;
use Closure;

interface PublisherMiddleware
{
    public function handle(EventContext $context, Closure $next): mixed;
}
