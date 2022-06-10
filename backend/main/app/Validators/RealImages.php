<?php

namespace App\Validators;

use App\Models\Image;
use Illuminate\Validation\Validator;
use Ramsey\Uuid\Rfc4122\Validator as UuidValidator;

/**
 * Class RealImages
 * @package App\Validators
 */
class RealImages
{
    /**
     * @param $attribute
     * @param $value
     * @param $parameters
     * @return bool
     */
    public function validate($attribute, array $value)
    {
        foreach ($value as $imageId) {
            if (empty(Image::query()->find($imageId))) {
                return false;
            }
        }

        return true;
    }
}
