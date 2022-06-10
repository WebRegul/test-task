<?php

namespace App\Http\Controllers\V1\Cabinet;

use App\Exceptions\ForbiddenException;
use App\Facades\Member;
use App\Http\Requests\Cabinet\ChangePasswordRequest;
use App\Http\Requests\Cabinet\ChangePasswordVerifyRequest;
use App\Http\Requests\Cabinet\ChangePhoneRequest;
use App\Http\Requests\Cabinet\ChangePhoneVerifyRequest;
use App\Http\Requests\Cabinet\ProfileUpdateRequest;
use App\Jobs\DeleteProfileJob;
use App\Services\Profile;
use App\Services\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Arr;
use Carbon\Carbon;

class ProfileController extends BaseController
{
    /**
     * @OA\Get(
     *   path="/{api_version}/cabinet/member",
     *   summary="получение данных синглтона мембер: юзера, профиля и источника регистрации",
     *   tags={"cabinet", "user", "profile", "member"},
     *   @OA\Parameter(
     *      name="api_version",
     *      description="версия api",
     *      in="path",
     *      required=true,
     *      @OA\Schema(
     *          type="string", format="^v\d{1,}$", default="v1"
     *      )
     *   ),
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
     *      description="ошибка получения мембера",
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
     *      description="данные мембера успешно получены",
     *      @OA\JsonContent(
     *        @OA\Property(
     *          property="member",
     *          type="array",
     *          description="массив данных мембера: юзер, профиль, источник регистрации",
     *          @OA\Items(
     *            @OA\Property(property="...", type="string"),
     *            @OA\Property(
     *              property="user",
     *              type="array",
     *              @OA\Items(
     *                @OA\Property(
     *                  property="...", type="string"
     *                )
     *              )
     *            ),
     *            @OA\Property(
     *              property="profile",
     *              type="array",
     *              @OA\Items(
     *                @OA\Property(
     *                  property="...", type="string"
     *                )
     *              )
     *            ),
     *            @OA\Property(
     *              property="tariff",
     *              type="array",
     *              @OA\Items(
     *                @OA\Property(
     *                  property="...", type="string"
     *                )
     *              )
     *            ),
     *            @OA\Property(
     *              property="register_source",
     *              type="array",
     *              @OA\Items(
     *                @OA\Property(
     *                  property="...", type="string"
     *                )
     *              )
     *            ),
     *            @OA\Property(
     *              property="photo",
     *              type="array",
     *              @OA\Items(
     *                @OA\Property(
     *                  property="...", type="string"
     *                )
     *              )
     *            )
     *          )
     *        )
     *      )
     *    ),
     *    security={{ "bearerAuth": {} }}
     * )
     */

    /**
     * @return JsonResponse
     */
    public function getMember(): JsonResponse
    {
        $member = collect();
        $member->put('member', Member::all());

        return response()->json($member);
    }

    /**
     * @OA\Get(
     *  path="/{api_version}/cabinet/storage/size",
     *  summary="получение текущего объема хранилища пользователя",
     *  tags={"cabinet", "storage"},
     *   @OA\Parameter(
     *      name="api_version",
     *      description="версия api",
     *      in="path",
     *      required=true,
     *      @OA\Schema(
     *          type="string", format="^v\d{1,}$", default="v1"
     *      )
     *   ),
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
     *      response=404,
     *      description="неизвестная единица объема хранилища",
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
     *      description="ошибка получения размера хранилища",
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
     *     response=200,
     *      description="текущий объем хранилища получен",
     *      @OA\JsonContent(
     *        @OA\Property(
     *          property="tariff",
     *          type="array",
     *          description="текущий объем хранилища",
     *          @OA\Items(
     *            @OA\Property(property="...", type="object")
     *          )
     *        )
     *      )
     *    ),
     *    security={{ "bearerAuth": {} }}
     * )
     */

    /**
     * @return float
     * @throws Exception
     */
    public function getStorageSize(): JsonResponse
    {
        $storage = Member::get('storage');
        foreach ($storage as &$item) {
            $item = intval($item * 1024 * 1024); // M to B
        }

        return response()->json($storage);
    }

