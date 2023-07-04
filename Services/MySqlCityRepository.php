<?php

namespace Services;

use Models\Entities\City;
use Models\Entities\Country;
use mysqli;
use QueryFailedException;
use StatementPrepareFailedException;
use Utils\MySqlUtils;

class MySqlCityRepository implements ICityRepository
{
    public function __construct(private readonly mysqli $context)
    {
        //empty
    }

    /**
     * @throws QueryFailedException
     */
    public function getAll(): array
    {
        $query = "SELECT Cities.Id, Cities.Name, C.Name as CountryName FROM Cities JOIN Countries C on C.Id = Cities.CountryId";
        $response = $this->context->query($query);
        if (!$response)
            throw new QueryFailedException($query, $this->context->error);

        $cities = [];
        while ($row = $response->fetch_assoc()) {
            $city = City::parseFromAssoc($row);
            $cities[] = $city;
        }

        $response->free_result();
        return $cities;
    }

    /**
     * @throws StatementPrepareFailedException
     * @throws QueryFailedException
     */
    public function getByCountryName(string $countryName): array
    {
        $query = "SELECT DISTINCT Cities.*, C.Name as CountryName FROM Cities JOIN Countries C on Cities.CountryId = C.Id WHERE C.Name = ?";
        $response = MySqlUtils::prepareAndGetResult($this->context, $query, 's', $countryName);

        $cities = [];
        while ($row = $response->fetch_assoc()) {
            $city = City::parseFromAssoc($row);
            $cities[] = $city;
        }

        return $cities;
    }

    /**
     * @throws StatementPrepareFailedException
     * @throws QueryFailedException
     */
    public function add(City $city): void
    {
        $query = "SELECT Id FROM Countries WHERE Name = ?";
        $response = MySqlUtils::prepareAndGetResult($this->context, $query, 's', $city->countryName);
        $countryId = $response->fetch_assoc()['Id'];
        $response->free_result();

        $query = "INSERT INTO Cities (Name, CountryId) VALUES (?, ?)";
        MySqlUtils::prepareAndExecute($this->context, $query, 'si', $city->name, $countryId);
    }

    /**
     * @throws StatementPrepareFailedException
     * @throws QueryFailedException
     */
    public function delete(int $id): void
    {
        $query = "DELETE FROM Cities WHERE Id = ?";
        MySqlUtils::prepareAndExecute($this->context, $query, 'i', $id);
    }

}