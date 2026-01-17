<?php

declare(strict_types=1);

return [
    /*
    |--------------------------------------------------------------------------
    | Keys mapping
    |--------------------------------------------------------------------------
    | Configure top-level key names used in success and error responses.
    | This lets consumers adjust to their preferred conventions.
    */
    "keys" => [
        'success' => [
            'root'    => 'success',
            'message' => 'message',
            'code'    => 'status',
            'data'    => 'data',
            'meta'    => 'meta',
        ],
         'error' => [
            'root'    => 'success',
            'message' => 'message',
            'code'    => 'status',
            'errors'  => 'errors',
            'meta'    => 'meta',
        ],
    ],

    'force_json' => true,

    'debug' => env('APP_DEBUG', false),

    'pagination' => [
        'default_per_page' => 15,
        'max_per_page' => 100,
    ],

    'versioning' => [
        'enabled' => true,
        'default' => 'v1',
        'header' => 'X-API-Version',
    ],
];
