<?php

namespace App\Console\Commands;

use App\Models\Payment;
use App\Services\Billing\PaymentService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\ItemNotFoundException;
use Tymon\JWTAuth\Exceptions\UserNotDefinedException;

class CheckPayStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'payment:check-status';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Проверка статуса платежей';


    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle(PaymentService $service)
    {
        $this->info('CheckPayStatus start');

        $now = Carbon::now();
        $payments = Payment::query()
            ->where('status', Payment::STATUS_PENDING)
            ->where('created_at', '<', $now->subMinutes(3))
            ->get();
        foreach ($payments as $payment) {
            try {
                $service->checkStatus($payment->id);
            } catch (UserNotDefinedException $exception) {
                $service->failture($exception->getMessage());
            } catch (ItemNotFoundException $exception) {
                $service->failture($exception->getMessage());
            }
        }

        $this->info('CheckPayStatus finish');
    }
}
