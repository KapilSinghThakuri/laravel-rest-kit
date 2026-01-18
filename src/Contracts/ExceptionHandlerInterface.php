<?php

declare(strict_types=1);

namespace Kapilsinghthakuri\RestKit\Contracts;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Http\Exceptions\ThrottleRequestsException;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;
use Kapilsinghthakuri\RestKit\Exceptions\ApiException;
use PDOException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

interface ExceptionHandlerInterface
{
    public function handleApiException(ApiException $e, $request): JsonResponse|Response;

    public function handleValidationException(ValidationException $e, $request): JsonResponse;

    public function handleAuthenticationException(AuthenticationException $e, $request): JsonResponse;

    public function handleAuthorizationException(AuthorizationException $e, $request): JsonResponse;

    public function handleModelNotFoundException(ModelNotFoundException $e, $request): JsonResponse;

    public function handleNotFoundHttpException(NotFoundHttpException $e, $request): JsonResponse;

    public function handleMethodNotAllowedException(MethodNotAllowedHttpException $e, $request): JsonResponse;

    public function handleThrottleRequestsException(ThrottleRequestsException $e, $request): JsonResponse;

    public function handleHttpException(HttpExceptionInterface $e, $request): JsonResponse;

    public function handleQueryException(QueryException $e, $request): JsonResponse;

    public function handlePdoException(PDOException $e, $request): JsonResponse;

    public function handleGenericException(Throwable $e, $request): JsonResponse;

    public function register($handler): void;
}
