<?php

namespace App\Models;

use App\Models\Traits\UuidPrimary;

/**
 * @property-read string $id
 * @property string $user_id
 */
class UserCard extends BaseModel
{
    use UuidPrimary;

    protected $fillable = [
        'user_id', 'provider', 'card_data', 'status', 'external_id'
    ];

    /**
     * @return mixed|string|string[]|null
     */
    public function getCardDataAttribute()
    {
        return json_decode($this->attributes['card_data'], 1);
    }
}
