<?php

namespace App\Models;

use App\Models\Traits\Active;
use App\Models\Traits\Creator;
use App\Models\Traits\Updater;
use App\Models\Traits\UuidPrimary;
use Grimzy\LaravelMysqlSpatial\Types\Point;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Grimzy\LaravelMysqlSpatial\Eloquent\SpatialTrait;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * @property-read integer $id
 * @property string $profile_id
 * @property string $type_id
 * @property string $city_id
 * @property string $url
 * @property string $title
 * @property string $description
 * @property Point  $coords
 * @property integer $status
 * @property mixed|string $updater_id
 * @property mixed|string $creator_id
 * @property mixed|string $created_at
 * @property mixed|string $updated_at
 */
class Building extends BaseModel
{
    use Creator;
    use Updater;
    use Active;

    use SpatialTrait;

    protected $spatialFields = [
        'coords'
    ];

    /**
     * @return BelongsTo
     */
    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }

    /**
     * @return HasOne
     */
    public function type(): HasOne
    {
        return $this->hasOne(BuildingType::class);
    }
}
