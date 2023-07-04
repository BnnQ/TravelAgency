<?php

namespace Exceptions;

use Exception;
use Throwable;

class UserNotFoundException extends Exception
{
    public function __construct(string $message = "User with such username not found or not registered.", int $code = 0, ?Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    public function  __toString(): string
    {
        return __CLASS__.": [$this->code]: $this->message\n";
    }

}