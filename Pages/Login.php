<?php

namespace Pages;

use DependencyContainer;
use Exceptions\InvalidCredentialsException;
use Exceptions\UserNotFoundException;
use Services\UserManagerBase;
use Utils\Router;


require_once "Utils\RouteConstants.php";

class Login
{
    public function __construct(public readonly UserManagerBase $userManager)
    {

    }
}

$component = DependencyContainer::getContainer()->get(Login::class);
?>

<?php
if (isset($_POST['submit'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    try {
        if ($component->userManager->verifyAndSignInUser($username, $password)) {
            Router::redirectToLocalPageByKey(ROUTE_Tours);
        } else {
            $errorMessage = "Invalid username or password.";
            include "ErrorToast.php";
        }
    } catch (UserNotFoundException $exception) {
        $errorMessage = "User with this username does not exist.";
        include "ErrorToast.php";
    }
}
?>

<div id="body" class="d-flex flex-column align-items-center justify-content-center min-vh-100">
    <div id="form-wrapper" class="container">
        <div class="text-center">
            <h1 class="fw-bold mb-4" style="font-size: 35px">Login</h1>
            <h2 class="mb-4" style="font-size: 20px">Welcome back</h2>
        </div>

        <div class="pb-3 text-center">
            <a href="/TravelAgency/Outlet.php?page=register" class="btn btn-primary btn-block create-btn w-50">
                Join
            </a>
        </div>

        <form method="POST" action="/TravelAgency/Outlet.php?page=login">
            <div class="form-group mb-1">
                <label class="form-label" for="username"></label>
                <input name="username" id="username" class="form-control" placeholder="username" required/>
            </div>
            <div class="form-group mb-3">
                <label class="form-label" for="password"></label>
                <input name="password" id="password" type="password" class="form-control" placeholder="••••••••"
                       required/>
            </div>
            <div>
                <button type="submit" name="submit" id="submit" class="btn btn-primary text-nowrap fw-bold w-100"
                        style="border-radius: 2px; padding: 7px 0;  font-size: 18px">
                    Login
                </button>
            </div>
        </form>

    </div>
</div>