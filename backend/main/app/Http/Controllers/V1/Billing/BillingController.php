<?php

namespace App\Http\Controllers\V1\Billing;

use App\Exceptions\PreconditionFailedException;
use App\Http\Requests\Cabinet\AutoRenewalRequest;
use App\Registries\Member;
use App\Services\Billing\BillingService;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

use Illuminate\Support\Arr;
use Tymon\JWTAuth\Exceptions\UserNotDefinedException;

/**
 * Class PaymentsController
 * @package App\Http\Controllers\V1
 */
class BillingController extends BaseBillngController
{
    protected $userId;

    protected Member $member;

    protected BillingService $service;

    /**
     * @throws AuthorizationException
     */
    public function __construct(BillingService $service, Member $member)
    {
        $this->member = $member;
        $this->service = $service;
        $this->userId = $this->member->get('id');
        if (!$this->userId) {
            throw new AuthorizationException('Метод доступен только авторизованным пользователям', 401);
        }
    }


    /**
     * @OA\Post(
     *   path="/{api_version}/cabinet/billing/set-tariff/{tariff_id}",
     *   summary="обновление тарифа пользователя",
     *   tags={"cabinet", "payment"},
     *   @OA\Parameter(
     *      name="api_version",
     *      description="версия api",
     *      in="path",
     *      required=true,
     *      @OA\Schema(
     *          type="string", format="^v\d{1,}$", default="v1"
     *      )
     *   ),
     *     @OA\Parameter(
     *      name="tariff_id",
     *      description="идентификатор тарифа",
     *      in="path",
     *      required=true,
     *      @OA\Schema(
     *          type="string", format="uuid"
     *      ),
     * ),
     *   @OA\Parameter(
     *      name="period",
     *      description="период",
     *      in="query",
     *      required=true,
     *      @OA\Schema(
     *           type="string", enum={"month", "year"}
     *      ),
     * ),
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
     *      description="тариф пользователя обновлен",
     *      @OA\JsonContent(
     *        @OA\Property(
     *          property="sections[]",
     *          type="array",
     *          description="массив карт",
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
     * Обновление тарифа пользователя
     * @param Request $request
     * @param string $id - идентификатор тарифа
     * @return JsonResponse
     * @throws PreconditionFailedException
     * @throws UserNotDefinedException
     */
    public function setTariff(Request $request, string $id): JsonResponse
    {
        $period = $request->get('period');
        $invoice = $this->service->setTariff($id, $period);
        return response()->json($invoice);
    }


    /**
     * @OA\Get(
     *   path="/{api_version}/cabinet/billing/cards",
     *   summary="получние карт пользователя",
     *   tags={"cabinet", "payment"},
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
     *      description="список карт пользоателя успешно получен",
     *      @OA\JsonContent(
     *        @OA\Property(
     *          property="sections[]",
     *          type="array",
     *          description="массив карт",
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
     * получение привязанных карт пользователя
     * @param Request $request
     * @return JsonResponse
     */
    public function getCards()
    {
        $cards = $this->service->getCards();
        return response()->json($cards);
    }


    /**
     * @OA\Put(
     *   path="/{api_version}/cabinet/billing/auto-renewal",
     *   summary="обновление автопродления профиля",
     *   tags={"cabinet", "payment"},
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
     *      name="auto",
     *      description="флаг автопродления",
     *      in="query",
     *      required=true,
     *      @OA\Schema(
     *          type="boolean"
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
     * @param AutoRenewalRequest $request
     * @return JsonResponse
     */
    public function autoRenewal(AutoRenewalRequest $request): JsonResponse
    {
        $auto = filter_var($request->get('auto'), FILTER_VALIDATE_BOOLEAN);
        $result = $this->service->autoRenewal($auto);
        return response()->json($result);
    }


    /**
     * @OA\Get(
     *   path="/{api_version}/cabinet/billing/payment/history",
     *   summary="история платежей",
     *   tags={"cabinet", "payment"},
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
     *      description="история платежей успешно получена",
     *      @OA\JsonContent(
     *        @OA\Property(
     *          property="sections[]",
     *          type="array",
     *          description="массив карт",
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
     */
    public function getPaymentHistory(): JsonResponse
    {
        $result = $this->service->getPaymentHistory();
        return response()->json($result);
    }
}
