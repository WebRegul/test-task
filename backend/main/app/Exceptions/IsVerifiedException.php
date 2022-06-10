<?php

namespace App\Exceptions;

use Exception;
use Throwable;

/**
 * Class IsVerifiedException
 * @package App\Exceptions
 */
class IsVerifiedException extends Exception
{
    public function __construct($message = "", $code = 0, Throwable $previous = null)
    {
        $code = $code ?: 409;

        parent::__construct($message, $code, $previous);
    }
}
