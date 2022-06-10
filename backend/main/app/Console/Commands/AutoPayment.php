<?php

namespace App\Console\Commands;

use App\Models\Profile;
use App\Services\Billing\BillingService;
use App\Services\User;
use App\Registries\Member as MemberRegistry;
use Carbon\Carbon;
use Illuminate\Console\Command;

class AutoPayment extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'autopayment';

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
        $profiles = Profile::where('tariff_finished_at', '<', Carbon::tomorrow())->get();
        foreach ($profiles as $profile) {
            if ($profile->auto_renewal) {
                $userService = new User($profile->user_id);
                $userService->setMember(app(MemberRegistry::class));
                $service->createProlongation();
            }
        }
        $this->info('finish');
    }
}
