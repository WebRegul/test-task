<?php

namespace App\Listeners;

use App\Events\UpdateInvoiceEvent;
use App\Services\Billing\BillingService;
use App\Services\Billing\Builders\Invoice;
use GuzzleHttp\Psr7\LazyOpenStream;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class InvoiceSuccessListener
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
        Log::info('listener const');
    }

    /**
     * Handle the event.
     *
     * @param InvoiceSuccessListener $event
     * @return void
     */
    public function handle(UpdateInvoiceEvent $event)
    {
        Log::info('listenaer handle');
        if ($event->invoice->status === 1) {
            $this->service->setInvoiceBuilder($event->invoice);
            $this->service->setTariffBuilder($event->invoice->tariff());
            $this->service->updateProfileTariff();
        }
    }
}
