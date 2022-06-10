<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Jobs\DeleteSectionJob;
use Illuminate\Support\Facades\Log;

class DeleteSections extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sections:delete';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Удаление секций';



    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->info('start');
        Log::info('delete sections command');
        dispatch_now(new DeleteSectionJob());

        $this->info('finish');
    }
}
