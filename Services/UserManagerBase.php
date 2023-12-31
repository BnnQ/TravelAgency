<?php

namespace Services;

require_once "Utils\RouteConstants.php";
require_once "Utils\RoleConstants.php";
use Exceptions\UserNotFoundException;
use Models\Entities\User;

abstract class UserManagerBase
{
    /**
     * @return User[]
     */
    public abstract function getUsers(): array;
    public abstract function getUser(string $login): User;
    public abstract function signUpUser(string $login, string $password, string $email, string $roleName = ROLE_User, float $discount = 0.0, ?string $avatar = null): void;
    public abstract function verifyAndSignInUser(string $login, string $password): bool;
    public abstract function signInUser(string $login): void;
    public abstract function signOutUser(): void;
    public abstract function getCurrentUser(): User;
    public function isCurrentUserAuthenticated(): bool {
        try {
            $currentUser = $this->getCurrentUser();
            $token = $_COOKIE[COOKIE_AuthenticationToken] ?? null;
            return isset($token) && $token === $currentUser->lastAuthenticationToken;
        } catch (UserNotFoundException $exception) {
            return false;
        }
    }
}