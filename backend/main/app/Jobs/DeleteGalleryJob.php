<?php

namespace App\Jobs;

use App\Models\Gallery as GalleryModel;
use App\Models\GallerySection as GallerySectionModel;
use App\Models\ImageSections as ImageSectionsModel;
use App\Services\Images\Image;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DeleteGalleryJob extends Job
{
    /**
     * @var string
     */
    protected $id;
    /**
     * @var GalleryModel
     */
    protected $gallery;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(GalleryModel $gallery)
    {
        $this->id = $gallery->id;
        $this->gallery = $gallery;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        DB::transaction(function () {
            $image = new Image();
            $images = $image->getEntityImages($this->id, 'gallery');
            $images = collect($images)->pluck('id')->toArray();

            ImageSectionsModel::query()
                ->where('gallery_id', $this->id)
                ->forceDelete();
            GallerySectionModel::query()
                ->where('gallery_id', $this->id)
                ->forceDelete();

            if (!empty($images)) {
                $drop = collect($image->dropImages($images, $this->gallery->user_id));
                if ($drop->has('error')) {
                    throw new \Exception('ошибка удаления изображений', 500);
                }
            }

            GalleryModel::query()
                ->where('id', $this->id)
                ->forceDelete();
        });
    }
}
