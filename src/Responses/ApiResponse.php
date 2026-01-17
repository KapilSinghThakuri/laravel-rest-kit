<?php

declare(strict_types=1);

namespace Kapilsinghthakuri\RestKit\Responses;

use Illuminate\Http\JsonResponse;
use Kapilsinghthakuri\RestKit\RestKit;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class ApiResponse
{
    public static function success(
        mixed $data = null,
        string $message = 'Success',
        int $status = Response::HTTP_OK,
        array $meta = [],
        array $headers = []
    ): JsonResponse {
        $keys = RestKit::config('keys.success');
        $response = [
            $keys['root'] => true,
            $keys['message'] => $message,
            $keys['code'] => $status,
            $keys['data'] => $data,
            $keys['meta'] => $meta,
        ];

        if (RestKit::config('debug', false)) {
            $response['debug'] = [
                'timestamp' => now(),
                'memory' => memory_get_usage()
            ];
        }

        return response()->json($response, $status, $headers);
    }

    public static function error(
        string $message = 'Error',
        int $status = Response::HTTP_INTERNAL_SERVER_ERROR,
        ?array $errors = null,
        ?Throwable $exception = null,
        array $meta = [],
        array $headers = []
    ): JsonResponse {
        $keys = RestKit::config('keys.error');
        $response = [
            $keys['root'] => false,
            $keys['message'] => $message,
            $keys['code'] => $status,
            $keys['errors'] => $errors,
            $keys['meta'] => $meta,
        ];

        $isDebugMode = RestKit::config('debug', false);

        if ($isDebugMode) {
            $response['debug'] = self::buildDebugData($exception);
        }

        return response()->json($response, $status, $headers);
    }

    private static function buildDebugData(?Throwable $exception): array
    {
        $debugData = [
            'timestamp' => now(),
            'memory' => memory_get_usage(),
        ];

        if ($exception) {
            $debugData['exception'] = self::formatException($exception);
        }

        if (app()->isLocal()) {
            $debugData['trace'] = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
        }

        return $debugData;
    }

    private static function formatException(Throwable $exception): array
    {
        return [
            'type' => get_class($exception),
            'message' => $exception->getMessage(),
            'code' => $exception->getCode(),
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
        ];
    }
}
