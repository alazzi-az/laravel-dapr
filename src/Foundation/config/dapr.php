<?php

return [
    'pubsub' => [
        'name' => env('DAPR_PUBSUB', 'pubsub'),
    ],
    'topics' => [
        // Register custom topic overrides: Event::class => 'custom.topic',
    ],
    'http' => [
        'prefix' => 'dapr',
        'verify_signature' => false,
        'signature_header' => 'x-dapr-signature',
        'signature_secret' => env('DAPR_INGRESS_SECRET'),
    ],
    'serialization' => [
        'wrap_cloudevent' => true,
    ],
    'publish_local_events' => true,
    'listener' => [
        'concurrency' => 1,
        'middleware' => [
            // \AlazziAz\LaravelDaprListener\Middleware\RetryOnceMiddleware::class,
        ],
    ],
    'publisher' => [
        'middleware' => [
            // \AlazziAz\LaravelDaprPublisher\Middleware\AddCorrelationId::class,
        ],
    ],
    'health' => [
        'enabled' => env('DAPR_HEALTH_ENABLED', true),
        'middleware' => [], // optional
        // return type: 'empty' or 'json'
        'response' => env('DAPR_HEALTH_RESPONSE', 'empty'),
        // a callable class you can override for custom checks (optional)
        'checker'  => env('DAPR_HEALTH_CHECKER', null), /**  \AlazziAz\LaravelDapr\Support\HealthCheckerInterface::class  */
    ],
    'invocation' => [
        'prefix' => 'dapr/invoke',

        'auto_register' => false,

        'middleware' => [
            // \App\Http\Middleware\Authenticate::class,
        ],

        'verify_signature' => false,
        'signature_header' => 'x-dapr-signature',
        'signature_secret' => env('DAPR_INVOKE_SECRET'),

        'map' => [
            // 'service.method' => App\Http\Controllers\InvokeTargetController::class,
            // 'orders.create' => [App\Http\Controllers\OrderController::class, 'createViaInvoke'],
        ],
    ]
];
