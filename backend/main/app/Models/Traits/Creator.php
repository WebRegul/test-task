<?php

namespace App\Models\Traits;

trait Creator
{
    public static function bootCreator()
    {
        static::creating(function ($model) {
            if (empty($model->creator_id)) {
                $model->creator_id = auth()->id();
            }
        });
    }
}
