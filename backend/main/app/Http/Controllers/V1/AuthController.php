<?php

namespace App\Http\Controllers\V1;

use App\Helpers\Helper;
use App\Http\Requests\Security\AuthLoginRequest;
use App\Http\Requests\Security\AuthRepeatSendCodeRequest;
use App\Http\Requests\Security\AuthRegistrationRequest;
use App\Http\Requests\Security\AuthVerifyByCodeRequest;
use App\Http\Requests\Security\CheckSmsCodeRequest;
use App\Http\Requests\Security\CreateRegistrationContactsRequest;
use App\Http\Requests\Security\PreregistrationRequest;
use App\Http\Requests\Security\ResetPasswordRequest;
use App\Http\Requests\Security\SendSmsCodeRequest;
use App\Http\Requests\Security\UpdatePasswordRequest;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\JsonResponse;
use App\Services\User;

/**
 * Class AuthController
 * @package App\Http\Controllers\V1
 */
class AuthController extends Controller
{
    /**
     * @OA\Post(
     *   path="/{api_version}/security/login",
     *   summary="авторизация пользователя",
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
     *   @OA\Parameter(
     *      name="login",
     *      description="телефон пользователя, который является логином",
     *      in="query",
     *      required=true,
     *      @OA\Schema(
     *          type="string", format="7\d{10}"
     *      )
     *   ),
     *   @OA\Parameter(
     *      name="password",
     *      description="пароль пользователя",
     *      in="query",
     *      required=true,
     *      @OA\Schema(
     *          type="string", format="[^А-Яа-яеЁ]+"
     *      )
     *  ),
     *  @OA\Response(
     *      response=401,
     *      description="ошибка аутентификации",
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
     *      description="успешная аутентификация",
     *      @OA\JsonContent(ref="#/components/schemas/SuccessAuthSchema")
     *    )
     * )
     */

    /**
     * @param AuthLoginRequest $request
     * @return JsonResponse
     * @throws AuthenticationException
     */
    public function login(AuthLoginRequest $request): JsonResponse
    {
        if ($request->has('login')) {
            $credentials = $request->only(['login', 'password']);
            if (!$token = auth()->attempt($credentials)) {
                throw new AuthenticationException('неверный логин или пароль');
            }
        } else {
            if ($request->get('type') == 'oauth') {
                $user = new User();
                $token = $user->loginByUid($request->get('uid'));
            }
        }

        return Helper::respondWithToken($token);
    }
    /**
     * @OA\Post(
     *   path="/{api_version}/security/logout",
     *   summary="выход пользователя из приложения",
     *   tags={"security"},
     *   @OA\Parameter(
     *      name="api_version",
     *      description="версия api",
     *      in="path",
     *      required=true,
     *      @OA\Schema(
     *          type="string", format="^v\d{1,}$", default="v1"
     *      )
     *  ),
     *  @OA\Response(
     *      response=401,
     *      description="пользователь не авторизован или не верифицирован",
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
     *      description="ошибка выхода",
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
     *      description="успешный выход"
     *    ),
     *    security={{ "bearerAuth": {} }}
     * )
     */

    /**
     * @return JsonResponse|\Illuminate\Http\Response|\Laravel\Lumen\Http\ResponseFactory
     */
    public function logout()
    {
        auth()->logout();

        return response(null);
    }

    /**
     * @OA\Post(
     *   path="/{api_version}/security/refresh",
     *   summary="выход пользователя из приложения",
     *   tags={"security"},
     *   @OA\Parameter(
     *      name="api_version",
     *      description="версия api",
     *      in="path",
     *      required=true,
     *      @OA\Schema(
     *          type="string", format="^v\d{1,}$", default="v1"
     *      )
     *  ),
     *   @OA\Response(
     *      response=409,
     *      description="ошибка обновления времени  жизни токена",
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
     *      description="успешное обновление времени жизни токена",
     *      @OA\JsonContent(ref="#/components/schemas/SuccessAuthSchema")
     *    ),
     *    security={{ "bearerAuth": {} }}
     * )
     */

    /**
     * @return JsonResponse
     */
    public function refresh()
    {
        return Helper::respondWithToken(auth()->refresh());
    }

