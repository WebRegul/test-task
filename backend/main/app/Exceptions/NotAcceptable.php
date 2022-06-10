<?php

namespace App\Exceptions;

use Exception;
use Throwable;

class NotAcceptable extends Exception
{
    public function __construct($message = "", $code = 0, Throwable $previous = null)
    {
        $code = $code ?: 406;

        parent::__construct($message, $code, $previous);
    }
}
