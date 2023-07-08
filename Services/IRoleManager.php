<?php

namespace Services;

use Models\Entities\Role;
use Models\Entities\User;

interface IRoleManager
{
    public function getRoleById(int $id) : Role;
    public function getRoleByName(string $name) : Role;

    /**
     * @param string $roleName
     * @return User[]
     */
    public function getUsersWithRole(string $roleName): array;
    public function addRole(Role $role): void;
    public function changeUserRole(User $user, Role $role): void;
    public function isUserInRole(User $user, string $roleName): bool;
    public function deleteRole(int $roleId): void;
}