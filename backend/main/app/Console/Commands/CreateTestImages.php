<?php

namespace App\Console\Commands;

use App\Models\GallerySection;
use Illuminate\Console\Command;
use Storage;
use App\Services\Images\Image;
use Illuminate\Support\Facades\File;
use App\Models\User;

class CreateTestImages extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:create-images {sectionId} {userId}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Добавление 5 фотографий в галерею';

    protected $filePath = 'database/seeders/test-images';


    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle(Image $image)
    {
        $this->info('start');
        $path  = Storage::disk('local')->getAdapter()->getPathPrefix();
        $files = File::allFiles($this->filePath);
        $type = pathinfo($path, PATHINFO_EXTENSION);
        $res = [];
        foreach ($files as $file) {
            $base64 = 'data:image/' . $type . ';base64,' . base64_encode($file->getContents());
            $res[] = [
                'src_mini' => $base64,
                'filename' => $file->getBasename()
            ];
        }

        $gallery = GallerySection::find($this->argument('sectionId'));
        if ($gallery) {
            $image = $image->saveEntityImages($gallery->gallery_id, 'gallery', $res, $this->argument('sectionId'), $this->argument('userId'));
        } else {
            dd('неизвестная секция');
        }

        $this->info('finish');
    }
}
