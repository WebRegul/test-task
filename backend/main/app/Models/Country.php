<?php

namespace App\Models;

use App\Models\Traits\Active;
use App\Models\Traits\Creator;
use App\Models\Traits\Updater;
use App\Models\Traits\UuidPrimary;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property-read string $id
 * @property integer $code
 * @property string $alfa2
 * @property integer $alfa3
 * @property string $title
 * @property string $name
 * @property integer $status
 * @property mixed|string $updater_id
 * @property mixed|string $creator_id
 * @property mixed|string $created_at
 * @property mixed|string $updated_at
 */
class Country extends BaseModel
{
    use UuidPrimary;
    use Creator;
    use Updater;
    use Active;

    /**
     * @return HasMany
     */
    public function cities(): HasMany
    {
        return $this->hasMany(City::class);
    }


}
