<?php

return [
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
