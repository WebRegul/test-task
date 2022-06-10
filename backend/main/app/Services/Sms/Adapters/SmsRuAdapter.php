<?php

namespace App\Services\Sms\Adapters;

use Ixudra\Curl\Facades\Curl;

class SmsRuAdapter implements SmsAdapterInterface
{
    /**
     * @var string|null
     */
    protected $login;

    /**
     * @var string|null
     */
    protected $password;

    /**
     * @var string|null
     */
    protected $apiKey;

    /**
     * @var string|null
     */
    protected $apiUrl;

    /**
     * @var string|null
     */
    protected $senderName;

    /**
     * SmsRuAdapter constructor.
     */
    public function __construct()
    {
        $this->login = config('sms.smsru.login');
        $this->password = config('sms.smsru.password');
        $this->apiKey = config('sms.smsru.api_key');
        $this->apiUrl = config('sms.smsru.api_url');
        $this->senderName = config('sms.smsru.sender');
    }

    /**
     * @param string $phone
     * @param string $message
     * @return bool
     * @throws \Exception
     */
    public function sendSms(string $phone, string $message): bool
    {
        try {
            return $this->send($phone, $message);
        } catch (\Exception $exception) {
            throw new \Exception($exception->getMessage(), $exception->getCode(), $exception);
        }
    }

    /**
     * @param string $phone
     * @param string $message
     * @return bool
     * @throws \Exception
     */
    private function send(string $phone, string $message): bool
    {
        if (empty($this->apiKey) && empty($this->login) && empty($this->password)) {
            throw new \Exception('нет данных доступа к api провайдера', 409);
        }

        if (empty($this->apiUrl)) {
            throw new \Exception('нет url до api провайдера', 502);
        }

        $result = false;

        $data = collect();
        $data->put('api_id', $this->apiKey);
        $data->put('to', $phone);
        $data->put('msg', $message);
        $data->put('json', 1);

        $response = Curl::to($this->apiUrl)
            ->withData($data->toArray())
            ->returnResponseObject()
            ->post();

        if ($response->content) {
            $content = collect(json_decode($response->content, true));

            if ($content->get('status') == 'OK') {
                foreach ($content->get('sms') as $smsPhone => $sms) {
                    $sms = collect($sms);
                    if ($sms->get('status') == 'OK') {
                        $result = true;
                    } else {
                        $result = false;

                        throw new \Exception(sprintf(
                            'ошибка отправки сообщения провайдера #%s: %s',
                            $sms->get('status_code'),
                            $sms->get('status_text')
                        ), 500);
                    }
                }
            } else {
                $result = false;

                throw new \Exception(sprintf(
                    'ошибка запроса к провайдеру #%s: %s',
                    $content->get('status_code'),
                    $content->get('status_text')
                ), 500);
            }
        } else {
            $result = false;

            throw new \Exception(sprintf('сообщение не отправлено: '
                . 'не удалось установить связь с провайдером'), 500);
        }

        return $result;
    }
}
