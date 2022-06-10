<?php

namespace App\Services\Billing\Builders;

use App\Exceptions\PreconditionFailedException;
use App\Models\Invoice;
use App\Registries\Member;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Collection;
use App\Models\Payment as PaymentModel;

/**
 * @property-read string $id
 * @property integer $status
 * @property string $invoice_id
 * @property float $amount
 * @property string $external_id
 * @property string $user_id
 * @property string $card_id
 */
class Payment
{
    public const ERRORS = [
        'UNCORRECT_AMOUNT' => 'Нельзя создать платежную транзакцию на 0 или менее рублей'
    ];

    private string $id;

    private float $bindAmount;

    protected PaymentModel $payment;

    protected ?Invoice $invoice = null;

    protected Member $member;

    protected Collection $data;

    /**
     * @var string
     */
    protected string $provider = 'yoomoney';

    public function __construct(PaymentModel $payment, ?Member $member)
    {
        $this->member = $member ?? app(Member::class);
        $this->payment = $payment;
        $this->setBindAmount(config('payments.YooMoneyProvider.default_amount'));
    }

    public function find(string $id): Payment
    {
        $this->payment = $this->payment->findOrFail($id);

        if ($this->payment->invoice_id) {
            $this->invoice = Invoice::query()->findOrFail($this->payment->invoice_id);
        }

        return $this;
    }

    public function create(Invoice $invoice): Payment
    {
        $this->payment->user_id = $invoice->user_id;
        $this->payment->provider = $this->provider;
        $this->payment->amount = $invoice->amount;
        $this->payment->status = $this->payment::STATUS_NEW;
        $this->payment->type = $this->payment::TYPE_CHARGE;
        $this->payment->invoice_id = $invoice->id;

        $this->payment->creator_id = $this->member->id;

        $this->invoice = $invoice;
        return $this;
    }

    public function createForBind(): Payment
    {
        $this->payment->user_id = $this->member->id;
        $this->payment->provider = $this->provider;
        $this->payment->status = $this->payment::STATUS_NEW;
        $this->payment->type = $this->payment::TYPE_BINDING;
        $this->payment->amount = $this->bindAmount;

        return $this;
    }

    /**
     * Перевод платежки в статус ожидания. Произовдится когда  все уже подготовлено, перед запросом  к провайдеру
     * Дальнейшие переходы возможны только из данного статуса
     * @param string $externalId
     * @return $this
     */
    public function pending(string $externalId): Payment
    {
        // @TODO добавить верификацию перехода
        $this->payment->external_id = $externalId;
        $this->payment->status = $this->payment::STATUS_PENDING;
        return $this;
    }

    /**
     * Успешное завершение платежки
     * @todo добавить событие и слушатель, чтобы закрывать через него инвойс.
     * @param string $cardId
     * @return $this
     */
    public function success(string $cardId): Payment
    {
        if($this->payment->status !== $this->payment::STATUS_PENDING){
            throw new PreconditionFailedException('Не корректный перевод статуса. Успешно завершить можно только ожидающую платежку');
        }
        $this->payment->card_id = $cardId;
        $this->payment->status = $this->payment::STATUS_SUCCESS;
        return $this;
    }

    /**
     * Закрытие платежки с ошибкой
     * @param string $reason
     * @return $this
     */
    public function failture(string $reason = ''): Payment
    {
        // @TODO добавить верификацию перехода
        $this->payment->message = $reason;
        $this->payment->status = $this->payment::STATUS_FAILTURE;
        return $this;
    }


    /**
     * @return bool
     * @throws PreconditionFailedException
     */
    public function save(): bool
    {
        $this->validate();
        return $this->payment->save();
    }



    public function get(): Collection
    {
        return collect($this->payment);
    }

    /**
     *
     * @throws PreconditionFailedException
     */
    protected function validate()
    {
        /**
         * @todo доработать логику валидации платежки перед сохранением
         * в случае провала выкидываем экзепшены
         * предназначено для устранения технических коллизий, а не для валидации данных от юзера.
         * Валидируем $this->payment
         */
        if ($this->payment->type == $this->payment::TYPE_CHARGE && !$this->invoice) {
            throw new PreconditionFailedException('Нельзя провести платеж без заведения счета');
        }
    }

    public function setInvoice()
    {
    }

    /**
     * @param float $bindAmount
     */
    public function setBindAmount(float $bindAmount)
    {
        $this->bindAmount = $bindAmount;
    }
    /**
     * перехватываем вызовы несуществующих свойств и возвращаем свойства платежа
     * @param $name
     * @return mixed
     */
    public function __get($name)
    {
        return $this->get()->get($name);
    }
}
