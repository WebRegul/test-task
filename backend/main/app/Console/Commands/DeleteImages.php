<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Jobs\DeleteImagesJob;
use Illuminate\Support\Facades\Log;

class DeleteImages extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'images:delete';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Удаление фотографий';



    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->info('start');
        dispatch_now(new DeleteImagesJob());
        $this->info('finish');
    }
}
