<?php

namespace App\Http\Middleware;

use Closure;

/**
 * Class SecureApiDocs
 * @package App\Http\Middleware
 */
class SecureApiDocs
{
    public function handle($request, Closure $next)
    {
        if (env('APP_ENV') === 'local') {
            return $next($request);
        }

        $token = $request->get('token');
        if (!$token) {
            // try to load the token from referer
            $query = [];
            parse_str(
                parse_url($request->header('referer'), PHP_URL_QUERY),
                $query
            );

            if (isset($query['token'])) {
                $token = $query['token'];
            }
        }

        if ($token === env('SWAGGER_DOCS_TOKEN')) {
            return $next($request);
        } else {
            return abort(403);
        }
    }
}