    /**
     * @OA\Get(
     *  path="/{api_version}/cabinet/user/tariff",
     *  summary="получение текущего тарифа пользователя",
     *  tags={"cabinet", "tariff"},
     *   @OA\Parameter(
     *      name="api_version",
     *      description="версия api",
     *      in="path",
     *      required=true,
     *      @OA\Schema(
     *          type="string", format="^v\d{1,}$", default="v1"
     *      )
     *   ),
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
     *      response=404,
     *      description="неизвестная единица объема хранилища",
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
     *      description="ошибка получения тарифа",
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
     *      description="тариф успешно получен",
     *      @OA\JsonContent(
     *        @OA\Property(
     *          property="tariff",
     *          type="array",
     *          description="массив данных о текущем тарифе пользователя",
     *          @OA\Items(
     *            @OA\Property(property="...", type="object")
     *          )
     *        )
     *      )
     *    ),
     *    security={{ "bearerAuth": {} }}
     * )
     */

    /**
     * @return JsonResponse
     * @throws Exception
     */
    public function getUserTariff(): JsonResponse
    {
        $userTariff = (new User($this->userId))->getUserTariff();

        $result = collect();
        $result->put('tariff', $userTariff);

        return response()->json($result);
    }

    /**
     * @OA\Put(
     *   path="/{api_version}/cabinet/profile/update",
     *   summary="обновление профиля",
     *   tags={"cabinet", "profile"},
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
     *      name="name",
     *      description="ссылка профиля для url; уникально для таблицы `profiles`",
     *      in="query",
     *      required=false,
     *      @OA\Schema(
     *          type="string", format="^[a-zA-Z0-9_\-]+$"
     *      )
     *  ),
     *   @OA\Parameter(
     *      name="name",
     *      description="имя",
     *      in="query",
     *      required=false,
     *      @OA\Schema(
     *          type="string", format="[a-zA-Zа-яА-ЯёЁ\-]+"
     *      )
     *   ),
     *   @OA\Parameter(
     *      name="middle_name",
     *      description="отчество",
     *      in="query",
     *      required=false,
     *      @OA\Schema(
     *          type="string", format="[a-zA-Zа-яА-ЯёЁ\-]+"
     *      )
     *   ),
     *   @OA\Parameter(
     *      name="surname",
     *      description="имя",
     *      in="query",
     *      required=false,
     *      @OA\Schema(
     *          type="string", format="[a-zA-Zа-яА-ЯёЁ\-]+"
     *      )
     *   ),
     *   @OA\Parameter(
     *      name="gender",
     *      description="пол",
     *      in="query",
     *      required=false,
     *      @OA\Schema(
     *          type="string"
     *      )
     *   ),
     *   @OA\Parameter(
     *      name="birthday_at",
     *      description="день рождения; формат: пригодный для парсинга `strtotime()` и при этоме `birthday_at < Carbon::now()->timestamp`",
     *      in="query",
     *      required=false,
     *      @OA\Schema(
     *          type="string"
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
     *      response=403,
     *      description="профиль принадлежит другому пользователю",
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
     *      description="ошибка обновления профиля",
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
     *      description="профиль успешно обновлен",
     *      @OA\JsonContent(
     *        @OA\Property(
     *          property="...",
     *          type="object",
     *          description="массив данных профиля"
     *        ),
     *        @OA\Property(
     *          property="contacts",
     *          type="object",
     *          description="массив контактов профиля"
     *        )
     *      )
     *    ),
     *    security={{ "bearerAuth": {} }}
     * )
     */

    /**
     * @param ProfileUpdateRequest $request
     * @return JsonResponse
     * @throws ForbiddenException
     */
    public function updateProfile(ProfileUpdateRequest $request): JsonResponse
    {
        $data = $request->all();
        $profileId = Member::get('profile.id');
        $result = (new Profile($profileId))->update($data);

        return response()->json($result);
    }

