<?php

namespace App\Services\Sms\Adapters;

class FakeAdapter implements SmsAdapterInterface
{
    /**
     * @param string $phone
     * @param string $message
     * @return bool
     * @throws \Exception
     */
    public function sendSms(string $phone, string $message): bool
    {
        return true;
    }
}
