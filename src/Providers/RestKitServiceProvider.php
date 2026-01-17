<?php

declare(strict_types=1);

namespace Kapilsinghthakuri\RestKit\Providers;

use Throwable;
use PDOException;
use Illuminate\Http\JsonResponse;
use Illuminate\Database\QueryException;
use Illuminate\Support\ServiceProvider;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Auth\Access\AuthorizationException;
use Kapilsinghthakuri\RestKit\Responses\ApiResponse;
use Kapilsinghthakuri\RestKit\Exceptions\ApiException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Exceptions\ThrottleRequestsException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Illuminate\Contracts\Debug\ExceptionHandler as ExceptionHandlerContract;

class RestKitServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../../config/rest-kit.php',
            'rest-kit',
        );
    }

    public function boot(): void
    {
        $this->publishes([
            __DIR__ . '/../../config/rest-kit.php' => config_path('rest-kit.php'),
        ], 'rest-kit-config');

        $this->app->resolving(ExceptionHandlerContract::class, function ($handler) {
            // ApiException -> ApiResponse
            $handler->renderable(function (ApiException $e, $request): JsonResponse|Response {
                return $e->toResponse($request);
            });

            // ValidationException -> 422 formatted
            $handler->renderable(function (ValidationException $e, $request): JsonResponse {
                return ApiResponse::error('Validation failed', Response::HTTP_UNPROCESSABLE_ENTITY, $e->errors(),);
            });

            // AuthenticationException -> 401
            $handler->renderable(function (AuthenticationException $e, $request): JsonResponse {
                return ApiResponse::error($e->getMessage() ?: 'Unauthenticated', Response::HTTP_UNAUTHORIZED, null);
            });

            // AuthorizationException -> 403
            $handler->renderable(function (AuthorizationException $e, $request): JsonResponse {
                return ApiResponse::error(
                    $e->getMessage() ?: 'This action is forbidden',
                    Response::HTTP_FORBIDDEN,
                );
            });

            // ModelNotFound -> 404
            $handler->renderable(function (ModelNotFoundException $e, $request): JsonResponse {
                return ApiResponse::error('Resource not found', Response::HTTP_NOT_FOUND, null,);
            });


            $handler->renderable(function (NotFoundHttpException $e, $request): JsonResponse {
                return ApiResponse::error(
                    'API endpoint not found',
                    Response::HTTP_NOT_FOUND,
                );
            });

            $handler->renderable(function (MethodNotAllowedHttpException $e, $request): JsonResponse {
                return ApiResponse::error(
                    'HTTP method not allowed',
                    Response::HTTP_METHOD_NOT_ALLOWED,
                );
            });

            $handler->renderable(function (ThrottleRequestsException $e, $request): JsonResponse {
                return ApiResponse::error(
                    'Too many requests. Please try again later.',
                    Response::HTTP_TOO_MANY_REQUESTS,
                    null,
                    $e
                );
            });

            $handler->renderable(function (HttpExceptionInterface $e, $request): JsonResponse {
                return ApiResponse::error(
                    $e->getMessage() ?: 'HTTP error',
                    $e->getStatusCode(),
                    null,
                    $e
                );
            });

            $handler->renderable(function (QueryException $e, $request): JsonResponse {
                $sqlState = $e->errorInfo[0] ?? null;

                return match ($sqlState) {
                    '23505', '23000' => ApiResponse::error(
                        'Duplicate resource',
                         Response::HTTP_CONFLICT, 
                         null, 
                         ($e instanceof Throwable) ? $e : null
                         ),
                    '23503' => ApiResponse::error(
                        'Resource conflict', 
                        Response::HTTP_CONFLICT, 
                        null, 
                        ($e instanceof Throwable) ? $e : null
                        ),
                    '23502' => ApiResponse::error(
                        'Missing required field', 
                        Response::HTTP_UNPROCESSABLE_ENTITY, 
                        null, 
                        ($e instanceof Throwable) ? $e : null
                        ),
                    default => ApiResponse::error(
                        'Database error', 
                        Response::HTTP_INTERNAL_SERVER_ERROR, 
                        null,     
                        ($e instanceof Throwable) ? $e : null
                        ),
                };
            });

            $handler->renderable(function (PDOException $e, $request): JsonResponse {
                return ApiResponse::error('Database error', 500, null, $e);
            });

            $handler->renderable(function (Throwable $e, $request): JsonResponse {
                return ApiResponse::error(
                    'Internal server error',
                    Response::HTTP_INTERNAL_SERVER_ERROR,
                    null, 
                    $e
                );
            });
        });
    }
}
