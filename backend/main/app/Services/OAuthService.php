<?php

namespace App\Services;

use App\Models\Profile as ProfileModel;
use App\Models\RegisterSource;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;
use App\Services\Drivers\Socialite\SocialiteInterfaceDriver;
use App\Models\User as UserModel;
use Exception;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Services\User;

class OAuthService
{
    /**
     * Перенаправление пользователя на социальный сайт
     * @param string $provider
     * @param SocialiteInterfaceDriver $driver
     */
    public function auth(string $provider, SocialiteInterfaceDriver $driver)
    {
        return $driver->auth($provider);
    }

    /**
     * @param string|null $code
     * @param string $provider
     * @param SocialiteInterfaceDriver $driver
     * @return mixed
     */
    public function callback(?string $code = null, string $provider, SocialiteInterfaceDriver $driver)
    {
        return $driver->callback($code, $provider);
    }

    /**
     * @param string $provider
     * @param string $uid
     * @return bool
     * @throws \App\Exceptions\ForbiddenException
     * @throws \App\Exceptions\IsVerifiedException
     * @throws \Illuminate\Auth\AuthenticationException
     */
    public function registrationByUid(string $provider, string $uid): bool
    {
        if (empty(config('services.' . $provider))) {
            throw new Exception('неизвестный провайдер ' . $provider, 409);
        }

        if ($authUser = Cache::get($uid)) {
            $login = Arr::get($authUser, 'login');
            $user = UserModel::query()
                ->where('login', $login)
                ->first();
            $isNewUser = empty($user->id);

            if ($isNewUser) {
                $id = (new User())->registration(
                    $login,
                    Hash::make(Carbon::now()),
                    Arr::get($authUser, 'profile.name'),
                    Arr::get($authUser, 'profile.surname'),
                    $provider
                )->get('user_id');

                if (!empty($id)) {
                    DB::transaction(function () use ($id, $login, $authUser) {
                        (new User($id))->verify(null, true);

                        (new Profile())->setByUserId($id)->update([
                            'source_data' => [
                                'login' => $login,
                                'url' => Arr::get($authUser, 'profile.url')
                            ]
                        ]);
                    });
                }
            }

            return $isNewUser;
        } else {
            throw new Exception('неизвестный uid', 400);
        }
    }

    /**
     * @param string $provider
     * @param array $data
     */
    public function authByLogin(string $provider, array $data): object
    {
        $response = Http::withHeaders([
            'Access-Token' => Hash::make(config('services.' . $provider . '.access_token')),
            'Accept' => 'application/json',
            'Content-Type' => 'application/json'
        ])->post(config('services.' . $provider . '.host') . '/api/auth-from-gallery', $data);
        $status = $response->status();
        $result = $response->json();
        DB::beginTransaction();
        try {
            if (Arr::get($result, 'success')) {
                $internalId = Arr::get($result, 'user.id');
                if (!empty($user = UserModel::with('profile')->where('login', $data['login'])->first())) {
                    if (Arr::get($user, 'profile.register_source_internal_id') != $internalId) {
                        $this->saveProfile($user, $internalId, $provider);
                    }
                } else {
                    $user = UserModel::create(['login' => $data['login']]);
                    $this->saveProfile($user, $internalId, $provider);
                }
                $auth = Auth::login($user, true);
                $result['token'] = $auth;
            }
            DB::commit();

            return response()->json($result, $status);
        } catch (Exception $e) {
            DB::rollBack();

            throw $e;
        }
    }

    protected function saveProfile(User $user, string $userId, string $provider): Profile
    {
        $sourceId = Arr::get(RegisterSource::where('name', $provider)->first(), 'id');
        if (!$sourceId) {
            throw new Exception('неизвестный провайдер', 409);
        }
        $profile = ProfileModel::query()
            ->where('user_id', $user->id)
            ->firstOrNew();

        $profile->register_source_id = $sourceId;
        $profile->register_source_internal_id = $userId;
        $profile->creator_id = $user->id;
        $profile->updater_id = $user->id;
        $profile->user_id = $user->id;
        $profile->save();
        $profile->refresh();

        return $profile;
    }
}
