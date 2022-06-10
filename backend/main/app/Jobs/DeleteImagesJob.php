<?php

namespace App\Jobs;

use App\Events\UpdateMemberEvent;
use App\Models\Image as ImageModel;
use App\Services\Images\Image;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class DeleteImagesJob extends Job
{
    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $now = Carbon::now();
        $userIds = [];
        $imageService = new Image();
        $images = ImageModel::withTrashed()
            ->with(['imageSectionswithTrashed', 'gallery'])
            ->whereNotNull('deleted_at')
            ->get();
        foreach ($images as $image) {
            DB::transaction(function () use ($now, $imageService, $image, &$userIds) {
                if ($now->diffInMinutes($image->deleted_at) >= 2) {
                    $userId = collect($image->gallery)->get('user_id');

                    $imageService->deleteImagesFiles($image);
                    $image->imageSectionswithTrashed()
                        ->forceDelete();
                    $image->forceDelete();

                    if (!empty($userId) && !in_array($userId, $userIds)) {
                        $userIds[] = $userId;
                    }
                }
            });
        }

        foreach ($userIds as $userId) {
            event(new UpdateMemberEvent($userId));
        }
    }
}
