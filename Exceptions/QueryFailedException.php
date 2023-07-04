<?php

class QueryFailedException extends Exception
{
    public function __construct(string $query, string $queryError, int $code = 0, ?Throwable $previous = null)
    {
        parent::__construct("Failed to execute query '$query': $queryError", $code, $previous);
    }
}