    /**
     * @OA\Post(
     *   path="/{api_version}/security/registration",
     *   summary="регистрация нового пользователя",
     *   tags={"security"},
     *   @OA\Parameter(
     *      name="api_version",
     *      description="версия api",
     *      in="path",
     *      required=true,
     *      @OA\Schema(
     *          type="string", format="^v\d{1,}$", default="v1"
     *      )
     *  ),
     *   @OA\Parameter(
     *      name="login",
     *      description="телефон пользователя, который является логином",
     *      in="query",
     *      required=true,
     *      @OA\Schema(
     *          type="string", format="7\d{10}"
     *      )
     *   ),
     *   @OA\Parameter(
     *      name="password",
     *      description="пароль пользователя",
     *      in="query",
     *      required=true,
     *      @OA\Schema(
     *          type="string", format="[^А-Яа-яеЁ]+"
     *      )
     *  ),
     *   @OA\Parameter(
     *      name="name",
     *      description="имя пользователя",
     *      in="query",
     *      required=true,
     *      @OA\Schema(
     *          type="string", format="[a-zA-Zа-яА-ЯёЁ\-]+"
     *      )
     *  ),
     *   @OA\Parameter(
     *      name="surname",
     *      description="фамилия пользователя",
     *      in="query",
     *      required=true,
     *      @OA\Schema(
     *          type="string", format="[a-zA-Zа-яА-ЯёЁ\-]+"
     *      )
     *  ),
     *  @OA\Response(
     *      response=409,
     *      description="пользователь уже верифицирован",
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
     *      response=422,
     *      description="ошибка регистрации",
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
     *        ),
     *        @OA\Property(
     *          property="errors",
     *          type="array",
     *          description="сообщения об ошибках",
     *          @OA\Items(ref="#/components/schemas/RequestValidationErrorsSchema")
     *        )
     *      )
     *   ),
     *   @OA\Response(
     *      response=200,
     *      description="успешная регистрация",
     *      @OA\JsonContent(
     *        @OA\Property(
     *          property="user_id",
     *          type="string",
     *          format="uuid",
     *          description="идентификатор пользователя"
     *        ),
     *        @OA\Property(
     *          property="code",
     *          type="integer",
     *          description="код верификации, работает только если env-переменная `SMS_CODE_DEBUG=true`"
     *        )
     *      )
     *    )
     * )
     */

    /**
     * @param AuthRegistrationRequest $request
     * @return JsonResponse
     */
    public function registration(AuthRegistrationRequest $request): JsonResponse
    {
        $login = request('login');
        $password = request('password');
        $firstName = request('name');
        $lastName = request('surname');
        $result = (new User())->registration($login, $password, $firstName, $lastName);

        return response()->json($result);
    }

    /**
     * @OA\Post(
     *   path="/{api_version}/security/registration/contacts",
     *   summary="создание контактов пользователя при регистрации",
     *   tags={"security"},
     *   @OA\Parameter(
     *      name="api_version",
     *      description="версия api",
     *      in="path",
     *      required=true,
     *      @OA\Schema(
     *          type="string", format="^v\d{1,}$", default="v1"
     *      )
     *  ),
     *   @OA\Parameter(
     *      name="phone",
     *      description="телефон пользователя",
     *      in="query",
     *      required=true,
     *      @OA\Schema(
     *          type="string", format="7\d{10}"
     *      )
     *   ),
     *   @OA\Parameter(
     *      name="email",
     *      description="адрес электронной почты пользователя: валидируется по рфц",
     *      in="query",
     *      required=true,
     *      @OA\Schema(
     *          type="string"
     *      )
     *  ),
     *  @OA\Response(
     *      response=404,
     *      description="указанного типа контакта не существует",
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
     *      response=422,
     *      description="ошибка создания контактов",
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
     *        ),
     *        @OA\Property(
     *          property="errors",
     *          type="array",
     *          description="сообщения об ошибках",
     *          @OA\Items(ref="#/components/schemas/RequestValidationErrorsSchema")
     *        )
     *      )
     *   ),
     *   @OA\Response(
     *      response=200,
     *      description="успешное создание контактов пользователя",
     *      @OA\JsonContent(
     *        @OA\Property(
     *          property="[]",
     *          type="object",
     *          description="массив сохраненных контактов пользователя"
     *        )
     *      )
     *    ),
     *    security={{ "bearerAuth": {} }}
     * )
     */

    /**
     * @param CreateRegistrationContactsRequest $request
     * @return JsonResponse
     */
    public function createRegistrationContacts(CreateRegistrationContactsRequest $request): JsonResponse
    {
        $contacts = collect();
        foreach (['phone', 'email'] as $key) {
            $contacts->put($key, $request->get($key));
        }

        $contacts = (new User())->createRegistrationContacts($contacts->toArray());

        return response()->json($contacts->toArray());
    }

