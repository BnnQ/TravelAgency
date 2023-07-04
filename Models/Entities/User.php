<?php

namespace Models\Entities;

class User
{
    public ?int $id;
    public string $login;
    public string $hashedPassword;
    public string $email;
    public int $roleId;
    public float $discount;
    public ?string $lastAuthenticationToken;
    public ?string $avatar;

    public function __construct(?int $id, string $login, string $hashedPassword, string $email, int $roleId, float $discount = 0.0, ?string $lastAuthenticationToken = null, ?string $avatar = null)
    {
        $this->id = $id;
        $this->login = $login;
        $this->hashedPassword = $hashedPassword;
        $this->email = $email;
        $this->roleId = $roleId;
        $this->discount = $discount;
        $this->lastAuthenticationToken = $lastAuthenticationToken;
        $this->avatar = $avatar;
    }

    public static function parseFromAssoc(array $associativeArray) : User {
        return new User(@$associativeArray['id'] ?? @$associativeArray['Id'], @$associativeArray['login'] ?? @$associativeArray['Login'], @$associativeArray['hashedPassword'] ?? @$associativeArray['HashedPassword'], @$associativeArray['email'] ?? @$associativeArray['Email'], @$associativeArray['roleId'] ?? @$associativeArray['RoleId'], @$associativeArray['discount'] ?? @$associativeArray['Discount'] ?? 0.0, @$associativeArray['lastAuthenticationToken'] ?? @$associativeArray['LastAuthenticationToken'], @$associativeArray['avatar'] ?? @$associativeArray['Avatar']);
    }

}