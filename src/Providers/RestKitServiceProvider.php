<?php

namespace Kapilsinghthakuri\RestKit\Providers;

use Illuminate\Support\ServiceProvider;
use Kapilsinghthakuri\RestKit\Responses\ApiResponse;
use Kapilsinghthakuri\RestKit\Exceptions\ApiException;
use Illuminate\Contracts\Debug\ExceptionHandler as ExceptionHandlerContract;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpFoundation\Response;

class RestKitServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../../config/rest-kit.php',
            'rest-kit'
        );
    }

    public function boot(): void
    {
        $this->publishes([
            __DIR__ . '/../../config/rest-kit.php' => config_path('rest-kit.php'),
        ], 'rest-kit-config');

        $this->app->resolving(ExceptionHandlerContract::class, function ($handler) {
            // ApiException -> ApiResponse
            $handler->renderable(function (ApiException $e, $request) {
                return $e->toResponse($request);
            });

            // ValidationException -> 422 formatted
            $handler->renderable(function (ValidationException $e, $request) {
                return ApiResponse::error('Validation failed', Response::HTTP_UNPROCESSABLE_ENTITY, $e->errors());
            });

            // AuthenticationException -> 401
            $handler->renderable(function (AuthenticationException $e, $request) {
                return ApiResponse::error('Unauthenticated', Response::HTTP_UNAUTHORIZED, null);
            });

            // ModelNotFound -> 404
            $handler->renderable(function (ModelNotFoundException $e, $request) {
                return ApiResponse::error('Resource not found', Response::HTTP_NOT_FOUND, null);
            });

        });
    }
}
