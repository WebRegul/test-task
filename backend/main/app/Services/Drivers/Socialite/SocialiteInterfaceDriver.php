<?php

namespace App\Services\Drivers\Socialite;

use Illuminate\Http\JsonResponse;

interface SocialiteInterfaceDriver
{
    /**
     * @param string $provider
     */
    public function auth(string $provider);

    /**
     * @param string $code
     * @param string $provider
     */
    public function callback(string $code, string $provider);
}
