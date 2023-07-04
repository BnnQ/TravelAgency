<?php

namespace Utils;

use mysqli;
use mysqli_result;
use mysqli_stmt;
use QueryFailedException;
use StatementPrepareFailedException;

class MySqlUtils
{
    /**
     * @throws StatementPrepareFailedException
     */
    private static function prepareStatement(mysqli $context, string $query, string $paramTypes, mixed ...$params): mysqli_stmt
    {
        $queryStatement = $context->prepare($query);
        if (!$queryStatement)
            throw new StatementPrepareFailedException($query, $context->error);

        $queryStatement->bind_param($paramTypes, ...$params);
        return $queryStatement;
    }

    /**
     * @throws StatementPrepareFailedException
     * @throws QueryFailedException
     */
    public static function prepareAndExecute(mysqli $context, string $query, string $paramTypes, mixed ...$params): void
    {
        $queryStatement = self::prepareStatement($context, $query, $paramTypes, ...$params);
        $response = $queryStatement->execute();
        if (!$response)
            throw new QueryFailedException($query, $context->error);
    }

    /**
     * @throws StatementPrepareFailedException
     * @throws QueryFailedException
     */
    public static function prepareAndGetResult(mysqli $context, string $query, string $paramTypes, mixed ...$params): bool|mysqli_result
    {
        $queryStatement = self::prepareStatement($context, $query, $paramTypes, ...$params);
        $queryStatement->execute();

        $response = $queryStatement->get_result();
        if (!$response)
            throw new QueryFailedException($query, $context->error);

        return $response;
    }
}