<?php

namespace App\Providers;

use App\Services\Sms\Adapters\SmsAdapterInterface;
use Illuminate\Support\ServiceProvider;

/**
 * Class SmsProvider
 * @package App\Providers
 */
class SmsProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $adapters = collect(config('sms'));
        $adapter = $adapters->get($adapters->get('default'));

        if (!empty($adapter)) {
            $this->app->bind(SmsAdapterInterface::class, function ($app) use ($adapter) {
                $class = collect($adapter)->get('class');
                return new $class();
            });
        }
    }
}
