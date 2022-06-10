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
 * @property-read string $id
 * @property string $country_id
 * @property string $fias_id
 * @property integer $osm_id
 * @property string $title
 * @property Point  $coords
 * @property integer $weight
 * @property integer $status
 * @property mixed|string $updater_id
 * @property mixed|string $creator_id
 * @property mixed|string $created_at
 * @property mixed|string $updated_at
 */
class City extends BaseModel
{
    use UuidPrimary;
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
    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }
}
