<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\UuidPrimary;

/**
 * @property mixed $id
 */
class PasswordReset extends Model
{
    use UuidPrimary;
    protected $table = 'password_resets';
    protected $guarded = ['id'];
    public $timestamps = false;
}
