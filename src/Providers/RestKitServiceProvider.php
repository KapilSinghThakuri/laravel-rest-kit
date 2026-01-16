<?php

declare(strict_types=1);

namespace Kapilsinghthakuri\RestKit\Providers;

use Illuminate\Support\ServiceProvider;
use Kapilsinghthakuri\RestKit\Responses\ApiResponse;
use Kapilsinghthakuri\RestKit\Exceptions\ApiException;
use Illuminate\Contracts\Debug\ExceptionHandler as ExceptionHandlerContract;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Auth\Access\AuthorizationException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Illuminate\Http\Exceptions\ThrottleRequestsException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Throwable;

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
                return ApiResponse::error($e->getMessage() ?: 'Unauthenticated', Response::HTTP_UNAUTHORIZED, null);
            });

            // AuthorizationException -> 403
            $handler->renderable(function (AuthorizationException $e, $request) {
                return ApiResponse::error(
                    $e->getMessage() ?: 'This action is forbidden',
                    Response::HTTP_FORBIDDEN
                );
            });

            // ModelNotFound -> 404
            $handler->renderable(function (ModelNotFoundException $e, $request) {
                return ApiResponse::error('Resource not found', Response::HTTP_NOT_FOUND, null);
            });


            $handler->renderable(function (NotFoundHttpException $e, $request) {
                return ApiResponse::error(
                    'API endpoint not found',
                    Response::HTTP_NOT_FOUND
                );
            });

            $handler->renderable(function (MethodNotAllowedHttpException $e, $request) {
                return ApiResponse::error(
                    'HTTP method not allowed',
                    Response::HTTP_METHOD_NOT_ALLOWED
                );
            });

            $handler->renderable(function (ThrottleRequestsException $e, $request) {
                return ApiResponse::error(
                    'Too many requests. Please try again later.',
                    Response::HTTP_TOO_MANY_REQUESTS
                );
            });

            $handler->renderable(function (HttpExceptionInterface $e, $request) {
                return ApiResponse::error(
                    $e->getMessage() ?: 'HTTP error',
                    $e->getStatusCode()
                );
            });

            $handler->renderable(function (Throwable $e, $request) {
                return ApiResponse::error(
                    'Internal server error',
                    Response::HTTP_INTERNAL_SERVER_ERROR
                );
            });
        });
    }
}
