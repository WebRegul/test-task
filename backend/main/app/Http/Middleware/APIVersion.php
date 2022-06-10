<?php

namespace App\Http\Middleware;

use Closure;

/**
 * Class APIVersion
 * @package App\Http\Middleware
 */
class APIVersion
{
    /**
     * @param $request
     * @param Closure $next
     * @param $guard
     */
    public function handle($request, Closure $next, $guard)
    {
        config(['app.api.version' => $guard]);
        return $next($request);
    }
}
