<?php

namespace App\Http\Controllers\V1;

use App\Http\Requests\Security\RegistrationByUidRequest;
use Illuminate\Http\Request;
use App\Http\Requests\Security\AuthByLoginRequest;
use App\Services\OAuthService;
use App\Services\Drivers\Socialite\LocalSocialiteDriver;
use App\Services\Drivers\Socialite\SocialiteDriver;

/**
 * Class OAuthController
 * @package App\Http\Controllers\V1
 */
class OAuthController extends Controller
{
    /**
     * @OA\Get(
     *   path="/{api_version}/security/oauth/{provider}/auth",
     *   summary="перенаправление пользователя на социальный сайт",
     *   tags={"security"},
     *   @OA\Parameter(
     *      name="api_version",
     *      description="версия api",
     *      in="path",
     *      required=true,
     *      @OA\Schema(
     *          type="string", format="^v\d{1,}$", default="v1"
     *      )
     *   ),
     *  @OA\Parameter(
     *      name="provider",
     *      description="название соц сети",
     *      in="path",
     *      required=true,
     *      @OA\Schema(
     *         type="string", format="^[a-zA-Z0-9_\-]+$"
     *      )
     *   ),
     *   @OA\Response(
     *      response=200,
     *      description="редирект на соц сеть",
     *    ),
     * )
     */

    /**
     * перенаправление пользователя на социальный сайт
     * @param string $provider
     * @param OAuthService $service
     * @return mixed
     */
    public function auth(string $provider, OAuthService $service)
    {
        $driver = (config('services.' . $provider . '.driver') == 'local') ? new LocalSocialiteDriver() : new SocialiteDriver();

        return response()->json(['url' => $service->auth($provider, $driver)]);
    }

    /**
     * @OA\Get(
     *   path="/{api_version}/security/oauth/{provider}/callback",
     *   summary="авторизация через соц сеть",
     *   tags={"security"},
     *   @OA\Parameter(
     *      name="api_version",
     *      description="версия api",
     *      in="path",
     *      required=true,
     *      @OA\Schema(
     *          type="string", format="^v\d{1,}$", default="v1"
     *      )
     *   ),
     *  @OA\Parameter(
     *      name="provider",
     *      description="название соц сети",
     *      in="path",
     *      required=true,
     *      @OA\Schema(
     *         type="string", format="^[a-zA-Z0-9_\-]+$"
     *      )
     *   ),
     *  @OA\Parameter(
     *      name="code",
     *      description="код авторизации от соц сети",
     *      in="query",
     *      required=true,
     *      @OA\Schema(
     *         type="string", format="^[a-zA-Z0-9_\-]+$"
     *      )
     *  ),
     *   @OA\Response(
     *      response=200,
     *      description="успешная авторизация через соц сеть",
     *      @OA\JsonContent(ref="#/components/schemas/SuccessAuthSchema")
     *   )
     * )
     */

    /**
     * принимает коллбек от провайдеров
     * @param string $provider
     * @param Request $request
     * @param OAuthService $service
     * @return mixed
     */
    public function callback(string $provider, Request $request, OAuthService $service)
    {
        $driver = (config('services.' . $provider . '.driver') == 'local')
            ? new LocalSocialiteDriver()
            : new SocialiteDriver();

        return $service->callback($request->code, $provider, $driver);
    }


