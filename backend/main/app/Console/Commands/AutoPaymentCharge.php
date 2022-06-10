<?php

namespace App\Console\Commands;

use App\Models\Invoice;
use App\Models\Payment as paymentModel;
use App\Services\Billing\BillingService;
use App\Registries\Member as MemberRegistry;
use App\Services\Billing\Builders\Invoice as BuildersInvoice;
use App\Services\Billing\Builders\Payment;
use App\Services\User;
use Illuminate\Console\Command;

class AutoPaymentCharge extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'autopayment:charge';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Автоплатеж';



    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle(BillingService $service)
    {
        $this->info('start');
        $invoices = Invoice::where('status', Invoice::STATUS_NEW)->get();
        foreach ($invoices as $invoice) {
            $userService = new User($invoice->user_id);
            $member = app(MemberRegistry::class);
            $userService->setMember($member);
            $invoiceServise = new BuildersInvoice($member, $invoice, new Payment(new paymentModel(), $member));
            $service->prolongationCharge($invoiceServise);
        }
        $this->info('finish');
    }
}
