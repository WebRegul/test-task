<?php

namespace App\Services\Billing\PaymentAdapters;

use App\Exceptions\NotAcceptable;
use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use YooKassa\Client;
use YooKassa\Common\Exceptions\ApiException;
use YooKassa\Common\Exceptions\BadApiRequestException;
use YooKassa\Common\Exceptions\ExtensionNotFoundException;
use YooKassa\Common\Exceptions\ForbiddenException;
use YooKassa\Common\Exceptions\InternalServerError;
use YooKassa\Common\Exceptions\NotFoundException;
use YooKassa\Common\Exceptions\ResponseProcessingException;
use YooKassa\Common\Exceptions\TooManyRequestsException;
use YooKassa\Common\Exceptions\UnauthorizedException;
use YooKassa\Model\PaymentMethodType;
use YooKassa\Request\Payments\CreatePaymentRequest;
use YooKassa\Request\Refunds\CreateRefundResponse;

/**
 * @see https://yookassa.ru/developers
 * Адаптер интеграции с YooKassa
 */
class YooMoneyAdapter implements PaymentAdapterInterface
{
    protected $id;
    //Секретный ключ кассы кассы
    protected $key;
    //объект SDK yoomoney
    protected Client $client;

    /**
     * @var string
     */
    protected string $name = 'yoomoney';

    /**
     * @var float
     */
    private $defaultAmount;
    /**
     * дополнительные данные о платеже, вернутся в неизменном виде в уведомлении
     * @var ?array
     */
    protected ?array $meta = [];

    /**
     * Константы возможных типов платежей
     * @var array
     */
    protected array $types = [
        self::TYPE_BANK_CARD => PaymentMethodType::BANK_CARD
    ];

    /**
     * @var string
     */
    protected string $currency;

    /**
     * тип платежа
     * @var string
     */
    protected string $type = '';

    /**
     * описание платежа
     * @var string
     */
    protected string $description = '';

    /**
     * описание платежа
     * @var bool
     */
    protected bool $saveCard = true;

    /*
     * @var string
     */
    protected string $redirectUrl;

    /**
     * YooMoneyAdapter constructor.
     * @param string|null $id
     * @param string|null $key
     */
    public function __construct(?string $id = null, ?string $key = null)
    {
        $this->id = $id ?? config('payments.YooMoneyProvider.id');
        $this->key = $key ?? config('payments.YooMoneyProvider.key');
        $this->client = new Client();
        $this->client->setAuth($this->id, $this->key);
        $this->defaultAmount = config('payments.YooMoneyProvider.default_amount');
        $this->type = $this->types[static::TYPE_BANK_CARD];
        $this->description =  config('payments.YooMoneyProvider.default_description');
        $this->redirectUrl = config('payments.YooMoneyProvider.redirect_url');
        $this->currency = \YooKassa\Model\CurrencyCode::RUB;
        $this->meta = [];
    }

