<?php

namespace App\Services\Drivers\Socialite;

use Illuminate\Auth\Access\AuthorizationException;
use Socialite;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Models\User;
use Illuminate\Http\JsonResponse;

class SocialiteDriver implements SocialiteInterfaceDriver
{
    /**
     * @param string $provider
     */
    public function auth(string $provider)
    {
        return Socialite::driver($provider)->stateless()->redirect();
    }


    /**
     * @param string|null $code
     * @param string $provider
     * @return JsonResponse
     */
    public function callback(?string $code = null, string $provider)
    {
        if (empty($code)) {
            $url = env('APP_FRONT_URL') . "/auth/oauth/{$provider}/failed";
            return redirect($url);
        }

        $providerUser = Socialite::driver($provider)->stateless()->user();
        $user = User::query()->firstOrNew(['email' => $providerUser->getEmail()]);
        if (!$user->exists) {
            $user->save();
        }
        $token = JWTAuth::fromUser($user);
        return new JsonResponse([
            'token' => $token
        ]);
    }
}
