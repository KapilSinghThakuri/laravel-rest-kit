<?php

declare(strict_types=1);

namespace Kapilsinghthakuri\RestKit\Exceptions;

use Illuminate\Contracts\Support\Responsable;
use Illuminate\Http\JsonResponse;
use Kapilsinghthakuri\RestKit\Responses\ApiResponse;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class ApiException extends \RuntimeException implements Responsable
{
    protected int $status = Response::HTTP_INTERNAL_SERVER_ERROR;

    protected ?array $errors = null;

    public function __construct(string $message = '', ?int $status = null, ?array $errors = null, ?Throwable $previous = null)
    {
        parent::__construct($message, 0, $previous);
        if ($status !== null) {
            $this->status = $status;
        }
        $this->errors = $errors;
    }

    public function toResponse($request): JsonResponse
    {
        return ApiResponse::error($this->getMessage(), $this->status, $this->errors);
    }

    public function getStatus(): int
    {
        return $this->status;
    }

    public function getErrors(): ?array
    {
        return $this->errors;
    }
}
