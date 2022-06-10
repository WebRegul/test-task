<?php

namespace App\Helpers;

use App\Facades\Member;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class Permissions
{
    public static function isOwner(Model $model, ?string $userId = null): bool
    {
        return $model->user_id == ($userId ?? Member::get('id'));
    }

    public static function isVerified(User $user): bool
    {
        return !empty($user->verified_at);
    }
}