    /**
     * @OA\Post(
     *   path="/{api_version}/security/verify",
     *   summary="верификация и последующая аутентификация пользователя",
     *   tags={"security"},
     *   @OA\Parameter(
     *      name="api_version",
     *      description="версия api",
     *      in="path",
     *      required=true,
     *      @OA\Schema(
     *          type="string", format="^v\d{1,}$", default="v1"
     *      )
     *  ),
     *   @OA\Parameter(
     *      name="user_id",
     *      description="идентификатор пользователя",
     *      in="query",
     *      required=true,
     *      @OA\Schema(
     *          type="string", format="uuid"
     *      )
     *   ),
     *   @OA\Parameter(
     *      name="code",
     *      description="код верификации",
     *      in="query",
     *      required=true,
     *      @OA\Schema(
     *          type="integer", format="\d{4}"
     *      )
     *  ),
     *  @OA\Response(
     *      response=401,
     *      description="неверный код верификации",
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
     *      description="пользователь уже верифицирован",
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
     *      response=422,
     *      description="ошибка верификации",
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
     *        ),
     *        @OA\Property(
     *          property="errors",
     *          type="array",
     *          description="сообщения об ошибках",
     *          @OA\Items(ref="#/components/schemas/RequestValidationErrorsSchema")
     *        )
     *      )
     *   ),
     *   @OA\Response(
     *      response=200,
     *      description="успешная верификация и аутентификация",
     *      @OA\JsonContent(ref="#/components/schemas/SuccessAuthSchema")
     *   )
     * )
     */

    /**
     * @param AuthVerifyByCodeRequest $request
     * @return JsonResponse
     */
    public function verify(AuthVerifyByCodeRequest $request): JsonResponse
    {
        $userId = $request->get(('user_id'));
        $code = $request->get('code');
        $token = (new User($userId))->verify($code);

        return Helper::respondWithToken($token);
    }

    /**
     * @OA\Post(
     *   path="/{api_version}/security/send-code/{user_id}",
     *   summary="отправка кода верификации",
     *   description="внимание: **количество попыток переполучения кода ограничено!** в `.env` - переменная `AUTH_REPEAT_CODE_SENDS`",
     *   tags={"security"},
     *   @OA\Parameter(
     *      name="api_version",
     *      description="версия api",
     *      in="path",
     *      required=true,
     *      @OA\Schema(
     *          type="string", format="^v\d{1,}$", default="v1"
     *      )
     *  ),
     *   @OA\Parameter(
     *      name="user_id",
     *      description="идентификатор пользователя",
     *      in="path",
     *      required=true,
     *      @OA\Schema(
     *          type="string", format="uuid"
     *      )
     *   ),
     *   @OA\Response(
     *      response=401,
     *      description="блокировка по превышению кол-ва попыток отправки кода",
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
     *      response=409,
     *      description="пользователь уже верифицирован",
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
     *      response=422,
     *      description="ошибка верификации",
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
     *        ),
     *        @OA\Property(
     *          property="errors",
     *          type="array",
     *          description="сообщения об ошибках",
     *          @OA\Items(ref="#/components/schemas/RequestValidationErrorsSchema")
     *        )
     *      )
     *   ),
     *   @OA\Response(
     *      response=200,
     *      description="успешная верификация и аутентификация",
     *      @OA\JsonContent(
     *        @OA\Property(
     *          property="user_id",
     *          type="string",
     *          format="uuid",
     *          description="идентификатор пользователя"
     *        ),
     *        @OA\Property(
     *          property="code",
     *          type="integer",
     *          description="код верификации; количество попыток переполучения кода ограничено!"
     *        )
     *      )
     *      )
     *   )
     * )
     */

    /**
     * @param AuthRepeatSendCodeRequest $request
     * @return JsonResponse
     */
    public function sendCode(AuthRepeatSendCodeRequest $request): JsonResponse
    {
        $userId = $request->get('user_id');
        return (new User($userId))->repeatSendCode();
    }

