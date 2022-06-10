<?php

namespace App\Models;

use App\Events\UpdateMemberEvent;
use App\Models\Traits\Creator;
use App\Models\Traits\Updater;
use App\Models\Traits\UuidPrimary;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property-read  string $id
 * @property mixed|string|null $birthday_at
 * @property mixed $name
 * @property mixed $surname
 * @property mixed $patronymic
 * @property mixed|string $updater_id
 * @property mixed|string $creator_id
 * @property mixed|string $user_id
 * @property boolean is_hotel
 *
 * @mixin Builder
 */
class Profile extends BaseModel
{
    use UuidPrimary;
    use SoftDeletes;
    use Creator;
    use Updater;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'surname',
        'patronymic',
        'gender',

        'birthday_at'
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        'creator_id',
        'updater_id',
        //'user_id', //сломали этим getGalleryInfo(GetGalleryInfoRequest
        'register_source_id',
        'created_at',
        'updated_at',
        'deleted_at',
        'birthday_at',
    ];

    /**
     * @var array
     */
    protected $casts = [
        'birthday_at' => 'datetime:d.m.Y',
    ];

    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted(): void
    {
        static::updated(function ($profile) {
            event(new UpdateMemberEvent($profile->user_id));
        });
    }

    /**
     * @return HasOne
     */
    public function user(): HasOne
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    /**
     * @return HasOne
     */
    public function registerSource(): HasOne
    {
        return $this->hasOne(RegisterSource::class, 'id', 'register_source_id');
    }

    /**
     * @return HasMany
     */
    public function contacts(): HasMany
    {
        return $this
            ->hasMany(UserContact::class, 'user_id', 'user_id')
            ->with('type');
    }

    /**
     * @return HasOne
     */
//    public function tariff(): HasOne
//    {
//        return $this->hasOne(Tariff::class, 'id', 'tariff_id');
//    }

    public function setNameAttribute($value): void
    {
        if ($value == '') {
            $this->attributes['name'] = null;
        } else {
            $this->attributes['name'] = $value;
        }
    }
}
