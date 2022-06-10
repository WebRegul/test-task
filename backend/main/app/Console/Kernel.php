<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Laravel\Lumen\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        \App\Console\Commands\CreateTestImages::class,
        \App\Console\Commands\DeleteImages::class,
        \App\Console\Commands\DeleteSections::class,
        \App\Console\Commands\AutoPayment::class,
        \App\Console\Commands\CheckPayStatus::class,
        \App\Console\Commands\AutoPaymentCharge::class,
        \App\Console\Commands\ExpiringSubsciptionsNotification::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param \Illuminate\Console\Scheduling\Schedule $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('images:delete')->everyTwoMinutes();
        $schedule->command('sections:delete')->dailyAt('02:00');
        $schedule->command('autopayment')->dailyAt('08:00');
        $schedule->command('autopayment:charge')->dailyAt('08:10');
        $schedule->command('payment:check-status')->everyFiveMinutes();
        $schedule->command('notification:expiring-subscriptions')->dailyAt('09:00');
    }
}
