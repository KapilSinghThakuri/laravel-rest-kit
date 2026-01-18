<?php

declare(strict_types=1);

namespace Kapilsinghthakuri\RestKit\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Kapilsinghthakuri\RestKit\Responses\ApiResponse;
use Symfony\Component\HttpFoundation\Response;

class ApiController extends Controller
{
    protected function success(
        $data = null,
        $message = 'Success',
        $status = Response::HTTP_OK,
    ): JsonResponse {
        return ApiResponse::success(
            $data,
            $message,
            $status,
        );
    }

    protected function error(
        $message = 'Error',
        $status = Response::HTTP_INTERNAL_SERVER_ERROR,
        $errors = null,
    ): JsonResponse {
        return ApiResponse::error(
            $message,
            $status,
            $errors,
        );
    }
}
