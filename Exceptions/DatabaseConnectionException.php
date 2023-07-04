<?php
namespace Exceptions;

use Exception;
use Throwable;

class DatabaseConnectionException extends Exception
{
public function __construct(string $databaseName, $errno, int $code = 0, ?Throwable $previous = null)
{
    parent::__construct("Failed to connect to database '$databaseName': $errno", $code, $previous);
}
}