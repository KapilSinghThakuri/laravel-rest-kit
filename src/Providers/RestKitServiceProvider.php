<?php

namespace Kapilsinghthakuri\RestKit\Providers;

use Illuminate\Support\ServiceProvider;


class RestKitServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../config/rest-kit.php',
            'rest-kit'
        );
    }

    public function boot(): void
    {
        $this->publishes([
            __DIR__ . '/../config/rest-kit.php' => config_path('rest-kit.php'),
        ], 'rest-kit-config');
    }
}
