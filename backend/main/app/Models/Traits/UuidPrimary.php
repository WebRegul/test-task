<?php

namespace App\Models\Traits;

use Illuminate\Support\Str;

trait UuidPrimary
{
    public static function bootUuidPrimary()
    {
        static::creating(function ($model) {
            if (empty($model->{$model->getKeyName()})) {
                $model->{$model->getKeyName()} = Str::uuid()->toString();
                //dd( $model->{$model->getKeyName()});
            }
        });
    }

    /**
     * @return bool
     */
    public function getIncrementing(): bool
    {
        return false;
    }

    /**
     * @return string
     */
    public function getKeyType(): string
    {
        return 'string';
    }
}
