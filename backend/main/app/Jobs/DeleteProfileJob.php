<?php

namespace App\Jobs;

use App\Models\Gallery;
use App\Models\GallerySection;
use App\Models\Image;
use App\Models\ImageSections;
use App\Models\Profile;
use App\Models\User;
use App\Models\UserCode;
use App\Models\UserContact;
use App\Models\UserStorage;
use App\Services\Images\Image as ImagesImage;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DeleteProfileJob extends Job
{
    private $id;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(string $userId)
    {
        $this->id = $userId;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        DB::beginTransaction();
        try {
            $imageService = new ImagesImage();
            Log::info('delete user_id', ['user_id' => $this->id]);
            UserCode::where('user_id', $this->id)->delete();
            UserContact::where('user_id', $this->id)->delete();
            UserStorage::where('user_id', $this->id)->delete();
            $gallery = Gallery::where('user_id', $this->id);
            $galleryCollection = $gallery->get();
            $galleryCollection->each(function ($e) use ($imageService) {
                $images = ImageSections::withTrashed()->where('gallery_id', Arr::get($e, 'id'));
                $imagesCollection = $images->get();
                $images->forceDelete();
                $imagesCollection->each(function ($e) use ($imageService) {
                    $image = Image::withTrashed()->where('id', $e->image_id)->get();
                    $image->each(function ($e) use ($imageService) {
                        $imageService->deleteImagesFiles($e);
                        $e->forceDelete();
                    });
                });

                GallerySection::withTrashed()->where('gallery_id', Arr::get($e, 'id'))->forceDelete();
            });

            $gallery->forceDelete();
            Profile::where('user_id', $this->id)->forceDelete();
            User::find($this->id)->forceDelete();
            DB::commit();
            return ['succeess' => true, 'message' => 'Пользователь успешно удален'];
        } catch (\Exception $e) {
            DB::rollback();
            return ['succeess' => false, 'message' => 'Ошибка при удалении пользователя' . $e->getMessage()];
        }
    }
}
