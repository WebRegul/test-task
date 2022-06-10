<?php

namespace App\Http\Controllers\V1\Billing;

use App\Http\Controllers\V1\Controller;
use App\Services\Billing\PaymentService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;

/**
 * Class PaymentsController
 * @package App\Http\Controllers\V1
 */
class PaymentNotificationsController extends BaseBillngController
{
    /**
     * @OA\Post(
     *   path="/{api_version}/update-payment",
     *   summary="уведомление из платежной ситемы (создание карты)",
     *   tags={"cabinet", "payment"},
     *  @OA\Response(
     *      response=400,
     *      description="ошибка",
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
     *      description="карта создана",
     *      @OA\Schema(
     *             type="boolean",
     *     ),
     *    ),
     *    security={{ "bearerAuth": {} }}
     * )
     */

    /**
     * @param paymentService $service
     * @return bool
     */
    public function updatePayment(paymentService $service): bool
    {
        $data = Arr::get(request()->all(), 'object');
        if (config('app.debug')) {
            Log::info(['object' => $data]);
        }
        return $service->updatePayment($data);
    }
}
