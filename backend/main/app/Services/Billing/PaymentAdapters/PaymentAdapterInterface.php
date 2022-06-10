<?php

namespace App\Services\Billing\PaymentAdapters;

use Illuminate\Support\Collection;

interface PaymentAdapterInterface
{
    public const TYPE_BANK_CARD = 'BANK_CARD';

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
    public function manualCharge(float $amount): Collection;

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
    public function addCard(): Collection;



    /**
     * @param string $externalCardId
     * @return bool
     */
    public function deleteCard(string $externalCardId): bool;


    /**
     * Получение платежа
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
    public function getPayment(string $id): Collection;


    /**
     * @return string
     */
    public function getName(): string;

    /**
     * @param string $currenc
     * @return void
     */
    public function setCurrency(string $currency): void;

    /**
     * @param string $description
     * @return void
     */
    public function setDescription(string $description): void;

    /**
     * @param string $type
     * @return void
     */
    public function setType(string $type): void;

    /**
     * @param bool $save
     *  @return void
     */
    public function setSaveCard(bool $save): void;


    /**
     * Установить дополнительные внутренние данные для платежа
     * @param array $data
     * @return void
     */
    public function setMetaData(array $data): void;

    /**
     * @param string $url
     *  @return void
     */
    public function setRedirectUrl(string $url): void;
}
