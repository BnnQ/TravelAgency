<?php

namespace Services;

use Models\Entities\Role;
use Models\Entities\User;
use mysqli;
use QueryFailedException;
use StatementPrepareFailedException;
use Utils\MySqlUtils;

class MySqlRoleManager implements IRoleManager
{
    public function __construct(private readonly mysqli $context)
    {
        //empty
    }

    /**
     * @throws StatementPrepareFailedException
     * @throws QueryFailedException
     */
    public function getRoleById(int $id): Role
    {
        $query = "SELECT * FROM Roles WHERE Id = ?";
        $response = MySqlUtils::prepareAndGetResult($this->context, $query, 'i', $id);
        $role = Role::parseFromAssoc($response->fetch_assoc());

        $response->free_result();
        return $role;
    }

    /**
     * @throws StatementPrepareFailedException
     * @throws QueryFailedException
     */
    public function getRoleByName(string $name): Role
    {
        $query = "SELECT * FROM Roles WHERE Name = ?";
        $response = MySqlUtils::prepareAndGetResult($this->context, $query, 's', $name);
        $role = Role::parseFromAssoc($response->fetch_assoc());

        $response->free_result();
        return $role;
    }

    /**
     * @inheritDoc
     * @throws StatementPrepareFailedException
     * @throws QueryFailedException
     */
    public function getUsersWithRole(string $roleName): array
    {
        $query = "SELECT Id from Roles WHERE Name = ?";
        $response = MySqlUtils::prepareAndGetResult($this->context, $query, 's', $roleName);
        $roleId = $response->fetch_assoc()['Id'];
        $response->free_result();

        $query = "SELECT * FROM Users WHERE RoleId = ?";
        $response = MySqlUtils::prepareAndGetResult($this->context, $query, 'i', $roleId);

        $users = [];
        while ($row = $response->fetch_assoc()) {
            $user = User::parseFromAssoc($row);
            $users[] = $user;
        }

        $response->free_result();
        return $users;
    }
    /**
     * @throws StatementPrepareFailedException
     * @throws QueryFailedException
     */
    public function addRole(Role $role): void
    {
        $query = "INSERT INTO Roles (Name) VALUE (?)";
        MySqlUtils::prepareAndExecute($this->context, $query, 's', $role->name);
    }

    /**
     * @throws StatementPrepareFailedException
     * @throws QueryFailedException
     */
    public function isUserInRole(User $user, string $roleName): bool
    {
        $query = "SELECT R.Name FROM Users JOIN Roles R on R.Id = Users.RoleId AND Users.Id = ?";
        $response = MySqlUtils::prepareAndGetResult($this->context, $query, 'i', $user->id);

        $fetchedRoleName = $response->fetch_assoc()['Name'] ?? null;
        $response->free_result();

        return $fetchedRoleName === $roleName;
    }

    /**
     * @throws StatementPrepareFailedException
     * @throws QueryFailedException
     */
    public function changeUserRole(User $user, Role $role): void
    {
        $query = "UPDATE Users SET RoleId = ? WHERE Id = ?";
        MySqlUtils::prepareAndExecute($this->context, $query, 'ii', $role->id, $user->id);
    }

    /**
     * @throws StatementPrepareFailedException
     * @throws QueryFailedException
     */
    public function deleteRole(int $roleId): void
    {
        $query = "DELETE FROM Roles WHERE Id = ?";
        MySqlUtils::prepareAndExecute($this->context, $query, 'i', $roleId);
    }


}