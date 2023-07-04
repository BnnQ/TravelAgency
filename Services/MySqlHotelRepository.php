<?php

namespace Services;

use Models\Entities\Hotel;
use mysqli;
use QueryFailedException;
use StatementPrepareFailedException;
use Utils\MySqlUtils;

class MySqlHotelRepository implements IHotelRepository
{
    public function __construct(private readonly mysqli $context)
    {
        //empty
    }

    /**
     * @inheritDoc
     */
    public function getAll(): array
    {
        $query = "SELECT Hotels.Id, Hotels.Name, Hotels.Stars, Hotels.Cost, Hotels.Info, C.Name as CountryName, C2.Name as CityName FROM Hotels JOIN Countries C on Hotels.CountryId = C.Id JOIN Cities C2 on C.Id = C2.CountryId";
        $response = $this->context->query($query);

        $hotels = [];
        while ($row = $response->fetch_assoc()) {
            $city = Hotel::parseFromAssoc($row);
            $hotels[] = $city;
        }

        $response->free_result();
        return $hotels;
    }

    /**
     * @throws StatementPrepareFailedException
     * @throws QueryFailedException
     */
    public function add(Hotel $hotel): void
    {
        $query = "SELECT Countries.Id as CountryId, C.Id as CityId FROM Countries JOIN Cities C on Countries.Id = C.CountryId AND Countries.Name = ? AND C.Name = ?";
        $response = MySqlUtils::prepareAndGetResult($this->context, $query, 'ss', $hotel->countryName, $hotel->cityName);
        $assocResponse = $response->fetch_assoc();
        $countryId = $assocResponse['CountryId'];
        $cityId = $assocResponse['CityId'];
        $response->free_result();

        $query = "INSERT INTO Hotels (Name, CountryId, CityId, Stars, Cost, Info) VALUES (?, ?, ?, ?, ?, ?)";
        MySqlUtils::prepareAndExecute($this->context, $query, 'siiids', $hotel->name, $countryId, $cityId, $hotel->stars, $hotel->cost, $hotel->info);
    }

    /**
     * @throws StatementPrepareFailedException
     * @throws QueryFailedException
     */
    public function delete(int $id): void
    {
        $query = "DELETE FROM Hotels WHERE Id = ?";
        MySqlUtils::prepareAndExecute($this->context, $query, 'i', $id);
    }

}