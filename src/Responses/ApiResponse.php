<?php

namespace Kapilsinghthakuri\RestKit\Responses;

use Illuminate\Http\JsonResponse;

class ApiResponse
{
    public static function success($data = null, $message = 'Success!', int $status = 200): JsonResponse
    {
        return new JsonResponse([
            'status'  => 'success',
            'message' => $message,
            'data'    => $data,
        ], $status);
    }

    public static function error($message = 'Error!', int $status = 500, $errors = null): JsonResponse
    {
        return new JsonResponse([
            'status'  => 'error',
            'message' => $message,
            'errors'  => $errors,
        ], $status);
    }
}
