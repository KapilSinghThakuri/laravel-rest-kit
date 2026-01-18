<?php

// src/Services/JsonRenderingService.php

declare(strict_types=1);

namespace Kapilsinghthakuri\RestKit\Services;

use Illuminate\Http\Request;
use Kapilsinghthakuri\RestKit\RestKit;
use Throwable;

class JsonRenderingService
{
    /**
     * Determine if the request should render JSON
     *
     * This is the CENTRAL DECISION POINT for all exception handling
     */
    public function shouldRenderJson(Request $request, Throwable $e): bool
    {
        // Priority 1: Check if force_json is disabled
        $forceJson = RestKit::config('force_json', true);
        if (! $forceJson) {
            // Only render JSON if client explicitly expects it
            return $request->expectsJson();
        }

        // Priority 2: Always render JSON for API routes
        if ($this->isApiRoute($request)) {
            return true;
        }

        // Priority 3: Check custom conditions from config
        $conditions = RestKit::config('json_rendering.conditions', []);
        foreach ($conditions as $condition) {
            if ($this->evaluateCondition($request, $condition)) {
                return true;
            }
        }

        // Priority 4: Check Accept header
        if ($request->expectsJson()) {
            return true;
        }

        // Priority 5: Check for AJAX requests (if enabled in config)
        $forceAJAX = RestKit::config('json_rendering.force_ajax', true);
        if ($forceAJAX && $request->ajax()) {
            return true;
        }

        // Default: Don't force JSON, let Laravel handle normally
        return false;
    }

    /**
     * Check if current route is an API route
     */
    protected function isApiRoute(Request $request): bool
    {
        $apiPrefixes = RestKit::config('json_rendering.api_prefixes', ['api']);

        foreach ($apiPrefixes as $prefix) {
            // Remove leading/trailing slashes for consistent matching
            $prefix = trim($prefix, '/');
            $path = trim($request->path(), '/');

            // Check exact match or prefix match
            if ($path === $prefix || str_starts_with($path, $prefix . '/')) {
                return true;
            }
        }

        // Check patterns
        $patterns = RestKit::config('json_rendering.patterns', []);
        foreach ($patterns as $pattern) {
            if ($request->is($pattern)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Evaluate a custom condition
     */
    protected function evaluateCondition(Request $request, string|callable $condition): bool
    {
        if (is_callable($condition)) {
            return (bool) $condition($request);
        }

        if (is_string($condition)) {
            return $request->is($condition);
        }

        return false;
    }
}
