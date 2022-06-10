<?php

namespace App\Models;

use App\Models\Traits\UuidPrimary;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserContactType extends BaseModel
{
    use UuidPrimary;
    use SoftDeletes;

    protected $table = 'user_contacts_types';

    protected $fillable = [
        'title',
        'name',
        'weight',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];
}