    /**
     * создание платежа
     * @param float $amount
     * @param string $cardId
     * @param string $currency
     * @return Collection
     * @throws NotFoundException
     * @throws ResponseProcessingException
     * @throws ApiException
     * @throws ExtensionNotFoundException
     * @throws BadApiRequestException
     * @throws InternalServerError
     * @throws ForbiddenException
     * @throws TooManyRequestsException
     * @throws UnauthorizedException
     */
    public function manualCharge(float $amount): Collection
    {
        try {
            return $this->createPayment($amount);
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Добавление карты (создание платежа на 1 руб)
     * @return Collection
     * @throws ApiException
     * @throws BadApiRequestException
     * @throws ExtensionNotFoundException
     * @throws ForbiddenException
     * @throws InternalServerError
     * @throws NotFoundException
     * @throws ResponseProcessingException
     * @throws TooManyRequestsException
     * @throws UnauthorizedException
     */
    public function addCard(): Collection
    {
        try {
            return $this->getAddCardLink();
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * заглушка. в случае с юманей карта удаляется только локалько, этого достаточно
     * @param string $externaCardId
     * @return bool
     */
    public function deleteCard(string $externaCardId): bool
    {
        return true;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $currenc
     * @return void
     */
    public function setCurrency(string $currency): void
    {
        $this->currency = $currency;
    }

    /**
     * @param string $description
     * @return void
     */
    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    /**
     * @param string $type
     * @return void
     */
    public function setType(string $type): void
    {
        $this->type = $type;
    }

    /**
     * @param bool $save
     *  @return void
     */
    public function setSaveCard(bool $save): void
    {
        $this->saveCard = $save;
    }

    /**
     * @param string $url
     *  @return void
     */
    public function setRedirectUrl(string $url): void
    {
        $this->saveCard = $url;
    }




    /**
     * Установить дополнительные внутренние данные для платежа
     * @param array $data
     * @return void
     */
    public function setMetaData(array $data): void
    {
        $this->meta = array_merge($this->meta, $data);
    }

    /**
     * @return array
     */
    public function getMetaData(): array
    {
        return $this->meta;
    }





    /**
     * Добавление карты (создание платежа на 1 руб)
     * @param string $userCardId
     * @return Collection
     * @throws ApiException
     * @throws BadApiRequestException
     * @throws ExtensionNotFoundException
     * @throws ForbiddenException
     * @throws InternalServerError
     * @throws NotFoundException
     * @throws ResponseProcessingException
     * @throws TooManyRequestsException
     * @throws UnauthorizedException
     */
    private function getAddCardLink(): Collection
    {
        try {
            return  $this->createPayment(
                $this->defaultAmount,
                'bank_card',
            );
        } catch (Exception $e) {
            throw $e;
        }
    }


    /**
     *  @see https://github.com/yoomoney/yookassa-sdk-php/blob/master/docs/examples/02-payments.md#%D0%97%D0%B0%D0%BF%D1%80%D0%BE%D1%81-%D0%BD%D0%B0-%D1%81%D0%BE%D0%B7%D0%B4%D0%B0%D0%BD%D0%B8%D0%B5-%D0%BF%D0%BB%D0%B0%D1%82%D0%B5%D0%B6%D0%B0-%D1%87%D0%B5%D1%80%D0%B5%D0%B7-%D0%B1%D0%B8%D0%BB%D0%B4%D0%B5%D1%80
     * @param float $amount
     * @param string $cardId
     * @param string $currency
     * @return Collection
     * @throws ApiException
     * @throws BadApiRequestException
     * @throws ExtensionNotFoundException
     * @throws ForbiddenException
     * @throws InternalServerError
     * @throws NotFoundException
     * @throws ResponseProcessingException
     * @throws TooManyRequestsException
     * @throws UnauthorizedException
     */
    private function createPayment(float $amount, ?string $type = null): Collection
    {
        if ($amount <= 0) {
            throw new NotAcceptable('Сумма оплаты должна быть больше 0');
        }
        $type = $type ?? $this->type;

        try {
            Log::info('getMetaData createPayment', ['meta' => $this->getMetaData()]);
            $builder =  CreatePaymentRequest::builder();
            $builder->setAmount($amount)
                ->setCurrency($this->currency)
                ->setCapture(true)
                ->setDescription($this->description)
                ->setMetadata($this->getMetaData());

            // Устанавливаем страницу для редиректа после оплаты
            $builder->setConfirmation(array(
                'type'      => \YooKassa\Model\ConfirmationType::REDIRECT,
                'returnUrl' => $this->redirectUrl,
            ));
            $builder->setPaymentMethodData($type);
            $builder->setSavePaymentMethod($this->saveCard);
            $request = $builder->build();
            $response = $this->client->createPayment($request);
            $confirmationUrl = $response->getConfirmation()->getConfirmationUrl();
            return  collect([
                'url' => $confirmationUrl,
                'id' => $response->id,
                'amount' =>  $amount,
                'status' => $response->status,
                'detail' => $response->toArray() //temp for debug
            ]);
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Получение платежа
     * @deprecated
     * @param string $id
     * @return Collection
     * @throws ApiException
     * @throws BadApiRequestException
     * @throws ExtensionNotFoundException
     * @throws ForbiddenException
     * @throws InternalServerError
     * @throws NotFoundException
     * @throws ResponseProcessingException
     * @throws TooManyRequestsException
     * @throws UnauthorizedException
     */
    public function getPayment(string $id): Collection
    {
        try {
            $payment = $this->client->getPaymentInfo($id);
        } catch (Exception $e) {
            return $e;
        }
        return collect([
            'status' => $payment->getStatus(),
            'value' => $payment->getAmount()->value,
            'id' => $id,
            'detail' => $payment->toArray() //temp for debug
        ]);
    }

    /**
     *  @deprecated
     * @param string $id
     * @param float $amount
     * @return CreateCaptureResponse
     */
    public function capturePayment(string $id, float $amount): ?\YooKassa\Request\Payments\Payment\CreateCaptureResponse
    {
        $response = $this->client->capturePayment(
            [
                'amount' => [
                    'value' =>  $amount,
                    'currency' => $this->currency,
                ],
            ],
            $id,
            uniqid('', true)
        );
        return $response;
    }

    /**
     * возвращает платёж обратно
     * @deprecated
     * @param string $id
     * @param float $value
     * @throws ApiException
     * @throws BadApiRequestException
     * @throws ExtensionNotFoundException
     * @throws ForbiddenException
     * @throws InternalServerError
     * @throws NotFoundException
     * @throws ResponseProcessingException
     * @throws TooManyRequestsException
     * @throws UnauthorizedException
     */
    public function refundPayment(string $id, float $value): CreateRefundResponse
    {
        return $this->client->createRefund(
            [
                'amount' => [
                    'value' => $value,
                    'currency' => $this->currency,
                ],
                'payment_id' => $id,
            ],
            uniqid('', true)
        );
    }

    /**
     * @deprecated
     * @param float $amount
     * @param string|null $currency
     * @param string $externaCardId
     * @return array
     * @throws NotFoundException
     * @throws ResponseProcessingException
     * @throws ApiException
     * @throws ExtensionNotFoundException
     * @throws BadApiRequestException
     * @throws InternalServerError
     * @throws ForbiddenException
     * @throws TooManyRequestsException
     * @throws UnauthorizedException
     */
    public function cardCharge(string $externaCardId, float $amount): array
    {
        try {
            return $this->createAutoPay($externaCardId, $amount);
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * @param float $amount
     * @param string|null $currency
     * @param string $externaCardId
     * @return array
     */
    private function createAutoPay(string $externaCardId, float $amount): array
    {
        try {
            $builder =  CreatePaymentRequest::builder();
            $builder->setAmount($amount)
                ->setCurrency($this->currency)
                ->setCapture(true)
                ->setDescription($this->description);
            $builder->setSavePaymentMethod($this->saveCard);
            $builder->setPaymentMethodId($externaCardId);
            $request = $builder->build();
            $response = $this->client->createPayment($request);
            return $response->toArray();
        } catch (Exception $e) {
            throw $e;
        }
    }
}
