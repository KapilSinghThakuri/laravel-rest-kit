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
    | JSON Rendering Configuration
    |--------------------------------------------------------------------------
    | Configure when exceptions should be rendered as JSON
    */
    'json_rendering' => [
        // API route prefixes
        'api_prefixes' => [
            'api',
            'api/v1',
            'api/v2',
        ],

        // Wildcard patterns
        'patterns' => [
            // Example: All routes starting with 'api'
            // 'api*',
            // 'admin/api*',
        ],

        // Custom conditions (callables)
        'conditions' => [
            // Example: Force JSON if special header present
            // fn($request) => $request->hasHeader('X-Api-Request'),
        ],

        // Force JSON for AJAX requests
        'force_ajax' => true,
    ],

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
