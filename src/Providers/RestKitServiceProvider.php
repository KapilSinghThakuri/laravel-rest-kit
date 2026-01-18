<?php

declare(strict_types=1);

namespace Kapilsinghthakuri\RestKit\Providers;

use Illuminate\Contracts\Debug\ExceptionHandler as ExceptionHandlerContract;
use Illuminate\Support\ServiceProvider;
use Kapilsinghthakuri\RestKit\Contracts\ExceptionHandlerInterface;
use Kapilsinghthakuri\RestKit\Services\ExceptionHandlerService;
use Kapilsinghthakuri\RestKit\Services\JsonRenderingService;

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

        // Register global JSON rendering
        $this->registerJsonRendering();
    }

    /**
     * Register package services
     */
    protected function registerServices(): void
    {
        // Bind Exception Handler Service
        $this->app->singleton(ExceptionHandlerInterface::class, function ($app) {
            return new ExceptionHandlerService(
                $app->make(JsonRenderingService::class),
            );
        });
        // $this->app->singleton(ExceptionHandlerInterface::class, function ($app) {
        //     return new ExceptionHandlerService;
        // });

        // Bind JSON Rendering Service
        $this->app->singleton(JsonRenderingService::class, function ($app) {
            return new JsonRenderingService;
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

    /**
     * Register global JSON rendering logic
     */
    protected function registerJsonRendering(): void
    {
        // This integrates with Laravel 11's shouldRenderJsonWhen
        $this->app->resolving(ExceptionHandlerContract::class, function ($handler) {
            // Check if method exists (Laravel 11+)
            if (method_exists($handler, 'shouldRenderJsonWhen')) {
                $jsonRenderingService = $this->app->make(JsonRenderingService::class);

                $handler->shouldRenderJsonWhen(function ($request, $e) use ($jsonRenderingService) {
                    return $jsonRenderingService->shouldRenderJson($request, $e);
                });
            }
        });
    }
}
