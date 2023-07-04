<?php

namespace Services;

use Models\Entities\Country;
use mysqli;
use QueryFailedException;
use StatementPrepareFailedException;
use Utils\MySqlUtils;

class MySqlCountryRepository implements ICountryRepository
{
    public function __construct(private readonly mysqli $context)
    {
        //empty
    }

    /**
     * @inheritDoc
     * @throws QueryFailedException
     */
    public function getAll(): array
    {
        $query = "SELECT * FROM Countries";
        $response = $this->context->query($query);
        if (!$response)
            throw new QueryFailedException($query, $this->context->error);

        $countries = [];
        while ($row = $response->fetch_assoc()) {
            $country = Country::parseFromAssoc($row);
            $countries[] = $country;
        }

        $response->free_result();
        return $countries;
    }

    /**
     * @throws StatementPrepareFailedException
     * @throws QueryFailedException
     */
    public function add(Country $country): void
    {
        $query = "INSERT INTO Countries (Name) VALUES (?)";
        MySqlUtils::prepareAndExecute($this->context, $query, 's', $country->name);
    }

    /**
     * @throws StatementPrepareFailedException
     * @throws QueryFailedException
     */
    public function delete(int $id): void
    {
        $query = "DELETE FROM Countries WHERE Id = ?";
        MySqlUtils::prepareAndExecute($this->context, $query, 'i', $id);
    }

}