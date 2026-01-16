<?php

declare(strict_types=1);

namespace Kapilsinghthakuri\RestKit\Http\Controllers;

use App\Http\Controllers\Controller;
use Kapilsinghthakuri\RestKit\Responses\ApiResponse;

class ApiController extends Controller
{
    protected function success($data = null, $message = 'Success')
    {
        return ApiResponse::success($data, $message);
    }

    protected function error($message = 'Error', $status = 500, $errors = null)
    {
        return ApiResponse::error($message, $status, $errors);
    }
}
