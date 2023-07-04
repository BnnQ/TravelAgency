<?php

namespace Services;

use Exceptions\LoginAlreadyTakenException;
use Exceptions\UserNotFoundException;
use Models\Entities\User;
use Models\ValidationRule;
use mysqli;
use QueryFailedException;
use StatementPrepareFailedException;
use Utils\MySqlUtils;
require_once "Utils\CookieConstants.php";
require_once "Utils\Utils.php";

class MySqlUserManager extends UserManagerBase
{
    public function __construct(private readonly mysqli $context, private readonly ITokenGenerator $tokenGenerator)
    {

    }

    /**
     * @throws StatementPrepareFailedException
     * @throws QueryFailedException
     * @throws UserNotFoundException
     */
    public function getUser(string $login): User
    {
        $query = "SELECT * FROM Users WHERE Login = ?";
        $response = MySqlUtils::prepareAndGetResult($this->context, $query, 's', $login);

        if ($response->num_rows < 1)
            throw new UserNotFoundException();

        $user = User::parseFromAssoc($response->fetch_assoc());
        $response->free_result();

        return $user;
    }

    /**
     * @throws StatementPrepareFailedException
     * @throws QueryFailedException
     * @throws LoginAlreadyTakenException
     * @throws UserNotFoundException
     */
    public function signUpUser(string $login, string $password, string $email): void
    {
        #region Validating values
        $query = "SELECT * FROM Users WHERE Login = ?";
        $response = MySqlUtils::prepareAndGetResult($this->context, $query, 's', $login);

        if ($response->num_rows > 0)
            throw new LoginAlreadyTakenException();

        $loginRegex = '/^[a-zA-Z0-9_]{3,27}$/';
        $passwordRegex = '/^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[^a-zA-Z\d\s]).{8,27}$/';
        $validator = (new ValidatorBuilder())
            ->addRule(new ValidationRule(preg_match($loginRegex, $login), "Login does not match requirements:\n•At least 3 characters\n•At most 27 characters\n•Can contain only letters, digits and underscores"))
            ->addRule(new ValidationRule(preg_match($passwordRegex, $password), "Password does not match requirements:\n•At least 8 characters\n•At most 27 characters\n•Must contain at least 1 uppercase letter\n•Must contain at least 1 lowercase letter\n•Must contain at least 1 digit\n•Must contain at least 1 special symbol\n•Must not contain simple words or repeating symbols"))
            ->build();

        if ($errorMessage = $validator->validateSoft()) {
            include "ErrorToast.php";
            return;
        }
        #endregion

        #region Registering user
        $query = "SELECT Id from Roles WHERE Name = ?";
        $response = MySqlUtils::prepareAndGetResult($this->context, $query, 's', "User");
        $roleId = $response->fetch_assoc()['Id'];
        $response->free_result();

        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $user = new User(id: null, login: $login, hashedPassword: $hashedPassword, email: $email, roleId: $roleId);

        $query = "INSERT INTO Users (Login, HashedPassword, Email, RoleId) VALUES (?, ?, ?, ?)";
        MySqlUtils::prepareAndExecute($this->context, $query, 'sssi', $user->login, $user->hashedPassword, $user->email, $user->roleId);
        #endregion

        $this->signInUser($user->login);
    }

    /**
     * @throws StatementPrepareFailedException
     * @throws UserNotFoundException
     * @throws QueryFailedException
     */
    public function signInUser(string $login): void
    {
        $user = $this->getUser($login);

        #region Setting cookie
        $authenticationToken = $login.'_'.$this->tokenGenerator->generateToken(length: 9);
        $secondsInMinute = 60;
        $minutesInHour = 60;
        $expiresTime = time() + ($secondsInMinute * $minutesInHour * 3);
        setcookie(COOKIE_AuthenticationToken, $authenticationToken, $expiresTime);
        #endregion

        #region Updating value in database
        $user->lastAuthenticationToken = $authenticationToken;
        $query = "UPDATE Users SET LastAuthenticationToken = ? WHERE Login = ?";
        MySqlUtils::prepareAndExecute($this->context, $query, 'ss', $user->lastAuthenticationToken, $user->login);
        #endregion
    }

    /**
     * @throws StatementPrepareFailedException
     * @throws UserNotFoundException
     * @throws QueryFailedException
     */
    public function verifyAndSignInUser(string $login, string $password): bool
    {
        $user = $this->getUser($login);
        if (password_verify($password, $user->hashedPassword)) {
            $this->signInUser($login);
            return true;
        }

        return false;
    }

    public function signOutUser(): void
    {
        unsetCookie(COOKIE_AuthenticationToken);
    }

    /**
     * @throws StatementPrepareFailedException
     * @throws UserNotFoundException
     * @throws QueryFailedException
     */
    public function getCurrentUser(): User
    {
        if (!isset($_COOKIE[COOKIE_AuthenticationToken]))
            throw new UserNotFoundException();

        $separator = '_';
        $splittedAuthenticationToken = explode($separator, $_COOKIE[COOKIE_AuthenticationToken]);
        $username = $splittedAuthenticationToken[0];

        return $this->getUser($username);
    }

}