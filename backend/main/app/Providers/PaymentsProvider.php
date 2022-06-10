<?php

namespace App\Providers;

use App\Services\Billing\PaymentAdapters\PaymentAdapterInterface;
use Illuminate\Support\ServiceProvider;

class PaymentsProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $adapter = config('payments.YooMoneyProvider.adapter');
        $this->app->bind(PaymentAdapterInterface::class, function ($app) use ($adapter) {
            return new $adapter();
        });
    }
}
