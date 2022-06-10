<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

/**
 * Class TrustedProxiesMiddleware
 * @package App\Http\Middleware
 */
class TrustedProxiesMiddleware
{
    /**
     *  use 0.0.0.0/0 if you trust any proxy, otherwise replace it with your proxy ips
     *
     * @var string[]
     */
    protected $trustedProxies = [
        '0.0.0.0/0'
    ];

    /**
     * @param $request
     * @param Closure $next
     * @param $guard
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        Request::setTrustedProxies($this->trustedProxies, Request::HEADER_X_FORWARDED_ALL);
        return $next($request);
    }
}
