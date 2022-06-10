<?php

namespace App\Console\Commands;

use App\Models\User as UserModel;
use App\Notifications\ExpiringSubscriptionsNotification;
use Illuminate\Console\Command;
use Carbon\Carbon;

class ExpiringSubsciptionsNotification extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notification:expiring-subscriptions';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Уведомление пользователям о заканчивающейся подписке';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $days = 3;
        $users = UserModel::query()
            ->whereNotNull('verified_at')
            ->whereHas('profile', function ($q) use ($days) {
                $q->where('tariff_finished_at', '<', Carbon::now()->addDays($days));
            })
            ->get();

        foreach ($users as $user) {
            $user->notify(new ExpiringSubscriptionsNotification([
                'message' => "Срок действия подписки истекает через {$days} дня",
            ]));
        }
    }
}
