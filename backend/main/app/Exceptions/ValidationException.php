<?php

namespace App\Exceptions;

use Exception;
use Throwable;

/**
 * Class ValidationException
 * @package App\Exceptions
 */
class ValidationException extends Exception
{
    public function __construct($message = "", $code = 0, Throwable $previous = null)
    {
        $code = $code ?: 422;

        parent::__construct($message, $code, $previous);
    }
}
