<?php

namespace App\Services\Sms\Adapters;

/**
 * Interface SmsAdapterInterface
 * @package App\Services\Sms\Adapters
 */
interface SmsAdapterInterface
{
    /**
     * @param string $phone
     * @param string $message
     * @return bool
     * @throws \Exception
     */
    public function sendSms(string $phone, string $message): bool;
}
