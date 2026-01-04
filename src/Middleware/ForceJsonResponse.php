<?php

namespace Kapilsinghthakuri\RestKit\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Kapilsinghthakuri\RestKit\RestKit;

class ForceJsonResponse
{
    public function handle(Request $request, Closure $next)
    {
        if (RestKit::config('force_json', true)) {
            $request->headers->set('Accept', 'application/json');
        }

        return $next($request);
    }
}
