<?php

declare(strict_types=1);

namespace Kapilsinghthakuri\RestKit\Providers;

use Illuminate\Contracts\Debug\ExceptionHandler as ExceptionHandlerContract;
use Illuminate\Support\ServiceProvider;
use Kapilsinghthakuri\RestKit\Contracts\ExceptionHandlerInterface;
use Kapilsinghthakuri\RestKit\Services\ExceptionHandlerService;

class RestKitServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../../config/rest-kit.php',
            'rest-kit',
        );

        // Register services
        $this->registerServices();
    }

    public function boot(): void
    {
        $this->publishes([
            __DIR__ . '/../../config/rest-kit.php' => config_path('rest-kit.php'),
        ], 'rest-kit-config');

        if ($this->app->runningInConsole()) {
            $this->reloads('rest-kit:reload');
        }

        // Register exception handlers
        $this->registerExceptionHandlers();
    }

    /**
     * Register package services
     */
    protected function registerServices(): void
    {
        // Bind Exception Handler Service
        $this->app->singleton(ExceptionHandlerInterface::class, function ($app) {
            return new ExceptionHandlerService;
        });
    }

    /**
     * Register exception handlers
     */
    protected function registerExceptionHandlers(): void
    {
        $this->app->resolving(ExceptionHandlerContract::class, function ($handler) {
            $exceptionHandler = $this->app->make(ExceptionHandlerInterface::class);
            $exceptionHandler->register($handler);
        });
    }
}
