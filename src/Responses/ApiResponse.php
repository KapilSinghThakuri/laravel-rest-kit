<?php

namespace Kapilsinghthakuri\RestKit\Responses;

use Illuminate\Http\JsonResponse;
use Kapilsinghthakuri\RestKit\RestKit;

class ApiResponse
{
    public static function success($data = null, $message = 'Success!', int $status = 200): JsonResponse
    {
        $response = [
            'status'  => 'success',
            'message' => $message,
            'data'    => $data,
        ];

        if (RestKit::config('debug', false)) {
            $response['debug'] = [
                'timestamp' => now(),
                'memory' => memory_get_usage()
            ];
        }

        return response()->json($response, $status);
    }

    public static function error($message = 'Error!', int $status = 500, $errors = null): JsonResponse
    {
        $response = [
            'status'  => 'error',
            'message' => $message,
            'errors'  => $errors,
        ];

        if (RestKit::config('debug', false)) {
            $response['debug'] = [
                'timestamp' => now(),
                'memory' => memory_get_usage()
            ];
        }

        return response()->json($response, $status);
    }
}
