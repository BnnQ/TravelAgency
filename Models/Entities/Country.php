<?php

namespace Models\Entities;

use Exception;

class Country
{
    public function __construct(public ?int $id, public string $name)
    {
        //empty
    }

    public static function parseFromAssoc(array $associativeArray): Country
    {
        return new Country(id: @$associativeArray['id'] ?? @$associativeArray['Id'], name: @$associativeArray['name'] ?? @$associativeArray['Name']);
    }

}