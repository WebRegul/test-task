<?php

namespace App\Models;

use App\Models\Traits\Creator;
use App\Models\Traits\Updater;
use App\Models\Traits\UuidPrimary;

class UserCode extends BaseModel
{
    use UuidPrimary;
    use Creator;
    use Updater;
}