    /**
     * @OA\Post(
     *   path="/{api_version}/security/preregistration",
     *   summary="пререгистрация пользователя",
     *   tags={"security"},
     *   @OA\Parameter(
     *      name="api_version",
     *      description="версия api",
     *      in="path",
     *      required=true,
     *      @OA\Schema(
     *          type="string", format="^v\d{1,}$", default="v1"
     *      )
     *  ),
     *   @OA\Parameter(
     *      name="phone",
     *      description="телефон пользователя, который впоследствии будет являться логином",
     *      in="query",
     *      required=true,
     *      @OA\Schema(
     *          type="string", format="7\d{10}"
     *      )
     *   ),
     *   @OA\Parameter(
     *      name="full_name",
     *      description="имя пользователя",
     *      in="query",
     *      required=true,
     *      @OA\Schema(
     *          type="string", format="^[\w\d\.\,\:\;\-\?\!\ ё]+$"
     *      )
     *  ),
     *  @OA\Response(
     *      response=422,
     *      description="ошибка пререгистрации",
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
     *        ),
     *        @OA\Property(
     *          property="errors",
     *          type="array",
     *          description="сообщения об ошибках",
     *          @OA\Items(ref="#/components/schemas/RequestValidationErrorsSchema")
     *        )
     *      )
     *   ),
     *   @OA\Response(
     *      response=200,
     *      description="успешная пререгистрация",
     *      @OA\JsonContent(
     *        @OA\Property(
     *          property="preregistration_user_id",
     *          type="string",
     *          format="uuid",
     *          description="идентификатор пользователя"
     *        ),
     *        @OA\Property(
     *          property="preregistration_user",
     *          type="array",
     *          description="массив данных пользователя",
     *          @OA\Items(
     *            @OA\Property(property="...", type="string")
     *          )
     *        )
     *      )
     *    )
     * )
     */

    /**
     * @param PreregistrationRequest $request
     * @return JsonResponse
     */
    public function preregistration(PreregistrationRequest $request): JsonResponse
    {
        $data = $request->all();
        $user = (new User())->preregistration($data);

        return response()->json($user);
    }

    /**
     * @OA\Post(
     *   path="/{api_version}/security/password/reset",
     *   summary="восстановление пароля",
     *   tags={"security"},
     *   @OA\Parameter(
     *      name="api_version",
     *      description="версия api",
     *      in="path",
     *      required=true,
     *      @OA\Schema(
     *          type="string", format="^v\d{1,}$", default="v1"
     *      )
     *  ),
     *   @OA\Parameter(
     *      name="phone",
     *      description="логин пользователя",
     *      in="query",
     *      required=true,
     *      @OA\Schema(
     *          type="string", format="7\d{10}"
     *      )
     *   ),
     *  @OA\Response(
     *      response=422,
     *      description="ошибка восстановления пароля",
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
     *        ),
     *        @OA\Property(
     *          property="errors",
     *          type="array",
     *          description="сообщения об ошибках",
     *          @OA\Items(ref="#/components/schemas/RequestValidationErrorsSchema")
     *        )
     *      )
     *   ),
     *   @OA\Response(
     *      response=200,
     *      description="запрос на восстановление пароля отправлен",
     *      @OA\JsonContent(
     *        @OA\Property(
     *          property="uid",
     *          type="string",
     *          format="uuid",
     *          description="uid"
     *        ),
     *         @OA\Property(
     *          property="reset_id",
     *          type="string",
     *          format="uuid",
     *          description="reset_id"
     *        ),
     *
     *      )
     *    )
     * )
     */

    /**
     * Восстановление пароля
     * @param ResetPasswordRequest $request
     * @param User $user
     * @return JsonResponse
     */
    public function resetPassword(ResetPasswordRequest $request, User $user): JsonResponse
    {
        $phone = $request->get('phone');
        $result = $user->resetPassword($phone);

        return response()->json($result);
    }


    /**
     * @OA\Post(
     *   path="/{api_version}/security/password/update",
     *   summary="восстановление пароля. обновление пароля",
     *   tags={"security"},
     *   @OA\Parameter(
     *      name="api_version",
     *      description="версия api",
     *      in="path",
     *      required=true,
     *      @OA\Schema(
     *          type="string", format="^v\d{1,}$", default="v1"
     *      )
     *  ),
     *   @OA\Parameter(
     *      name="password",
     *      description="новый пароль",
     *      in="query",
     *      required=true,
     *      @OA\Schema(
     *          type="string"
     *      )
     *   ),
     *   @OA\Parameter(
     *      name="reset_id",
     *      description="reset_id",
     *      in="query",
     *      required=true,
     *      @OA\Schema(
     *          type="string"
     *      )
     *   ),
     *  @OA\Response(
     *      response=422,
     *      description="ошибка восстановления пароля",
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
     *        ),
     *        @OA\Property(
     *          property="errors",
     *          type="array",
     *          description="сообщения об ошибках",
     *          @OA\Items(ref="#/components/schemas/RequestValidationErrorsSchema")
     *        )
     *      )
     *   ),
     *   @OA\Response(
     *      response=200,
     *      description="Пароль обновлен!",
     *      @OA\JsonContent(
     *         @OA\Property(
     *          property="success",
     *          type="boolean",
     *          default="true",
     *          description="флаг успеха операции"
     *        ),
     *         @OA\Property(
     *          property="token",
     *          type="string",
     *          format="string",
     *          description="токен"
     *        ),
     *
     *      )
     *    )
     * )
     */
    /**
     * Восстановление пароля. Обновление пароля и вход в систему
     * @param UpdatePasswordRequest $request
     * @param User $user
     * @return JsonResponse
     */
    public function updatePassword(UpdatePasswordRequest $request, User $user)
    {
        $data = $request->only(['password', 'reset_id']);
        $result = $user->updatePassword($data);

        return response()->json($result);
    }


