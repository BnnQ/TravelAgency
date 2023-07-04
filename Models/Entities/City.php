<?php

namespace Models\Entities;

class City
{
    public function __construct(public ?int $id, public string $name, public string $countryName)
    {
        //empty
    }

    public static function parseFromAssoc(array $associativeArray): City {
        return new City(id: @$associativeArray['id'] ?? @$associativeArray['Id'], name: @$associativeArray['name'] ?? @$associativeArray['Name'], countryName: @$associativeArray['countryName'] ?? @$associativeArray['CountryName']);
    }
}