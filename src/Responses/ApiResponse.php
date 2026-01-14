<?php

namespace Kapilsinghthakuri\RestKit\Responses;

use Illuminate\Http\JsonResponse;
use Kapilsinghthakuri\RestKit\RestKit;
use Symfony\Component\HttpFoundation\Response;

class ApiResponse
{
    public static function success(mixed $data = null, string $message = 'Success', int $status = Response::HTTP_OK, array $meta = [], array $headers = []): JsonResponse
    {
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

    public static function error(string $message = 'Error', int $status = Response::HTTP_INTERNAL_SERVER_ERROR, ?array $errors = null, array $meta = [], array $headers = []): JsonResponse
    {
        $keys = RestKit::config('keys.error');
        $response = [
            $keys['root'] => false,
            $keys['message'] => $message,
            $keys['code'] => $status,
            $keys['errors'] => $errors,
            $keys['meta'] => $meta,
        ];

        if (RestKit::config('debug', false)) {
            $response['debug'] = [
                'timestamp' => now(),
                'memory' => memory_get_usage()
            ];
        }

        if (RestKit::config('include_trace', false) && app()->isLocal()) {
            $response['trace'] = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
        }
        
        return response()->json($response, $status, $headers);
    }
}
