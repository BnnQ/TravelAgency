<?php

namespace Models\Entities;

class Role
{
    public function __construct(public ?int $id, public string $name)
    {
        //empty
    }

    public static function parseFromAssoc(array $associativeArray) : Role {
        return new Role(@$associativeArray['id'] ?? @$associativeArray['Id'] ?? null, @$associativeArray['name'] ?? @$associativeArray['Name'] ?? null);
    }

}