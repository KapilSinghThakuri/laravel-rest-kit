<?php

declare(strict_types=1);

namespace Kapilsinghthakuri\RestKit\Services;

use Closure;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Http\Exceptions\ThrottleRequestsException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Kapilsinghthakuri\RestKit\Contracts\ExceptionHandlerInterface;
use Kapilsinghthakuri\RestKit\Exceptions\ApiException;
use Kapilsinghthakuri\RestKit\Responses\ApiResponse;
use PDOException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

class ExceptionHandlerService implements ExceptionHandlerInterface
{
    protected JsonRenderingService $jsonRenderingService;

    public function __construct(JsonRenderingService $jsonRenderingService)
    {
        $this->jsonRenderingService = $jsonRenderingService;
    }

    /**
     * Create a gated handler - wraps handler with JSON check
     *
     * This is the decorator that adds gate logic to any handler
     */
    protected function gatedHandler(Closure $handler): Closure
    {
        return function (Throwable $e, Request $request) use ($handler) {
            if (! $this->jsonRenderingService->shouldRenderJson($request, $e)) {
                return null;
            }

            return $handler($e, $request);
        };
    }

    /**
     * Register all exception handlers with automatic gating
     */
    public function register($handler): void
    {
        // API Exception Handler
        $handler->renderable(
            $this->gatedHandler(
                fn (ApiException $e, Request $req) => $this->handleApiException($e, $req),
            ),
        );

        // Validation Exception Handler
        $handler->renderable(
            $this->gatedHandler(
                fn (ValidationException $e, Request $req) => $this->handleValidationException($e, $req),
            ),
        );

        // Authentication Exception Handler
        $handler->renderable(
            $this->gatedHandler(
                fn (AuthenticationException $e, Request $req) => $this->handleAuthenticationException($e, $req),
            ),
        );

        // Authorization Exception Handler
        $handler->renderable(
            $this->gatedHandler(
                fn (AuthorizationException $e, Request $req) => $this->handleAuthorizationException($e, $req),
            ),
        );

        // Model Not Found Exception Handler
        $handler->renderable(
            $this->gatedHandler(
                fn (ModelNotFoundException $e, Request $req) => $this->handleModelNotFoundException($e, $req),
            ),
        );

        // Not Found HTTP Exception Handler
        $handler->renderable(
            $this->gatedHandler(
                fn (NotFoundHttpException $e, Request $req) => $this->handleNotFoundHttpException($e, $req),
            ),
        );

        // Method Not Allowed Exception Handler
        $handler->renderable(
            $this->gatedHandler(
                fn (MethodNotAllowedHttpException $e, Request $req) => $this->handleMethodNotAllowedException($e, $req),
            ),
        );

        // Throttle Requests Exception Handler
        $handler->renderable(
            $this->gatedHandler(
                fn (ThrottleRequestsException $e, Request $req) => $this->handleThrottleRequestsException($e, $req),
            ),
        );

        // HTTP Exception Handler
        $handler->renderable(
            $this->gatedHandler(
                fn (HttpExceptionInterface $e, Request $req) => $this->handleHttpException($e, $req),
            ),
        );

        // Query Exception Handler
        $handler->renderable(
            $this->gatedHandler(
                fn (QueryException $e, Request $req) => $this->handleQueryException($e, $req),
            ),
        );

        // PDO Exception Handler
        $handler->renderable(
            $this->gatedHandler(
                fn (PDOException $e, Request $req) => $this->handlePdoException($e, $req),
            ),
        );

        // Generic Exception Handler (catch-all)
        $handler->renderable(
            $this->gatedHandler(
                fn (Throwable $e, Request $req) => $this->handleGenericException($e, $req),
            ),
        );
    }

    /**
     * Handle API exceptions
     */
    public function handleApiException(ApiException $e, $request): JsonResponse|Response
    {
        return $e->toResponse($request);
    }

    /**
     * Handle validation exceptions
     */
    public function handleValidationException(ValidationException $e, $request): JsonResponse
    {
        return ApiResponse::error(
            'Validation failed',
            Response::HTTP_UNPROCESSABLE_ENTITY,
            $e->errors(),
        );
    }

    /**
     * Handle authentication exceptions
     */
    public function handleAuthenticationException(AuthenticationException $e, $request): JsonResponse
    {
        $message = $e->getMessage() ?: 'Unauthenticated';

        return ApiResponse::error(
            $message,
            Response::HTTP_UNAUTHORIZED,
        );
    }

    /**
     * Handle authorization exceptions
     */
    public function handleAuthorizationException(AuthorizationException $e, $request): JsonResponse
    {
        $message = $e->getMessage() ?: 'This action is forbidden';

        return ApiResponse::error(
            $message,
            Response::HTTP_FORBIDDEN,
        );
    }

    /**
     * Handle model not found exceptions
     */
    public function handleModelNotFoundException(ModelNotFoundException $e, $request): JsonResponse
    {
        return ApiResponse::error(
            'Resource not found',
            Response::HTTP_NOT_FOUND,
        );
    }

    /**
     * Handle not found HTTP exceptions
     */
    public function handleNotFoundHttpException(NotFoundHttpException $e, $request): JsonResponse
    {
        return ApiResponse::error(
            'API endpoint not found',
            Response::HTTP_NOT_FOUND,
        );
    }

