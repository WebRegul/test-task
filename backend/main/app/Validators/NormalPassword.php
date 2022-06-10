<?php

namespace App\Validators;

use Illuminate\Validation\Validator;

/**
 * Class NormalPassword
 * @package App\Validators\
 */
class NormalPassword
{
    /**
     * @param $attribute
     * @param $value
     * @param $parameters
     * @return bool
     */
    public function validate($attribute, $value)
    {
        return strval($value) === $value
            && strlen($value) > 5
            && boolval(preg_match('/^[^а-яё]+$/ui', $value));
    }
}
