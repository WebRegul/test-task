<?php

namespace App\Services\Billing;

use App\Exceptions\PreconditionFailedException;
use App\Models\Invoice as ModelsInvoice;
use App\Models\Payment;
use App\Models\Profile;
use App\Models\Tariff as TariffModel;
use App\Models\UserCard;
use App\Services\Billing\Builders\Invoice;
use App\Registries\Member;
use Illuminate\Support\Collection;
use App\Services\Billing\Builders\Tariff;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;
use Throwable;
use Tymon\JWTAuth\Exceptions\UserNotDefinedException;
use Illuminate\Support\Facades\DB;

class BillingService
{
    /**
     * @var Member
     */
    protected Member $member;

    /**
     * @var Invoice
     */
    protected Invoice $invoice;

    /**
     * @var Tariff
     */
    protected Tariff $tariff;
    private PaymentService $service;


    /**
     * @param Member $member
     * @param Invoice $invoice
     */
    public function __construct(Invoice $invoice, PaymentService $service, Member $member)
    {
        $this->member = $member; //всегда работаем по конкретному юзеру
        $this->invoice = $invoice; //и с конкретным инвойсом
        $this->service = $service;
    }

    /**
     * @param string $id
     * @param string|null $period
     * @return Collection
     * @throws UserNotDefinedException
     * @throws PreconditionFailedException
     */
    public function setTariff(string $id, ?string $period = 'month'): Collection
    {
        $this->tariff = new Tariff($id, $period);
        $this->tariff->verify(); // проверяем возможность смены тарифа

        $invoice = collect([]);

        DB::transaction(function () use (&$invoice) {
            $this->invoice->create($this->tariff)->save(); //создаем счет на оплату
            if ($this->tariff->isFree()) { //если тариф бесплатный доводим счет до финала и апаем тариф юзеру
                $this->invoice->pending();
                $this->invoice->success();
                $this->invoice->save();
                $this->updateProfileTariff();
                $invoice = $this->invoice->invoice();
            } else {
                $this->invoice->charge(); //инициируем оплату счета и создаем платежку
                $data = $this->service->start($this->invoice->payment());
                $invoice = $this->invoice->invoice();
                $invoice->put('url', $data->get('url'));
                $invoice->put('payment', $data->get('payment'));
                $invoice->put('external', $data->get('external'));
            }
        });

        return $invoice;
    }

    /**
     * @param Invoice $invoice
     * @return $this
     */
    public function setInvoiceBuilder(Invoice $invoice): BillingService
    {
        $this->invoice = $invoice;
        return $this;
    }

    /**
     * @param Tariff $tariff
     * @return $this
     */
    public function setTariffBuilder(Tariff $tariff): BillingService
    {
        $this->tariff = $tariff;
        return $this;
    }

    /**
     * @return void
     * @throws PreconditionFailedException|Throwable
     */
    public function updateProfileTariff(): void
    {
        throw_if(
            (!$this->invoice->status == \App\Models\Invoice::STATUS_SUCCESS),
            new PreconditionFailedException('Нельзя обновить тариф с неуспешным счетом')
        );

        /**
         * @var Profile
         */
        $profile = Profile::query()->where('user_id', $this->invoice->user_id)->firstOrFail();
        $profile->tariff_id = $this->invoice->tariff_id;
        $profile->tariff_period = $this->invoice->tariff_period;
        $profile->tariff_finished_at = $this->tariff->getFinishDate();
        $profile->save();
        Log::info('user tariff updated', ['user' => $this->invoice->user_id, 'tariff' => $this->invoice->tariff_id]);
    }

    /**
     * Получение привязанных карт пользователя
     * @return Collection
     */
    public function getCards(): Collection
    {
        $cards = UserCard::query()
            ->where('user_id', $this->member->get('id'))
            ->where('status', 1)
            ->latest()
            ->get();

        return collect($cards);
    }

    /**
     * создание счета на пролонгацию
     * @return Invoice
     */
    public function createProlongation(): Invoice
    {
        $profile = Profile::where('user_id', $this->member->id)->first();
        $tariff = new Tariff($profile->tariff_id, $profile->tariff_period);
        $this->invoice->create($tariff)->save();
        return $this->invoice;
    }

    /**
     * инициализация оплаты счета и создание платежки
     * @param Invoice $invoice
     * @return Collection
     */
    public function prolongationCharge(Invoice $invoice): Collection
    {
        $invoice->charge();
        return $this->service->start($invoice->payment());
    }

    /**
     * включение/выключение автоплатежа
     * @param string $userId
     * @param boolean $auto
     * @return Collection
     */
    public function autoRenewal(bool $auto): Collection
    {
        $profile = Profile::where('user_id', $this->member->id)->first();
        $profile->auto_renewal = $auto;
        $profile->save();
        return collect($profile);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection
     */
    public function getTariffs()
    {
        return TariffModel::query()->orderBy('size')->get();
    }

    /**
     * @return Collection
     */
    public function getPaymentHistory(): Collection
    {
        $invoices = ModelsInvoice::where('user_id', $this->member->id)->where('status', ModelsInvoice::STATUS_SUCCESS)->get();
        $res = collect();
        $invoices->each(function ($e) use (&$res) {
            $result = collect();
            $payment = Payment::with('card')->where('status', 1)->where('invoice_id', $e->id)->orderBy('updated_at', 'desc')->first();
            if (!empty($payment) && $payment->card) {
                $cardData = $payment->card->card_data;
                if (empty($cardData)) {
                    throw new PreconditionFailedException('Нет данных карты');
                }
                $invoice = collect();
                $cartData = collect();

                $result->put('invoice_id', $e->id);
                $result->put('payment_id', $payment->id);
                $result->put('amount', $e->amount);

                $invoice->put('updated_at', $e->updated_at->toDateString());
                $invoice->put('amount', $e->amount);
                $cartData->put('last4', Arr::get($cardData, 'last4'));

                $result = $result->merge(['invoice' => $invoice]);
                $result = $result->merge(['card_data' => $cartData]);

                $res = $res->add($result);
            }

            return $res;
        });
        return $res;
    }
}
