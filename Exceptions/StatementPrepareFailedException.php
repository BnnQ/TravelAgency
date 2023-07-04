<?php

class StatementPrepareFailedException extends Exception
{
    public function __construct($statementQuery, string $errorMessage, int $code = 0, ?Throwable $previous = null)
    {
        parent::__construct("Failed to prepare statement '$statementQuery': $errorMessage", $code, $previous);
    }
}