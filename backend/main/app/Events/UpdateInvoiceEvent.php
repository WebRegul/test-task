<?php

namespace App\Events;

use App\Services\Billing\Builders\Invoice;
use App\Services\Billing\Builders\Payment;
use Illuminate\Support\Facades\Log;

class UpdateInvoiceEvent extends Event
{
    #use Dispatchable, InteractsWithSockets, SerializesModels;

    public Invoice $invoice;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Invoice $invoice)
    {
        $this->invoice = $invoice;
        Log::info('event invoice success');
    }
}
