<?php

namespace App\Events;

use App\Services\Billing\Builders\Payment;

class UpdatePaymentEvent extends Event
{
    #use Dispatchable, InteractsWithSockets, SerializesModels;

    public Payment $payment;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Payment $payment)
    {
        //
    }
}
