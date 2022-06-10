<?php

namespace App\Validators;

use Carbon\Carbon;

/**
 * Class RealDate
 * @package App\Validators
 */
class RealDate
{
    /**
     * @param $attribute
     * @param $value
     * @param $parameters
     * @return bool
     */
    public function validate($attribute, $value)
    {
        if (is_null($value) || empty($value)) {
            return true;
        }

        $value = strtotime($value);
        return $value !== false && $value <= Carbon::now()->timestamp;
    }
}
