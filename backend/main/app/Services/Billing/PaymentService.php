<?php

namespace App\Services\Billing;

use App\Events\UpdateInvoiceEvent;
use App\Exceptions\ForbiddenException;
use App\Exceptions\PreconditionFailedException;
use App\Models\UserCard;
use App\Registries\Member;
use App\Services\Billing\Builders\Invoice;
use App\Services\Billing\Builders\Payment;
use App\Services\Billing\PaymentAdapters\PaymentAdapterInterface;
use App\Services\User;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\QueryException;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Registries\Member as MemberRegistry;
use App\Models\User as UserModel;
use Illuminate\Support\ItemNotFoundException;
use Tymon\JWTAuth\Exceptions\UserNotDefinedException;

class PaymentService
{
    protected PaymentAdapterInterface $adapter;
    protected Payment $payment;
    private Member $member;
    private Invoice $invoice;
    private $billing;

    /**
     * PaymentService constructor.
     * @param PaymentAdapterInterface $adapter
     * @param Member $member
     */
    public function __construct(
        PaymentAdapterInterface $adapter,
        Payment                 $payment,
        Invoice                 $invoice,
        Member                  $member
    ) {
        $this->adapter = $adapter;
        $this->member = $member;
        $this->payment = $payment;
        $this->invoice = $invoice;
        $this->adapter->setMetaData(['user_id' => $this->member->id]);
    }

    /**
     * @param Payment $payment
     * @return Collection
     * @throws PreconditionFailedException
     */
    public function start(Payment $payment): Collection
    {
        $this->payment = $payment;
        $this->adapter->setMetaData(['invoice_id' => $payment->invoice_id, 'payment_id' => $payment->id]);

        $data = $this->adapter->manualCharge($payment->amount);
        $payment->pending($data->get('id'));
        // @TODO добавить проверку суммы и статуса вернувшихся от адаптера
        $payment->save();
        return collect(['url' => $data->get('url'), 'payment' => $payment->get(), 'external' => $data]);
    }

    /**
     * @param ?Payment $payment = null
     * @return Collection
     * @throws PreconditionFailedException
     */
    public function startBinding(?Payment $payment = null): Collection
    {
        $this->payment = $payment ?? $this->payment->createForBind();
        if (!$payment) {
            $this->payment->save();
        }
        $this->adapter->setMetaData(['payment_id' => $this->payment->id]);
        $data = $this->adapter->addCard();
        $this->payment->pending($data->get('id'));
        $this->payment->amount = $data->get('amount');
        // @TODO добавить проверку суммы и статуса вернувшихся от адаптера
        $this->payment->save();
        Log::info('getMetaData startBinding', ['meta' => $this->adapter->getMetaData()]);
        return collect(['url' => $data->get('url'), 'payment' => $this->payment->get(), 'external' => $data]);
    }


    /**
     * @throws UserNotDefinedException|PreconditionFailedException
     */
    public function checkStatus(string $paymentId): Collection
    {
        $this->payment->find($paymentId);

        if (empty(UserModel::query()->find($this->payment->user_id))) {
            throw new UserNotDefinedException("пользователь {$this->payment->user_id} не существует");
        }

        $external = $this->adapter->getPayment($this->payment->external_id);
        $status = $external->get('status');
        $card = collect([]);
        if ($this->payment->invoice_id) { //если платеж по счету, то закрываем счет
            $this->invoice->find($this->payment->invoice_id);

            if ($this->invoice->tariff()->get()->isEmpty()) {// если платеж был по счету, то тариф обязателен
                throw new ItemNotFoundException("тариф {$this->invoice->tariff_id} не существует");
            }
        }

        /**
         * @todo вынести маппинг на уровень адаптера. тут использовать внутренние статусы
         */
        switch ($status) {
            case 'succeeded':
                if ($this->payment->status === 1) {
                    $card = UserCard::query()->find($this->payment->card_id);
                    break; //если платеж уже исполнен достаем карту и выходим
                }
                /** @todo добавить валидацию данных платежа */
                DB::transaction(function () use ($external, &$card) {
                    $data = collect($external->pull('detail.payment_method'));
                    $card = collect($this->createCard($data));
                    $this->payment->success($card->get('id'));
                    $this->payment->save();
                    /** @todo перенести в событие и пушить его из payment->success */
                    if ($this->invoice->id) { //если платеж по счету, то закрываем счет
                        $this->invoice->success();
                        $this->invoice->save();
                        Log::info('status invoice success');
                        event(new UpdateInvoiceEvent($this->invoice));
                    }
                });
                break;
            case 'waiting_for_capture':
                //ожидает подтверждения системы после холдирования. для двухстадийки, не используем
                break;
            case 'pending':
                //ждемс...
                break;
            case 'canceled':
                $this->failture($external->pull('detail.cancellation_details.reason'));
                break;
            default:
                break;
        }
        $result = $this->payment->get();
        $result->put('external', $external);
        $result->put('card', $card);
        if ($this->invoice->id) {
            $result->put('tariff', $this->invoice->tariff()->get());
            $result->put('invoice', $this->invoice->invoice());
        }
        return $result;
    }

    /**
     * @param string $reason
     * @return bool
     * @throws PreconditionFailedException
     */
    public function failture(string $reason = ''): bool
    {
        return $this->payment->failture($reason)->save();
    }

    /**
     * @param Collection $data
     * @return Builder|Model
     */
    public function createCard(Collection $data)
    {
        //@TODO не надо завязываться на ключи провайдера. поменять в БД на external_id
        try {
            $card = UserCard::query()->where([
                'external_id' => $data->get('id'),
                'user_id' => $this->payment->user_id
            ])->first();
            if (!$card) {
                $card = UserCard::query()->create([
                    'user_id' => $this->payment->user_id,
                    'external_id' => $data->get('id'),
                    'card_data' => $data->except(['id', 'saved', 'title']),
                    'status' => 1
                ]);
            }
        } catch (QueryException $exception) {
            throw $exception; //@todo подумать над обработкой
        } catch (\Exception $exception) {
            throw $exception;
        }
        return $card;
    }


    /**
     * @param string $cardId
     * @return bool
     */
    public function deleteCard(string $cardId): bool
    {
        $card = UserCard::findOrFail($cardId);
        if ($card->user_id !== $this->member->id) {
            throw new ForbiddenException('Card is not having for this user', 403);
        }
        if ($this->adapter->deleteCard($cardId)) {
            return (bool)$card->delete();
        }
        return false;
    }

    /**
     * @return bool
     */
    public function updatePayment($data): bool
    {
        return DB::transaction(function () use ($data) {
            try {
                $cardData = Arr::get($data, 'payment_method.card');
                $paymentId = Arr::get($data, 'metadata.payment_id');
                $userId = Arr::get($data, 'metadata.user_id');
                $amount = Arr::get($data, 'amount');

                if ($paymentId && is_array($cardData)) {
                    $userService = new User($userId);
                    $userService->setMember(app(MemberRegistry::class));
                    $this->checkStatus($paymentId);
                    Log::info(['method' => 'updatePayment', 'cashboxId' => $paymentId, 'amount' => $amount, 'succeess' => true]);
                    $result = true;
                } else {
                    Log::info(['method' => 'updatePayment', 'cashboxId' => $paymentId, 'amount' => $amount, 'succeess' => false]);
                    $result = false;
                }
                return $result;
            } catch (Exception $e) {
                Log::info(['method' => 'updatePayment', 'error' => $e->getMessage(), 'succeess' => false]);
                if (config('app.debug')) {
                    Log::info(['error' => $e]);
                }
                throw $e;
            }
        });
    }
}
