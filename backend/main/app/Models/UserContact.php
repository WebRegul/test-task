<?php

namespace App\Models;

use App\Models\Traits\UuidPrimary;
use Illuminate\Support\Arr;

class UserContact extends BaseModel
{
    use UuidPrimary;

    protected $fillable = [
        'value'
    ];

    protected $hidden = [
        'user_id',
        'user_contacts_type_id',
        'created_at',
        'updated_at'
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function type()
    {
        return $this
            ->hasOne(UserContactType::class, 'id', 'user_contacts_type_id')
            ->orderBy('weight');
    }

    /**
     * @return mixed|string|string[]|null
     */
    public function getValueAttribute()
    {
        $type = $this->attributes['user_contacts_type_id'];
        $phoneType = UserContactType::where('name', 'phone')->first();
        if ($type == Arr::get($phoneType, 'id')) {
            $phone = trim($this->attributes['value']);
            $res = preg_replace(
                '/[\+]?([7|8])[-|\s]?(\d{3})[-|\s]?(\d{3})[-|\s]?(\d{2})[-|\s]?(\d{2})/',
                '+7 ($2) $3 $4 $5',
                $phone
            );
            return $res;
        }

        return $this->attributes['value'];
    }
}