    /**
     * @OA\Post(
     *   path="/{api_version}/security/send-sms-code",
     *   summary="отправка sms",
     *   tags={"security"},
     *   @OA\Parameter(
     *      name="api_version",
     *      description="версия api",
     *      in="path",
     *      required=true,
     *      @OA\Schema(
     *          type="string", format="^v\d{1,}$", default="v1"
     *      )
     *  ),
     *    @OA\Parameter(
     *      name="uid",
     *      description="uid полученный в методе password/reset",
     *      in="query",
     *      required=true,
     *      @OA\Schema(
     *          type="string", format="7\d{10}"
     *      )
     *   ),
     *  @OA\Response(
     *      response=422,
     *      description="ошибка отправки смс",
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
     *        ),
     *        @OA\Property(
     *          property="errors",
     *          type="array",
     *          description="сообщения об ошибках",
     *          @OA\Items(ref="#/components/schemas/RequestValidationErrorsSchema")
     *        )
     *      )
     *   ),
     *   @OA\Response(
     *      response=200,
     *      description="смс отправлено",
     *      @OA\JsonContent(
     *        @OA\Property(
     *          property="user_id",
     *          type="string",
     *          format="uuid",
     *          description="идентификатор пользователя"
     *        ),
     *        @OA\Property(
     *          property="code",
     *          type="string",
     *          format="string",
     *          description="код"
     *        ),
     *      )
     *    )
     * )
     */

    /**
     * @param SendSmsCodeRequest $request
     * @param User $user
     * @return JsonResponse
     */
    public function sendSmsCode(SendSmsCodeRequest $request, User $user): JsonResponse
    {
        $data = $request->only(['phone', 'uid']);
        $result = $user->sendSmsCode($data);

        return response()->json($result);
    }


    /**
     * @OA\Post(
     *   path="/{api_version}/security/check-sms-code",
     *   summary="проверка sms кода",
     *   tags={"security"},
     *   @OA\Parameter(
     *      name="api_version",
     *      description="версия api",
     *      in="path",
     *      required=true,
     *      @OA\Schema(
     *          type="string", format="^v\d{1,}$", default="v1"
     *      )
     *  ),
     *   @OA\Parameter(
     *      name="code",
     *      description="код",
     *      in="query",
     *      required=true,
     *      @OA\Schema(
     *          type="string", format="7\d{10}"
     *      )
     *   ),
     *    @OA\Parameter(
     *      name="reset_id",
     *      description="reset_id полученный в методе password/reset",
     *      in="query",
     *      required=true,
     *      @OA\Schema(
     *          type="string", format="7\d{10}"
     *      )
     *   ),
     *    @OA\Parameter(
     *      name="uid",
     *      description="uid полученный в методе password/reset",
     *      in="query",
     *      required=true,
     *      @OA\Schema(
     *          type="string", format="7\d{10}"
     *      )
     *   ),
     *  @OA\Response(
     *      response=422,
     *      description="ошибка проверки кода",
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
     *        ),
     *        @OA\Property(
     *          property="errors",
     *          type="array",
     *          description="сообщения об ошибках",
     *          @OA\Items(ref="#/components/schemas/RequestValidationErrorsSchema")
     *        )
     *      )
     *   ),
     *   @OA\Response(
     *      response=200,
     *      description="код подтвержден",
     *      @OA\JsonContent(
     *        @OA\Property(
     *          property="success",
     *          type="boolean",
     *          default="true",
     *          description="флаг успеха операции"
     *        ),
     *      )
     *    )
     * )
     */
    /**
     * @param SendSmsCodeRequest $request
     * @param User $user
     * @return JsonResponse
     */
    public function checkSmsCode(CheckSmsCodeRequest $request, User $user): JsonResponse
    {
        $data = $request->only(['code', 'reset_id', 'uid']);
        $result = $user->checkSmsCode($data);

        return response()->json($result);
    }
}
