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
    'client' => [
        'timeout' => env('DAPR_CLIENT_TIMEOUT', 10),
        'connect_timeout' => env('DAPR_CLIENT_CONNECT_TIMEOUT', 10)
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
        'discovery' => [
            'enabled' => true,

            // enable/disable each discovery channel
            // so this will register events topics to dapr subscriptions so the event will be dispatched in this service also
            'events' => [
                'enabled' => false,
                'directories' => [
                    app_path('Events'),
                    // base_path('modules/*/Events'),
                ],
            ],

            'listeners' => [
                'enabled' => true,
                'directories' => [
                    app_path('Listeners'),
                    // base_path('modules/*/Listeners'),
                ],
            ],
        ]
    ],
    'publisher' => [
        'middleware' => [
            // \AlazziAz\LaravelDaprPublisher\Middleware\AddCorrelationId::class,
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
    'health' => [
        'enabled' => env('DAPR_HEALTH_ENABLED', true),
        'middleware' => [], // optional
        // return type: 'empty' or 'json'
        'response' => env('DAPR_HEALTH_RESPONSE', 'empty'),
        // a callable class you can override for custom checks (optional)
        'checker' => env('DAPR_HEALTH_CHECKER', null),/**  \AlazziAz\LaravelDapr\Support\HealthCheckerInterface::class  */
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
    ],
    'invoker' => [
        'middleware' => [
            // \App\Dapr\Invoker\Middleware\AddJwtAuthorization::class,
        ],
    ],
];
