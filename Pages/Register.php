<?php

namespace Pages;
use DependencyContainer;
use Exceptions\LoginAlreadyTakenException;
use Models\ValidationRule;
use Services\UserManagerBase;
use Services\ValidatorBuilder;
use Utils\Router;

class Register
{
    public function __construct(public readonly UserManagerBase $userManager)
    {
        //empty
    }
}

$component = DependencyContainer::getContainer()->get(Register::class);

if (isset($_POST['submit'])) {
    $login = $_POST['login'];
    $password = $_POST['password'];
    $email = $_POST['email'];

    $loginPattern = '/^[A-Za-z\d_]{3,27}$/';
    $passwordPattern = '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_])[A-Za-z\d\W_]{8,27}$/';
    $emailPattern = '/^\b[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\.[A-Za-z]{2,}\b$/';
    $validator = (new ValidatorBuilder())
        ->addRule(new ValidationRule(preg_match($loginPattern, $login), "Login doesnt meet the requirements:\n• Can only contain latin letters, digits, and underscores\n• A length must be at least 3 characters and at most 27 characters"))
        ->addRule(new ValidationRule(preg_match($passwordPattern, $password), "Password doesnt meet the requirements:\n• Must contain at least one lowercase letter\n• Must contain at least one uppercase letter\n• Must contain at least one digit\n• Must contain at least one special character\n• A length must be at least 8 characters and at most 27 characters"))
        ->addRule(new ValidationRule(preg_match($emailPattern, $email), "Entered value is not valid email address"))
        ->build();
    if ($errorMessage = $validator->validateSoft()) {
        include "ErrorToast.php";
    } else {
        try {
            $component->userManager->signUpUser($login, $password, $email);
            Router::redirectToLocalPageByKey(ROUTE_Tours);
        } catch (LoginAlreadyTakenException $exception) {
            $errorMessage = "Login already taken!";
            include "ErrorToast.php";
        }
    }

}
?>

<div id="body" class="container d-flex align-items-center justify-content-center p-0 min-vh-100 w-100 ">
            <div id="form-wrapper" class="container-fluid">
                <div class="text-center">
                    <h1 class="fw-bold mb-4" style="font-size: 35px">Join TravelUp</h1>
                    <h2 class="mb-4 label-sm text-light-gray">Already have an account? <a
                            href="/TravelAgency/Outlet.php?page=login"
                            class="d-inline text-primary">Login</a>
                    </h2>
                </div>

                <form method="POST" action="/TravelAgency/Outlet.php?page=register">
                    <div class="row mb-1">
                        <div class="form-group">
                            <label class="form-label" for="login">Login</label>
                            <input name="login" id="login" class="form-control" required/>
                        </div>
                    </div>
                    <div class="row mb-1">
                        <div class="form-group">
                            <label class="form-label" for="email">Email</label>
                            <input name="email" id="email" type="email" class="form-control"
                                   placeholder="example@mail.com" required/>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="form-group">
                            <label class="form-label" for="password">Password</label>
                            <input name="password" id="password" type="password"
                                   class="form-control"
                                   placeholder="••••••••" required/>
                        </div>
                    </div>

                    <div>
                        <button type="submit" name="submit" id="submit"
                                class="btn btn-primary text-nowrap fw-bold w-100"
                                style="border-radius: 2px; padding: 7px 0;  font-size: 18px">
                            Join
                        </button>
                    </div>
                </form>
            </div>
</div>