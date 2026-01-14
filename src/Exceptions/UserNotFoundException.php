<?php

namespace Kapilsinghthakuri\RestKit\Exceptions;

use Symfony\Component\HttpFoundation\Response;

class UserNotFoundException extends ApiException
{
    protected int $status = Response::HTTP_NOT_FOUND;
}