<?php

namespace App\Http\Controllers\V1\Billing;

use App\Facades\Member;
use App\Http\Controllers\V1\Controller;
use App\Http\Requests\Payment\CheckCardRequest;
use App\Services\Billing\PaymentService;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

use Illuminate\Support\Arr;
use YooKassa\Model\PaymentStatus;

/**
 * Class PaymentsController
 * @package App\Http\Controllers\V1
 */
class PaymentsController extends BaseBillngController
{
    protected $userId;

    protected PaymentService $service;

    /**
     * @throws AuthorizationException
     */
    public function __construct(PaymentService $service)
    {
        $this->userId = Member::get('id');
        if (!$this->userId) {
            throw new AuthorizationException('Метод доступен только авторизованным пользователям');
        }
        $this->service = $service;
    }


    /**
     * @OA\Post(
     *   path="/{api_version}/cabinet/billing/card",
     *   summary="добавление карты",
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
     *  @OA\Response(
     *      response=400,
     *      description="ошибка при сохранении платежа",
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
     *      description="карта успешно добавлена",
     *      @OA\JsonContent(
     *        @OA\Property(
     *          property="[]",
     *          type="object",
     *          description="массив данных карты"
     *        )
     *      )
     *    ),
     *    security={{ "bearerAuth": {} }}
     * )
     */

    /**
     * Добавление карт
     * @return string
     */
    public function addCard(): JsonResponse
    {
        return response()->json($this->service->startBinding());
    }



    /**
     * @OA\Post(
     *   path="/{api_version}/cabinet/check-card/{payment_id}",
     *   summary="проверка карты",
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
     *      name="payment_id",
     *      description="идентификатор платежки",
     *      in="path",
     *      required=true,
     *      @OA\Schema(
     *          type="string", format="uuid"
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
     *      description="статус карты",
     *      @OA\JsonContent(
     *        @OA\Property(
     *          property="[]",
     *          type="object",
     *          description="массив данных тарифа"
     *        )
     *      )
     *    ),
     *    security={{ "bearerAuth": {} }}
     * )
     */
    /**
     * @param string $payment_id
     * @return JsonResponse
     */
    public function checkCard(CheckCardRequest $request): JsonResponse
    {
        $res = $this->service->checkStatus($request->payment_id);
        return response()->json($res, Arr::get($res, 'code', 200));
    }



    /**
     * @OA\Get(
     *   path="/{api_version}/cabinet/payment/check-status/{id}",
     *   summary="проверка статуса платежки",
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
     *      name="id",
     *      description="идентификатор платежки",
     *      in="path",
     *      required=true,
     *      @OA\Schema(
     *          type="string", format="uuid"
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
     *      description="статус карты",
     *      @OA\JsonContent(
     *        @OA\Property(
     *          property="[]",
     *          type="object",
     *          description="массив данных платежки"
     *        )
     *      )
     *    ),
     *    security={{ "bearerAuth": {} }}
     * )
     */
    /**
     * @param Request $request
     * @param string $paymentId
     * @return JsonResponse
     */
    public function checkPaymentStatus(string $id): JsonResponse
    {
        $data = $this->service->checkStatus($id);
        return response()->json($data, 200);
    }



    /**
     * @OA\Delete(
     *   path="/{api_version}/cabinet/billing/card/{card_id}",
     *   summary="удаление карты пользователя",
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
     *    *@OA\Parameter(
     *      name="card_id",
     *      description="идентификатор карты",
     *      in="path",
     *      required=true,
     *      @OA\Schema(
     *          type="string", format="uuid"
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
     *      description="карта успешно удалена"
     *    ),
     *    security={{ "bearerAuth": {} }}
     * )
     */

    /**
     * @param Request $request
     * @param string $cardId
     * @return boolean
     */
    public function deleteCard(Request $request, string $cardId)
    {
        return response()->json(['status' => $this->service->deleteCard($cardId)]);
    }
}
