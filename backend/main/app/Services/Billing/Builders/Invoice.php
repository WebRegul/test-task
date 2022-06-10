<?php

namespace App\Services\Billing\Builders;

use App\Exceptions\PreconditionFailedException;
use App\Registries\Member;
use App\Models\Invoice as InvoiceModel;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Tymon\JWTAuth\Exceptions\UserNotDefinedException;
use App\Services\Billing\Builders\Payment;

/**
 * @property-read string $id
 * @property string $user_id
 * @property string $tariff_id
 * @property string $tariff_period
 * @property float $amount Сумма оплаты по выбранному тарифу с учетом периода
 * @property int $status
 */
class Invoice
{
    /**
     * @var InvoiceModel
     */
    protected InvoiceModel $invoice;

    /**
     * @var Member
     */
    protected Member $member;

    /**
     * @var ?Tariff
     */
    protected ?Tariff $tariff = null;

    protected Payment $payment;

    public function __construct(Member $member, InvoiceModel $invoice, Payment $payment)
    {
        $this->member = $member;
        $this->invoice = $invoice;
        $this->payment = $payment;
    }

    /**
     * @param string $id
     * @return Invoice
     * @throws UserNotDefinedException
     * @throws ModelNotFoundException
     */
    public function find(string $id): Invoice
    {
        $this->invoice = $this->invoice->findOrFail($id);

        $this->tariff = new Tariff($this->invoice->tariff_id, $this->invoice->tariff_period, $this->member);

        return $this;
    }

    /**
     * @param Tariff $tariff
     * @return $this
     * @throws Exception
     */
    public function create(Tariff $tariff): Invoice
    {
        $this->tariff = $tariff;

        $pending = $this->getPendingInvoice();
        if ($pending->isNotEmpty()) {
            /**
             * @TODO вынести в отдельный экзепшен и подумать над кейсами, может быть стоит просто использовать его вместо создания нового
             */
            //throw new \Exception('Уже имеется ожидающий платежа счет с данным тарифом и периодом '.$pending->get('id'), 422);
            // наверное все же использовать... или игнорить.. пока создается новый в любом случае. обдумать кейсы
        }

        $this->invoice->amount = $this->tariff->getAmount();
        $this->invoice->tariff_id = $this->tariff->id;
        $this->invoice->tariff_period = $this->tariff->period;
        $this->invoice->user_id = $this->member->id;
        $this->invoice->status = $this->invoice::STATUS_NEW;
        $this->invoice->creator_id = $this->member->id;
        return $this;
    }

    /**
     * Инициация начала проведения платежа. Создаем платежку и переводим счет в режим ожидания
     * @throws PreconditionFailedException
     */
    public function charge()
    {
        if (!$this->invoice || !$this->invoice->amount || !$this->invoice->id) {
            throw new PreconditionFailedException('Для проведения платежа счет должен быть создан и сохранен');
        }
        if ($this->invoice->status > $this->invoice::STATUS_NEW) {
            throw new PreconditionFailedException('Платежку можно создать только для нового счета');
        }
        $this->payment->create($this->invoice)->save();

        $this->pending();
        $this->save();
    }

    /**
     * @throws PreconditionFailedException
     */
    public function pending()
    {
        if ($this->invoice->status !== $this->invoice::STATUS_NEW) {
            throw new PreconditionFailedException('Перевести в ожидание можно только новый счет');
        }
        if (!$this->payment->id && !$this->isFreeInvoice()) {
            throw new PreconditionFailedException('Перевести в ожидание без платежки можно только счет по бесплатному тарифу');
        }
        $this->invoice->status = $this->invoice::STATUS_PENDING;
    }

    public function waiting()
    {
    }

    /**
     * @throws PreconditionFailedException
     */
    public function success()
    {
        if ($this->invoice->status !== $this->invoice::STATUS_PENDING) {
            throw new PreconditionFailedException('Успешно завершен может быть только ожидающий счет');
        }
        $this->invoice->status = $this->invoice::STATUS_SUCCESS;
    }

    /**
     * Сохранение данных счета в БД
     * @return bool
     */
    public function save(): bool
    {
        return $this->invoice->save();
    }

    /**
     * @return bool
     */
    protected function isFreeInvoice(): bool
    {
        return ($this->tariff->isFree() && !($this->amount > 0));
    }

    /**
     * @return Collection
     */
    protected function getPendingInvoice(): Collection
    {
        $invoice = $this->invoice->getQuery()->where([
            'user_id' => $this->member->id,
            'status' => $this->invoice::STATUS_PENDING,
            'tariff_id' => $this->tariff->id,
            'tariff_period' => $this->tariff->period
        ])->first();

        return collect($invoice);
    }

    public function setTariff(string $id, string $period)
    {
        $this->invoice->tariff_id = $id;
        $this->invoice->tariff_period = $period;
    }

    /**
     * @return Tariff|null
     */
    public function tariff(): ?Tariff
    {
        return $this->tariff;
    }

    /**
     * @return Collection
     */
    public function invoice(): Collection
    {
        return collect($this->invoice);
    }

    /**
     * @return Payment
     */
    public function payment(): Payment
    {
        return $this->payment;
    }

    /**
     * перехватываем вызовы несуществующих свойств и возвращаем свойства счета
     * @param $name
     * @return mixed
     */
    public function __get($name)
    {
        return $this->invoice()->get($name);
    }
}
