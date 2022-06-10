<?php

namespace App\Validators;

/**
 * Class NormalSlug
 * @package App\Validators
 */
class NormalSlug
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

        return strval($value) === $value && boolval(preg_match('/^[a-z0-9_\-]+$/i', $value));
    }
}