    /**
     * @OA\Post(
     *   path="/{api_version}/security/oauth/{provider}/auth-by-login",
     *   summary="Авторизация в хд по логину и паролю",
     *   tags={"security"},
     *   @OA\Parameter(
     *      name="api_version",
     *      description="версия api",
     *      in="path",
     *      required=true,
     *      @OA\Schema(
     *          type="string", format="^v\d{1,}$", default="v1"
     *      )
     *   ),
     *  @OA\Parameter(
     *      name="provider",
     *      description="название соц сети",
     *      in="path",
     *      required=true,
     *      @OA\Schema(
     *         type="string", format="^[a-zA-Z0-9_\-]+$"
     *      )
     *   ),
     *  @OA\Parameter(
     *      name="login",
     *      description="логин",
     *      in="query",
     *      required=true,
     *      @OA\Schema(
     *         type="string", format="^[a-zA-Z0-9_\-]+$"
     *      )
     *  ),
     *  @OA\Parameter(
     *      name="password",
     *      description="пароль",
     *      in="query",
     *      required=true,
     *      @OA\Schema(
     *         type="string", format="^[a-zA-Z0-9_\-]+$"
     *      )
     *  ),
     *   @OA\Response(
     *      response=200,
     *      description="ответ получен",
     *      @OA\JsonContent(
     *        @OA\Property(
     *          property="data",
     *          type="array",
     *          description="массив данных",
     *          @OA\Items(
     *              @OA\Property(property="...", type="object")
     *          )
     *       )
     *     )
     *   )
     * )
     */

    /**
     * Авторизация в хд по логину и паролю
     * @param string $provider
     * @param Request $request
     * @param OAuthService $service
     */
    public function authByLogin(string $provider, AuthByLoginRequest $request, OAuthService $service)
    {
        $data = [
            'login' => $request->input('login'),
            'password' => $request->input('password')
        ];

        return $service->authByLogin($provider, $data);
    }

    /**
     * @OA\Post(
     *   path="/{api_version}/security/oauth/{provider}/change",
     *   summary="регистрация пользователя по uid с данными провайдера",
     *   tags={"security"},
     *   @OA\Parameter(
     *      name="api_version",
     *      description="версия api",
     *      in="path",
     *      required=true,
     *      @OA\Schema(
     *          type="string", format="^v\d{1,}$", default="v1"
     *      )
     *   ),
     *  @OA\Parameter(
     *      name="provider",
     *      description="название соц сети",
     *      in="path",
     *      required=true,
     *      @OA\Schema(
     *         type="string", format="^[a-zA-Z0-9_\-]+$"
     *      )
     *   ),
     *  @OA\Parameter(
     *      name="uid",
     *      description="uid данных провайдера",
     *      in="query",
     *      required=true,
     *      @OA\Schema(
     *         type="string", format="uuid"
     *      )
     *  ),
     *  @OA\Response(
     *      response=400,
     *      description="uid даненных не найден",
     *      @OA\JsonContent(
     *        @OA\Property(
     *          property="success",
     *          type="boolean",
     *          default="false",
     *          description="флаг успеха операции"
     *        ),
     *        @OA\Property(
     *          property="message",
     *          type="string",
     *          description="сообщение о результате операции"
     *        )
     *      )
     *   ),
     *  @OA\Response(
     *      response=409,
     *      description="неизвестный провайдер",
     *      @OA\JsonContent(
     *        @OA\Property(
     *          property="success",
     *          type="boolean",
     *          default="false",
     *          description="флаг успеха операции"
     *        ),
     *        @OA\Property(
     *          property="message",
     *          type="string",
     *          description="сообщение о результате операции"
     *        )
     *      )
     *   ),
     *   @OA\Response(
     *      response=200,
     *      description="ответ получен",
     *      @OA\JsonContent(
     *        @OA\Property(
     *          property="registration",
     *          type="boolean",
     *          description="true - новый пользователь; false - уже зарегистрированный пользователь"
     *       )
     *     )
     *   ),
     * )
     */

    /**
     * @param RegistrationByUidRequest $request
     * @param string $provider
     * @param OAuthService $service
     * @return \Illuminate\Http\JsonResponse
     * @throws \App\Exceptions\ForbiddenException
     * @throws \App\Exceptions\IsVerifiedException
     * @throws \Illuminate\Auth\AuthenticationException
     */
    public function registrationByUid(RegistrationByUidRequest $request, string $provider, OAuthService $service)
    {
        $isNewUser = $service->registrationByUid($provider, $request->get('uid'));
        $result = collect(['registration' => $isNewUser]);

        return response()->json($result);
    }
}
