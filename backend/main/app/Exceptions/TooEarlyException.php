<?php

namespace App\Exceptions;

use Exception;
use Throwable;

class TooEarlyException extends Exception
{
    public function __construct($message = "", $code = 0, Throwable $previous = null)
    {
        $code = $code ?: 425;

        parent::__construct($message, $code, $previous);
    }
}
