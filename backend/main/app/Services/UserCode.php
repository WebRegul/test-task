<?php

namespace App\Services;

use App\Models\User as UserModel;
use App\Models\UserCode as UserCodeModel;
use App\Registries\Member;
use App\Services\Sms\SmsService;
use Carbon\Carbon;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Collection;

/**
 * Class UserCode
 * @package App\Services
 */
class UserCode
{
    /**
     * @var Member
     */
    private $member;

    /**
     * @var string
     */
    private $id;

    /**
     * @var Collection
     */
    private $config;

    /**
     * UserCode constructor.
     * @param Member $member
     */
    public function __construct(Member $member)
    {
        $this->member = $member;
        $this->id = $this->member->get('id');
        $this->config = collect(config('sms_code'));
    }

    /**
     * @return int
     * @throws \Exception
     */
    public function sendCode()
    {
        $code = intval($this->config->get('fake_code'));

        if (empty($code)) {
            $userCode = new UserCodeModel();
            $userCode->code = random_int(1000, 9999);
            $userCode->user_id = $this->id;
            $userCode->creator_id = $this->id;
            $userCode->updater_id = $this->id;
            $userCode->sended_at = Carbon::now()->toDateTimeString();
            $userCode->save();
            $code = $userCode->code;

            $this->clearCodes(2, $userCode->code);
        }

        if (!$this->config->get('debug')) {
            $service = app(SmsService::class);
            $message = $this->config
                ->get('message', 'Код подтверждения: %s Никому не сообщайте его.');
            $service->sendSms($this->member->get('user.login'), sprintf($message, $code));
        }

        return $code;
    }

    /**
     * @param $status
     * @param null $code
     * @return mixed
     */
    public function clearCodes(int $status = 4, ?int $code = null): bool
    {
        return UserCodeModel::where('user_id', $this->id)
            ->where('status', 0)
            ->where('code', '!=', $code)
            ->update([
                'status' => $status
            ]);
    }

    /**
     * @param $code
     * @return bool
     */
    public function verifyCode(int $code): bool
    {
        if (!empty($this->config->get('fake_code'))) {
            return $code == $this->config->get('fake_code');
        }

        $userCode = (new UserCodeModel())
            ->where('user_id', $this->id)
            ->where('status', 0)
            ->orderBy('created_at', 'DESC')
            ->first();
        return !empty($userCode) && $code == $userCode->code;
    }

    /**
     * @param $code
     * @return bool
     */
    public function makeCodeSigned(int $code): bool
    {
        return (new UserCodeModel())
            ->where('user_id', $this->id)
            ->where('code', $code)
            ->update(['signed_at' => Carbon::now()->toDateTimeString()]);
    }

    private function isBlocked(): bool
    {
        $blockRules = collect($this->config->get('block_rules'));
        $cacheKey = sprintf('REPEAT_SEND_DATA_%s', $this->id);
        $levelKey = sprintf('%s_level', $cacheKey);
        $attemptsKey = sprintf('%s_attempts', $cacheKey);
        $repeatsKey = sprintf('%s_repeats', $cacheKey);

        $level = Cache::get($levelKey);
        $attempts = Cache::get($attemptsKey);
        $repeats = Cache::get($repeatsKey);

        if (empty($level)) {
            $level = Cache::remember(
                $levelKey,
                $blockRules->get('level_cache_ttl') * 60,
                function () {
                    return 0;
                }
            );
        }
        if (empty($attempts)) {
            $attempts = Cache::remember(
                $attemptsKey,
                $blockRules->get('attempts_cache_ttl') * 60,
                function () {
                    return 0;
                }
            );
        }

        if (empty($repeats)) {
            $repeats = Cache::remember(
                $repeatsKey,
                $blockRules->get('repeats_cache_ttl') * 60,
                function () {
                    return 0;
                }
            );
        }

        $repeats = Cache::increment($repeatsKey);

        if ($level) {
            return true;
        }

        if (!$level && $attempts + 1 > $blockRules->get('attempts')) {
            Cache::forget($attemptsKey);
            Cache::increment($levelKey);
            return true;
        }

        if ($repeats > $blockRules->get('repeats')) {
            $this->clearCodes();
            Cache::forget($repeatsKey);
            Cache::increment($attemptsKey);
            return true;
        }

        return false;
    }

    /**
     * @return int
     * @throws \Exception
     */
    public function repeatSendCode(): int
    {
        if ($this->isBlocked()) {
            throw new AuthenticationException('вы превысили количество попыток'
                . ' отправки кода! вы заблокированы!');
        }

        $this->clearCodes();
        return $this->sendCode();
    }
}
