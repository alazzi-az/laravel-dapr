<?php

return [
    'publisher' => [
        'middleware' => [
            \AlazziAz\LaravelDaprPublisher\Middleware\AddCorrelationId::class,
            \AlazziAz\LaravelDaprPublisher\Middleware\AddTenantContext::class,
            \AlazziAz\LaravelDaprPublisher\Middleware\AddTimestamp::class,
        ],
    ],
];
