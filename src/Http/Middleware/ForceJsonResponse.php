<?php

declare(strict_types=1);

namespace Kapilsinghthakuri\RestKit\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Kapilsinghthakuri\RestKit\RestKit;
use Symfony\Component\HttpFoundation\Response;

class ForceJsonResponse
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (RestKit::config('force_json', false)) {
            if ($this->shouldForceJson($request)) {
                $request->headers->set('Accept', 'application/json', true);
            }
        }

        return $next($request);
    }

    /**
     * Determine if JSON should be forced for this request
     */
    protected function shouldForceJson(Request $request): bool
    {
        // Check if path starts with 'api'
        if (str_starts_with($request->path(), 'api')) {
            return true;
        }

        // Check if route has 'api' middleware
        $route = $request->route();
        if ($route && in_array('api', $route->middleware())) {
            return true;
        }

        return false;
    }
}
