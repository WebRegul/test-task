<?php

namespace App\Models;

use App\Models\Traits\Active;
use App\Models\Traits\Creator;
use App\Models\Traits\Updater;
use App\Models\Traits\UuidPrimary;
use Grimzy\LaravelMysqlSpatial\Types\Point;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Grimzy\LaravelMysqlSpatial\Eloquent\SpatialTrait;

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
class BuildingType extends BaseModel
{
    use Creator;
    use Updater;
    use Active;


    /**
     * @return BelongsTo
     */
    public function building(): BelongsTo
    {
        return $this->belongsTo(Building::class);
    }

}
