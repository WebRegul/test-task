<?php

namespace App\Http\Middleware;

use App\Helpers\Permissions;
use Closure;
use Illuminate\Auth\AuthenticationException;

class Verify
{
    public function handle($request, Closure $next)
    {
        $user = auth()->user();
        if (!empty($user) && !Permissions::isVerified($user)) {
            throw new AuthenticationException('пользователь не верифицирован');
        }

        return $next($request);
    }
}
