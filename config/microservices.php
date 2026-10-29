<?php

declare(strict_types=1);

return [
    /*
    |--------------------------------------------------------------------------
    | Gateway Dispatch Mode
    |--------------------------------------------------------------------------
    |
    | Supported modes:
    | - 'internal': Direct in-process execution (Zero network latency, ideal for dev).
    | - 'http': Dispatches requests to separate service ports / microservice URLs.
    |
    */
    'mode' => env('MICROSERVICE_GATEWAY_MODE', 'http'),

    /*
    |--------------------------------------------------------------------------
    | Internal Gateway Security Secret
    |--------------------------------------------------------------------------
    |
    | Prevents direct external access to microservice ports (8001-8006).
    | Only requests routed through the API Gateway (port 8000) carrying this
    | secret header will be accepted by individual microservices.
    |
    */
    'gateway_secret' => env('GATEWAY_INTERNAL_SECRET', 'microservice_internal_secret_key_987456321'),

    /*
    |--------------------------------------------------------------------------
    | Microservice Port & URL Registry
    |--------------------------------------------------------------------------
    |
    | Used by API Gateway when running in distributed / separate port mode.
    |
    */
    'services' => [
        'identity' => [
            'name' => 'Identity Microservice',
            'port' => (int) env('IDENTITY_SERVICE_PORT', 8001),
            'url' => env('IDENTITY_SERVICE_URL', 'http://127.0.0.1:8001'),
        ],
        'attendance' => [
            'name' => 'Attendance Microservice',
            'port' => (int) env('ATTENDANCE_SERVICE_PORT', 8002),
            'url' => env('ATTENDANCE_SERVICE_URL', 'http://127.0.0.1:8002'),
        ],
        'payroll' => [
            'name' => 'Payroll Microservice',
            'port' => (int) env('PAYROLL_SERVICE_PORT', 8003),
            'url' => env('PAYROLL_SERVICE_URL', 'http://127.0.0.1:8003'),
        ],
        'accounting' => [
            'name' => 'Accounting Microservice',
            'port' => (int) env('ACCOUNTING_SERVICE_PORT', 8004),
            'url' => env('ACCOUNTING_SERVICE_URL', 'http://127.0.0.1:8004'),
        ],
        'inventory' => [
            'name' => 'Inventory Microservice',
            'port' => (int) env('INVENTORY_SERVICE_PORT', 8005),
            'url' => env('INVENTORY_SERVICE_URL', 'http://127.0.0.1:8005'),
        ],
        'notification' => [
            'name' => 'Notification Microservice',
            'port' => (int) env('NOTIFICATION_SERVICE_PORT', 8006),
            'url' => env('NOTIFICATION_SERVICE_URL', 'http://127.0.0.1:8006'),
        ],
    ],
];
