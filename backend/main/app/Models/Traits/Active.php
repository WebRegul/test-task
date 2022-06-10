<?php

namespace App\Models\Traits;

use Illuminate\Database\Eloquent\Builder;

trait Active
{
    /**
     * @param Builder $query
     * @return Builder
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', 1);
    }
}
