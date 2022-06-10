<?php

namespace App\Models\Traits;

trait Updater
{
    public static function bootUpdater()
    {
        static::creating(function ($model) {
            if (empty($model->updater_id)) {
                $model->updater_id = auth()->id();
            }
        });
    }
}
