<?php

return [
    'publisher' => [
        'middleware' => [
            \AlazziAz\LaravelDaprPublisher\Middleware\AddCorrelationId::class,
            \AlazziAz\LaravelDaprPublisher\Middleware\AddTenantContext::class,
            \AlazziAz\LaravelDaprPublisher\Middleware\AddTimestamp::class,
        ],
        'serialization' => [
            'wrap_cloudevent' => env('DAPR_WRAP_CLOUDEVENT', true),
            'default_content_type' => 'application/json', // do not change this unless you know that this for custom cloud event
        ],
        'cloudevents' => [
            'source' => env('APP_URL', 'laravel-service'),
            'specversion' => '1.0',
            'type_strategy' => 'class', // class|alias
            'id_strategy' => 'ulid', // ulid|uuid
        ],
    ],
];
