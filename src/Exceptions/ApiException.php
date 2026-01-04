<?php

namespace Kapilsinghthakuri\RestKit\Exceptions;

use Exception;
use Kapilsinghthakuri\RestKit\Responses\ApiResponse;
use \Illuminate\Http\JsonResponse;
class ApiException extends Exception
{
    public function render(): JsonResponse
    {
        return ApiResponse::error($this->getMessage(), $this->getCode() ?: 400);
    }
}
