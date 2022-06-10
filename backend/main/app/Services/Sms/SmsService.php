<?php

namespace App\Services\Sms;

use App\Services\Sms\Adapters\SmsAdapterInterface;

class SmsService
{
    /**
     * @var SmsAdapterInterface
     */
    protected SmsAdapterInterface $adapter;

    /**
     * SmsService constructor.
     * @param SmsAdapterInterface $adapter
     */
    public function __construct(SmsAdapterInterface $adapter)
    {
        $this->adapter = $adapter;
    }

    /**
     * @param string $phone
     * @param string $message
     * @return bool
     * @throws \Exception
     */
    public function sendSms(string $phone, string $message): bool
    {
        return $this->adapter->sendSms($phone, $message);
    }
}
