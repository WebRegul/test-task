<?php

namespace App\Http\Middleware;

use Closure;

class CorrectInputNull
{
    public function handle($request, Closure $next)
    {
        $input = collect($request->input())
            ->transform(function ($value, $key) {
                if (is_string($value) && $value === 'null') {
                    return null;
                } else {
                    return $value;
                }
            });

        $request->replace($input->toArray());

        return $next($request);
    }
}
