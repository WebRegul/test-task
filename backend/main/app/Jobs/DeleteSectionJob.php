<?php

namespace App\Jobs;

use App\Models\GallerySection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

class DeleteSectionJob extends Job
{
    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Log::info('delete section job');
        $now = Carbon::now();
        $gallerySection = GallerySection::withTrashed()->with('imagesWithTrashed')->whereNotNull('deleted_at')->get();
        Log::info('gallerySection', ['gallerySection' => $gallerySection]);
        foreach ($gallerySection as $section) {
            DB::transaction(function () use ($now, $section) {
                if ($now->diffInDays($section->deleted_at) >= 1) {
                    Log::info('imagesWithTrashed', ['imagesWithTrashed' => $section->imagesWithTrashed]);
                    if (!empty($section->imagesWithTrashed->toArray())) {
                        Log::critical(
                            "секция не может быть удалена, потому что имеются не удаленные фото",
                            ['images' => $section->imagesWithTrashed, 'section_id' => $section->id]
                        );
                        Mail::raw('секция ' . $section->id . ' не может быть удалена, потому что имеются не удаленные фото', function ($message) {
                            $message->to(config('mail.support_email'))
                                ->subject('секция не может быть удален');
                        });
                    } else {
                        $section->forceDelete();
                    }
                }
            });
        }
    }
}