    /**
     * @OA\Get(
     *   path="/{api_version}/cabinet/profile",
     *   summary="получение публичных данных профиля",
     *   tags={"cabinet", "profile"},
     *   @OA\Parameter(
     *      name="api_version",
     *      description="версия api",
     *      in="path",
     *      required=true,
     *      @OA\Schema(
     *          type="string", format="^v\d{1,}$", default="v1"
     *      )
     *   ),
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
     *      response=422,
     *      description="ошибка получения профиля",
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
     *      description="данные профиля успешно получены",
     *      @OA\JsonContent(
     *        @OA\Property(
     *          property="profile",
     *          type="array",
     *          description="массив данных профиля",
     *          @OA\Items(
     *            @OA\Property(property="...", type="string"),
     *            @OA\Property(
     *              property="contacts",
     *              type="object"
     *            )
     *          )
     *        )
     *      )
     *    ),
     *    security={{ "bearerAuth": {} }}
     * )
     */

    /**
     * @return JsonResponse
     */
    public function getProfile(): JsonResponse
    {
        $profileId = Member::get('profile.id');
        $profile = collect((new Profile($profileId))->get());

        return response()->json($profile);
    }


    /**
     * @OA\Delete(
     *   path="/{api_version}/cabinet/profile",
     *   summary="удаление профиля",
     *   tags={"cabinet", "gallery"},
     *   @OA\Parameter(
     *      name="api_version",
     *      description="версия api",
     *      in="path",
     *      required=true,
     *      @OA\Schema(
     *          type="string", format="^v\d{1,}$", default="v1"
     *      )
     *   ),
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
     *      description="ошибка удаления профиля",
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
     *  @OA\Response(
     *      response=404,
     *      description="профиля не существует",
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
     *      description="профиль успешно удален"
     *    ),
     *    security={{ "bearerAuth": {} }}
     * )
     */

    /**
     * @return JsonResponse
     */
    public function deleteProfile()
    {
        $result = dispatch_now(new DeleteProfileJob($this->userId));
        return response()->json($result);
    }


    /**
     * @OA\Post(
     *   path="/{api_version}/cabinet/profile/phone/change",
     *   summary="изменение телефона (логина)",
     *   tags={"cabinet", "profile"},
     *   @OA\Parameter(
     *      name="api_version",
     *      description="версия api",
     *      in="path",
     *      required=true,
     *      @OA\Schema(
     *          type="string", format="^v\d{1,}$", default="v1"
     *      )
     *   ),
     *    @OA\Parameter(
     *      name="phone",
     *      description="Новый номер телефона",
     *      in="query",
     *      required=true,
     *      @OA\Schema(
     *        type="string", format="7\d{10}"
     *      )
     *  ),
     *  @OA\Response(
     *      response=422,
     *      description="ошибка обновления телефона",
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
     *      description="На новый номер телефона отправлен код"
     *    ),
     *    security={{ "bearerAuth": {} }}
     * )
     */
    /**
     * @param ChangePhoneRequest $request
     * @param User $userService
     * @return  JsonResponse
     */
    public function changePhone(ChangePhoneRequest $request, User $userService): JsonResponse
    {
        $phone = $request->input('phone');
        $res = $userService->changePhone($phone);
        return response()->json($res);
    }



    /**
     * @OA\Post(
     *   path="/{api_version}/cabinet/profile/phone/change/verify",
     *   summary="подтверждение изменение телефона (логина)",
     *   tags={"cabinet", "profile"},
     *   @OA\Parameter(
     *      name="api_version",
     *      description="версия api",
     *      in="path",
     *      required=true,
     *      @OA\Schema(
     *          type="string", format="^v\d{1,}$", default="v1"
     *      )
     *   ),
     *    @OA\Parameter(
     *      name="code",
     *      description="код верификации",
     *      in="query",
     *      required=true,
     *      @OA\Schema(
     *       type="integer", format="\d{4}"
     *      )
     *  ),
     * @OA\Response(
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
     *      response=422,
     *      description="ошибка обновления телефона",
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
     *      description="Номер телефона изменен"
     *    ),
     *    security={{ "bearerAuth": {} }}
     * )
     */
    /**
     * @param ChangePhoneVerifyRequest $request
     * @param User $userService
     * @return  JsonResponse
     */
    public function changePhoneVerify(ChangePhoneVerifyRequest $request, User $userService): JsonResponse
    {
        $code = $request->input('code');
        $res = $userService->changePhoneVerify($code);
        return response()->json($res);
    }