    /**
     * Handle method not allowed exceptions
     */
    public function handleMethodNotAllowedException(MethodNotAllowedHttpException $e, $request): JsonResponse
    {
        return ApiResponse::error(
            'HTTP method not allowed',
            Response::HTTP_METHOD_NOT_ALLOWED,
        );
    }

    /**
     * Handle throttle requests exceptions
     */
    public function handleThrottleRequestsException(ThrottleRequestsException $e, $request): JsonResponse
    {
        return ApiResponse::error(
            'Too many requests. Please try again later.',
            Response::HTTP_TOO_MANY_REQUESTS,
            null,
            $e,
        );
    }

    /**
     * Handle HTTP exceptions
     */
    public function handleHttpException(HttpExceptionInterface $e, $request): JsonResponse
    {
        $message = $e->getMessage() ?: 'HTTP error';

        return ApiResponse::error(
            $message,
            $e->getStatusCode(),
            null,
            $e,
        );
    }

    /**
     * Handle query exceptions
     */
    public function handleQueryException(QueryException $e, $request): JsonResponse
    {
        $sqlState = $e->errorInfo[0] ?? null;

        return match ($sqlState) {
            '23505', '23000' => $this->duplicateResourceError($e),
            '23503' => $this->resourceConflictError($e),
            '23502' => $this->missingFieldError($e),
            default => $this->databaseError($e),
        };
    }

    /**
     * Handle PDO exceptions
     */
    public function handlePdoException(PDOException $e, $request): JsonResponse
    {
        return ApiResponse::error(
            'Database connection error',
            Response::HTTP_INTERNAL_SERVER_ERROR,
            null,
            $e,
        );
    }

    /**
     * Handle generic throwable exceptions
     */
    public function handleGenericException(Throwable $e, $request): JsonResponse
    {
        return ApiResponse::error(
            'Internal server error',
            Response::HTTP_INTERNAL_SERVER_ERROR,
            null,
            $e,
        );
    }

    /**
     * Duplicate resource error response
     */
    protected function duplicateResourceError(QueryException $e): JsonResponse
    {
        return ApiResponse::error(
            'Duplicate resource',
            Response::HTTP_CONFLICT,
            null,
            ($e instanceof Throwable ? $e : null),
        );
    }

    /**
     * Resource conflict error response
     */
    protected function resourceConflictError(QueryException $e): JsonResponse
    {
        return ApiResponse::error(
            'Resource conflict',
            Response::HTTP_CONFLICT,
            null,
            ($e instanceof Throwable ? $e : null),
        );
    }

    /**
     * Missing field error response
     */
    protected function missingFieldError(QueryException $e): JsonResponse
    {
        return ApiResponse::error(
            'Missing required field',
            Response::HTTP_UNPROCESSABLE_ENTITY,
            null,
            ($e instanceof Throwable ? $e : null),
        );
    }

    /**
     * Database error response
     */
    protected function databaseError(QueryException $e): JsonResponse
    {
        return ApiResponse::error(
            'Database error',
            Response::HTTP_INTERNAL_SERVER_ERROR,
            null,
            ($e instanceof Throwable ? $e : null),
        );
    }

    /**
     * Register all exception handlers
     */
    // public function register($handler): void
    // {
    //     // API Exception
    //     $handler->renderable(
    //         fn (ApiException $e, $request) => $this->handleApiException($e, $request),
    //     );

    //     // Validation Exception
    //     $handler->renderable(
    //         fn (ValidationException $e, $request) => $this->handleValidationException($e, $request),
    //     );

    //     // Authentication Exception
    //     $handler->renderable(
    //         fn (AuthenticationException $e, $request) => $this->handleAuthenticationException($e, $request),
    //     );

    //     // Authorization Exception
    //     $handler->renderable(
    //         fn (AuthorizationException $e, $request) => $this->handleAuthorizationException($e, $request),
    //     );

    //     // Model Not Found Exception
    //     $handler->renderable(
    //         fn (ModelNotFoundException $e, $request) => $this->handleModelNotFoundException($e, $request),
    //     );

    //     // Not Found HTTP Exception
    //     $handler->renderable(
    //         fn (NotFoundHttpException $e, $request) => $this->handleNotFoundHttpException($e, $request),
    //     );

    //     // Method Not Allowed Exception
    //     $handler->renderable(
    //         fn (MethodNotAllowedHttpException $e, $request) => $this->handleMethodNotAllowedException($e, $request),
    //     );

    //     // Throttle Requests Exception
    //     $handler->renderable(
    //         fn (ThrottleRequestsException $e, $request) => $this->handleThrottleRequestsException($e, $request),
    //     );

    //     // HTTP Exception
    //     $handler->renderable(
    //         fn (HttpExceptionInterface $e, $request) => $this->handleHttpException($e, $request),
    //     );

    //     // Query Exception
    //     $handler->renderable(
    //         fn (QueryException $e, $request) => $this->handleQueryException($e, $request),
    //     );

    //     // PDO Exception
    //     $handler->renderable(
    //         fn (PDOException $e, $request) => $this->handlePdoException($e, $request),
    //     );

    //     // Generic Exception (catch-all)
    //     $handler->renderable(
    //         fn (Throwable $e, $request) => $this->handleGenericException($e, $request),
    //     );
    // }
}
