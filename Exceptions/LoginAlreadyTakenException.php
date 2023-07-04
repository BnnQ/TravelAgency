<?php

namespace Exceptions;

use Exception;
use Throwable;

class LoginAlreadyTakenException extends Exception
{
    public function __construct(string $message = "Username is already taken.", int $code = 0, ?Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}