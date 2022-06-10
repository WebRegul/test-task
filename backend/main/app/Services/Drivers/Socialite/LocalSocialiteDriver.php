<?php

namespace App\Services\Drivers\Socialite;

use Carbon\Carbon;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Arr;
use App\Models\User as UserModel;
use Illuminate\Support\Str;

class LocalSocialiteDriver implements SocialiteInterfaceDriver
{
    /**
     * @param string $provider
     */
    public function auth(string $provider)
    {
        $query = http_build_query([
            'client_id' => config('services.' . $provider . '.client_id'),
            'redirect_uri' => config('services.' . $provider . '.redirect'),
            'display' => 'page',
            'response_type' => 'code',
            'scope' => ($provider == 'vkontakte') ? 'email' : '',
            'prompt' => 'select_account'
        ]);

        return config('services.' . $provider . '.host') . '/oauth/authorize?' . $query;
//        return redirect(config('services.' . $provider . '.host') . '/oauth/authorize?' . $query)
//            ->header('Access-Control-Allow-Origin', '*')->header('Access-Control-Allow-Credentials', 'true');
    }

    /**
     * @param string|null $code
     * @param string $provider
     * @return array|\Illuminate\Http\Client\Response|\Illuminate\Http\RedirectResponse|\Laravel\Lumen\Http\Redirector|mixed
     * @throws AuthorizationException
     */
    public function callback(?string $code = null, string $provider)
    {
        if (empty($code)) {
            $url = env('APP_FRONT_URL') . "/auth/oauth/{$provider}/failed";
            return redirect($url);
        }

        $clientHost = config('services.' . $provider . '.host');
        $data = [
            'grant_type' => 'authorization_code',
            'client_id' => config('services.' . $provider . '.client_id'),
            'client_secret' => config('services.' . $provider . '.client_secret'),
            'redirect_uri' => config('services.' . $provider . '.redirect'),
            'code' => $code,
        ];

        if ($provider == 'vkontakte') {
            $query = http_build_query($data);
            $res = Http::get($clientHost . '/oauth/access_token?' . $query);
            $this->addEmailUser(json_decode($res, true));

            return $res;
        } elseif ($provider == 'happyday') {
            $response = Http::post($clientHost . '/oauth/token', $data);
            if (empty(Arr::get($response, 'error'))) {
                $authUser = collect($this->getUser(Arr::get($response, 'access_token'), $clientHost))
                    ->put('provider', $provider)->toArray();
                $uuid = Str::uuid()->toString();

                Cache::add($uuid, $authUser, Carbon::now()->addMinutes(5));

                $url = env('APP_FRONT_URL') . '/auth/oauth/happyday/success?';
                $url .= http_build_query([
                    'uid' => $uuid,
                    'name' => Arr::get($authUser, 'profile.name'),
                    'lastname' => Arr::get($authUser, 'profile.surname'),
                ]);

                return redirect($url);
            } else {
                throw new AuthorizationException(Arr::get($response, 'error'));
            }
        } else {
            $response = Http::post($clientHost . '/oauth/token', $data);

            return $response->json();
        }
    }

    /**
     * @param string $token
     * @param string $clientHost
     * @return array
     */
    protected function getUser(string $token, string $clientHost): array
    {
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json',
            'Content-Type' => 'application/json'
        ])->get($clientHost . '/api/user')->json();
        return $response;
    }

    /**
     * @param array $aAuthUser
     */
    protected function addUser(array $aAuthUser): bool
    {
        $user = UserModel::where('login', Arr::get($aAuthUser, 'login'))->first();
        if (empty($user)) {
            $user = UserModel::create(['login' => Arr::get($aAuthUser, 'login')]);
        }
        return true;
    }

    /**
     * @param array $aAuthUser
     */
    protected function addEmailUser(array $aAuthUser): bool
    {
        $user = UserModel::where('email', Arr::get($aAuthUser, 'email'))->first();
        if (empty($user)) {
            $user = UserModel::create(['email' => Arr::get($aAuthUser, 'email')]);
        }

        return true;
    }
}
