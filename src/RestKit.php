<?php

declare(strict_types=1);

namespace Kapilsinghthakuri\RestKit;

class RestKit
{
    public static function config(string $key, mixed $default = null): mixed
    {
        return config("rest-kit.$key", $default);
    }
}
