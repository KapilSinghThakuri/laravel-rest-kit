<?php

declare(strict_types=1);

return [
    /*
    |--------------------------------------------------------------------------
    | Keys Mapping
    |--------------------------------------------------------------------------
    | Configure top-level key names used in success and error responses.
    */
    'keys' => [
        'success' => [
            'root' => 'success',
            'message' => 'message',
            'code' => 'status',
            'data' => 'data',
            'meta' => 'meta',
        ],
        'error' => [
            'root' => 'success',
            'message' => 'message',
            'code' => 'status',
            'errors' => 'errors',
            'meta' => 'meta',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Force JSON Response
    |--------------------------------------------------------------------------
    | When enabled, automatically sets Accept header to application/json
    | for all API routes.
    */
    'force_json' => true,

    /*
    |--------------------------------------------------------------------------
    | Debug Mode
    |--------------------------------------------------------------------------
    | When enabled, includes additional debug information in error responses.
    */
    'debug' => env('APP_DEBUG', false),

    /*
    |--------------------------------------------------------------------------
    | Pagination
    |--------------------------------------------------------------------------
    | Default pagination settings for API responses.
    */
    'pagination' => [
        'default_per_page' => 15,
        'max_per_page' => 100,
    ],

    /*
    |--------------------------------------------------------------------------
    | API Versioning
    |--------------------------------------------------------------------------
    | Configure API versioning settings.
    */
    'versioning' => [
        'enabled' => true,
        'default' => 'v1',
        'header' => 'X-API-Version',
    ],
];
