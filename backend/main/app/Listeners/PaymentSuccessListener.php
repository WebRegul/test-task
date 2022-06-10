<?php

namespace App\Listeners;

use App\Events\ExampleEvent;
use App\Events\UpdatePaymentEvent;
use App\Services\Billing\BillingService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class PaymentSuccessListener
{
    protected $service;

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct(BillingService $service)
    {
        $this->service = $service;
    }

    /**
     * Handle the event.
     *
     * @param \App\Events\ExampleEvent $event
     * @return void
     */
    public function handle(UpdatePaymentEvent $event)
    {
    }
}
