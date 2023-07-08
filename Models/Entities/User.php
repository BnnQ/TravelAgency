<?php

namespace Models\Entities;

class User
{
    public function __construct(public ?int $id, public string $login, public string $hashedPassword, public string $email, public string $roleName, public float $discount = 0.0, public ?string $lastAuthenticationToken = null, public ?string $avatar = null)
    {
        //empty
    }

    public static function parseFromAssoc(array $associativeArray) : User {
        return new User(@$associativeArray['id'] ?? @$associativeArray['Id'], @$associativeArray['login'] ?? @$associativeArray['Login'], @$associativeArray['hashedPassword'] ?? @$associativeArray['HashedPassword'], @$associativeArray['email'] ?? @$associativeArray['Email'], @$associativeArray['roleName'] ?? @$associativeArray['RoleName'], @$associativeArray['discount'] ?? @$associativeArray['Discount'] ?? 0.0, @$associativeArray['lastAuthenticationToken'] ?? @$associativeArray['LastAuthenticationToken'], @$associativeArray['avatar'] ?? @$associativeArray['Avatar']);
    }

}