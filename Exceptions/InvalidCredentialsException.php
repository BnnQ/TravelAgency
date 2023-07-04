<?php

namespace Exceptions;

use Exception;
use Throwable;

class InvalidCredentialsException extends Exception
{
    public function __construct(string $message = "Entered credentials is invalid.", int $code = 0, ?Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}