    /**
     * @OA\Post(
     *   path="/{api_version}/cabinet/profile/password/change",
     *   summary="изменение пароля",
     *   tags={"cabinet", "profile"},
     *   @OA\Parameter(
     *      name="api_version",
     *      description="версия api",
     *      in="path",
     *      required=true,
     *      @OA\Schema(
     *          type="string", format="^v\d{1,}$", default="v1"
     *      )
     *   ),
     *    @OA\Parameter(
     *      name="old_password",
     *      description="Старый пароль",
     *      in="query",
     *      required=true,
     *      @OA\Schema(
     *        type="string"
     *      )
     *  ),
     *  @OA\Response(
     *      response=422,
     *      description="ошибка обновления пароля",
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
     *      response=409,
     *      description="Неверно указан пароль",
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
     *    @OA\Response(
     *      response=200,
     *      description="запрос на изменение пароля отправлен",
     *      @OA\JsonContent(
     *         @OA\Property(
     *          property="reset_id",
     *          type="string",
     *          format="uuid",
     *          description="reset_id"
     *        ),
     *      )
     *    ),
     *    security={{ "bearerAuth": {} }}
     * )
     */

    /**
     * @param ChangePasswordRequest $request
     * @param User $userService
     * @return JsonResponse
     */
    public function changePassword(ChangePasswordRequest $request, User $userService): JsonResponse
    {
        $oldPassword = $request->input('old_password');
        $res = $userService->changePassword($oldPassword);
        return response()->json($res);
    }


    /**
     * @OA\Post(
     *   path="/{api_version}/cabinet/profile/password/change/verify",
     *   summary="изменение пароля (подтверждение)",
     *   tags={"cabinet", "profile"},
     *   @OA\Parameter(
     *      name="api_version",
     *      description="версия api",
     *      in="path",
     *      required=true,
     *      @OA\Schema(
     *          type="string", format="^v\d{1,}$", default="v1"
     *      )
     *   ),
     *    *   @OA\Parameter(
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
     *      description="ошибка обновления пароля",
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
     *      response=409,
     *      description="Неверно указан пароль",
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
     *    @OA\Response(
     *      response=200,
     *      description="Пароль обновлен!",
     *    ),
     *    security={{ "bearerAuth": {} }}
     * )
     */
    /**
     * @param ChangePasswordVerifyRequest $request
     * @param User $userService
     * @return JsonResponse
     */
    public function changePasswordVerify(ChangePasswordVerifyRequest $request, User $userService): JsonResponse
    {
        $data = $request->only(['reset_id', 'password']);
        $res = $userService->changePasswordVerify($data);
        return response()->json($res);
    }

    /**
     * @OA\Get(
     *   path="/{api_version}/cabinet/user/notifications",
     *   summary="получение списка непрочитанных уведомлений пользователя",
     *   tags={"cabinet", "user", "notification"},
     *   @OA\Parameter(
     *      name="api_version",
     *      description="версия api",
     *      in="path",
     *      required=true,
     *      @OA\Schema(
     *          type="string", format="^v\d{1,}$", default="v1"
     *      )
     *   ),
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
     *   @OA\Response(
     *      response=200,
     *      description="непрочитанные уведомления успешно получены",
     *      @OA\JsonContent(
     *        @OA\Property(
     *          property="[]",
     *          type="array",
     *          description="массив данных профиля",
     *          @OA\Items(
     *            @OA\Property(property="...", type="string")
     *          )
     *        )
     *      )
     *    ),
     *    security={{ "bearerAuth": {} }}
     * )
     */

    /**
     * @return JsonResponse
     */
    public function getUserNotifications(): JsonResponse
    {
        $limit = config('utils.show_notifications_limit');
        $notifications = array_slice(auth()->user()->unreadNotifications->toArray(), 0, $limit);
        foreach ($notifications as &$notification) {
            $notification = [
                'message' => Arr::get($notification, 'data.message'),
                'created_at' => Carbon::parse(Arr::get($notification, 'created_at'))
                    ->format(config('utils.default_date_format')),
            ];
        }

        return response()->json($notifications);
    }
}
