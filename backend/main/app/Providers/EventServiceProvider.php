<?php

namespace App\Providers;

use Laravel\Lumen\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        \App\Events\ExampleEvent::class => [
            \App\Listeners\ExampleListener::class,
        ],
        \App\Events\UpdateInvoiceEvent::class => [
            \App\Listeners\InvoiceSuccessListener::class,
        ],
        \App\Events\UpdateMemberEvent::class => [
            \App\Listeners\UpdateMemberListener::class,
        ],
        \SocialiteProviders\Manager\SocialiteWasCalled::class => [
            'SocialiteProviders\\LaravelPassport\\LaravelPassportExtendSocialite@handle',
            'SocialiteProviders\\VKontakte\\VKontakteExtendSocialite@handle',
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        //
    }

    /**
     * Определить, должны ли автоматически обнаруживаться события и слушатели.
     *
     * @return bool
     */
    public function shouldDiscoverEvents()
    {
        return true;
    }
}
