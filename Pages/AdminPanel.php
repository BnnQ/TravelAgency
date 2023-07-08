<?php

namespace Pages;

use DependencyContainer;
use Exceptions\LoginAlreadyTakenException;
use Exceptions\ValueInvalidationException;
use Models\Entities\City;
use Models\Entities\Country;
use Models\Entities\Hotel;
use Models\ImageDto;
use Services\ICityRepository;
use Services\ICountryRepository;
use Services\IHotelRepository;
use Services\IImageRepository;
use Services\IRoleManager;use Services\UserManagerBase;
use Utils\Router;

class AdminPanel
{
    public function __construct(public readonly UserManagerBase $userManager, public readonly IRoleManager $roleManager, public readonly ICountryRepository $countryRepository, public readonly ICityRepository $cityRepository, public readonly IHotelRepository $hotelRepository, public readonly IImageRepository $imageRepository)
    {
        //empty
    }
}

$component = DependencyContainer::getContainer()->get(AdminPanel::class);
if (!$component->userManager->isCurrentUserAuthenticated()) {
    Router::redirectToLocalPageByKey('login');
    return;
}
if (!$component->roleManager->isUserInRole($component->userManager->getCurrentUser(), "Admin")) {
    http_response_code(403);
    echo "<h1 class='text-center'>Access Denied</h1>";
    return;
}

if (isset($_POST['deleteCountry'])) {
    if (isset($_POST['countries'])) {
        $countries = $_POST['countries'];
        foreach ($countries as $country) {
            $component->countryRepository->delete($country);
        }
    } else {
        $errorMessage = "Select at least one country.";
        include "ErrorToast.php";
    }

    Router::redirectToLocalPageByKey(ROUTE_Admin);
}
else if (isset($_POST['addCountry'])) {
    $country = Country::parseFromAssoc($_POST);
    $component->countryRepository->add($country);

    Router::redirectToLocalPageByKey(ROUTE_Admin);
}
else if (isset($_POST['deleteCity'])) {
    if (isset($_POST['cities'])) {
        $cities = $_POST['cities'];
        foreach ($cities as $cityId) {
            $component->cityRepository->delete($cityId);
        }

        Router::redirectToLocalPageByKey(ROUTE_Admin);
    } else {
        $errorMessage = "Select at least one city.";
        include "ErrorToast.php";
    }
}
else if (isset($_POST['addCity'])) {
    $city = City::parseFromAssoc($_POST);
    $component->cityRepository->add($city);

    Router::redirectToLocalPageByKey(ROUTE_Admin);
}
else if (isset($_POST['deleteHotel'])) {
    if (isset($_POST['hotels'])) {
        $hotels = $_POST['hotels'];
        foreach ($hotels as $hotelId) {
            $component->hotelRepository->delete($hotelId);
        }

        Router::redirectToLocalPageByKey(ROUTE_Admin);
    } else {
        $errorMessage = "Select at least one hotel.";
        include "ErrorToast.php";
    }
}
else if (isset($_POST['addHotel'])) {
    $hotel = Hotel::parseFromAssoc($_POST);
    $component->hotelRepository->add($hotel);
    Router::redirectToLocalPageByKey(ROUTE_Admin);
}
else if (isset($_POST['addUser'])) {
    $avatarBytes = null;
    if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
        $avatarFileName = $_FILES['avatar']['tmp_name'];
        $avatarBytes = file_get_contents($avatarFileName);
    }

    try {
        $component->userManager->signUpUser($_POST['login'], $_POST['password'], $_POST['email'], $_POST['roleName'], $_POST['discount'], $avatarBytes);
    } catch (LoginAlreadyTakenException $exception) {
        $errorMessage = "Login already taken!";
        include "ErrorToast.php";
    } catch (ValueInvalidationException $exception) {
        $errorMessage = $exception->getMessage();
        include "ErrorToast.php";
    }

    Router::redirectToLocalPageByKey(ROUTE_Admin);
}
else if (isset($_POST['deleteImage'])) {
    if (isset($_POST['images'])) {
        $images = $_POST['images'];
        foreach ($images as $imageId) {
            $component->imageRepository->delete($imageId);
        }

        Router::redirectToLocalPageByKey(ROUTE_Admin);
    } else {
        $errorMessage = "Select at least one image.";
        include "ErrorToast.php";
    }
}
else if (isset($_POST['addImage'])) {
    if (isset($_FILES['images'])) {
        $hotelName = $_POST['hotelName'];
        foreach ($_FILES['images']['name'] as $key=>$_) {
            if ($_FILES['images']['error'][$key] != 0)
                continue;

            $uploadedImage = new ImageDto();
            $uploadedImage->hotelName = $hotelName;

            $uploadedImage->uploadedFile = [
                'tmp_name' => $_FILES['images']['tmp_name'][$key]
            ];

            $component->imageRepository->add($uploadedImage);
            Router::redirectToLocalPageByKey(ROUTE_Admin);
        }
    } else {
        $errorMessage = "You should upload at least one image.";
        include "ErrorToast.php";
    }
}

?>

<link rel="stylesheet" type="text/css" href="/TravelAgency/wwwroot/stylesheets/admin.css"/>
<div id="body" class="container my-5">
    <h1 class="text-center mb-4">Admin Panel</h1>
    <div class="row row-cols-1 row-cols-md-2 g-4">
        <!-- Countries Section -->
        <div class="col">
            <div class="card h-100">
                <div class="card-header">Countries</div>
                <div class="card-body">
                    <!-- Table -->
                    <form method="post" class="table-container">
                        <table class="table table-bordered table-hover mb-3">
                            <thead>
                            <tr>
                                <th>Id</th>
                                <th>Name</th>
                                <th>Select</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php
                            $countries = $component->countryRepository->getAll();
                            foreach ($countries as $country) {
                                echo "<tr><td>$country->id</td><td>$country->name</td><td><input type='checkbox' class='form-check' name='countries[]' value='$country->id'/></td></tr>";
                            }
                            ?>
                            </tbody>
                        </table>
                        <!-- Delete Button -->
                        <button type="submit" name="deleteCountry" class="btn btn-danger my-3">
                            Delete Selected
                        </button>
                    </form>
                    <!-- Add Form -->
                    <form action="" method="post">
                        <div class="mb-3">
                            <label for="countryName" class="form-label">Name</label>
                            <input type="text" class="form-control" id="countryName" name="name" required/>
                        </div>
                        <button type="submit" name="addCountry" class="btn btn-primary">Add
                            Country
                        </button>
                    </form>
                </div>
            </div>
        </div>
        <!-- Cities Section -->
        <div class="col">
            <div class="card h-100">
                <div class="card-header">Cities</div>
                <div class="card-body">
                    <!-- Table -->
                    <form method="post" class="table-container">
                        <table class="table table-bordered table-hover mb-3">
                            <thead>
                            <tr>
                                <th>Id</th>
                                <th>Name</th>
                                <th>Country Name</th>
                                <th>Select</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php
                            $cities = $component->cityRepository->getAll();
                            foreach ($cities as $city) {
                                echo "<tr><td>$city->id</td><td>$city->name</td><td>$city->countryName</td><td><input type='checkbox' class='form-check' name='cities[]' value='$city->id'/></td></tr>";
                            }
                            ?>
                            </tbody>
                        </table>
                        <!-- Delete Button -->
                        <button type="submit" name="deleteCity" class="btn btn-danger my-3">Delete
                            Selected
                        </button>
                    </form>
                    <!-- Add Form -->
                    <form action="" method="post">
                        <div class="mb-3">
                            <label for="cityName" class="form-label">Name</label>
                            <input type="text" class="form-control" id="cityName" name="name" required/>
                        </div>
                        <div class="mb-3">
                            <label for="countryOfCity" class="form-label">Country</label>
                            <select class="form-select" name="countryName" id="countryOfCity" required>
                                <option value="" disabled selected>-- Choose a country --</option>
                                <?php
                                foreach ($countries as $country) {
                                    echo "<option value='$country->name'>$country->name</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <button type="submit" name="addCity" class="btn btn-primary">Add City
                        </button>
                    </form>
                </div>
            </div>
        </div>
        <!-- Hotels Section -->
        <div class="col">
            <div class="card h-100">
                <div class="card-header">Hotels</div>
                <div class="card-body">
                    <!-- Table -->
                    <form method="post" class="table-container">
                        <table class="table table-bordered table-hover mb-3">
                            <thead>
                            <tr>
                                <th>Id</th>
                                <th>Name</th>
                                <th>Country Name</th>
                                <th>City Name</th>
                                <th>Stars</th>
                                <th>Cost</th>
                                <th>Info</th>
                                <th>Select</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php
                            $hotels = $component->hotelRepository->getAll();
                            foreach ($hotels as $hotel) {
                                echo "<tr><td>$hotel->id</td><td>$hotel->name</td><td>$hotel->countryName</td><td>$hotel->cityName</td><td>$hotel->stars</td><td>$hotel->cost</td><td>$hotel->info</td><td><input type='checkbox' class='form-check' name='hotels[]' value='$hotel->id'/></td></tr>";
                            }
                            ?>
                            </tbody>
                        </table>
                        <!-- Delete Button -->
                        <button type="submit" name="deleteHotel" class="btn btn-danger my-3">Delete
                            Selected
                        </button>
                    </form>
                    <!-- Add Form -->
                    <form action="" method="post">
                        <div class="mb-3">
                            <label for="hotelName" class="form-label">Name</label>
                            <input type="text" class="form-control" id="hotelName" name="name" required/>
                        </div>
                        <div class="mb-3">
                            <label for="countryOfHotel" class="form-label">Country</label>
                            <select class="form-select" name="countryName" id="countryOfHotel" required>
                                <option value="" disabled selected>-- Choose a country --</option>
                                <?php
                                foreach ($countries as $country) {
                                    echo "<option value='$country->name'>$country->name</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="cityOfHotel" class="form-label">City</label>
                            <select class="form-select" name="cityName" id="cityOfHotel" required>
                                <option value="" disabled selected>-- Choose a city --</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="hotelStars" class="form-label">Stars</label>
                            <select class="form-select" name="stars" id="hotelStars" required>
                                <option value="1">1</option>
                                <option value="2">2</option>
                                <option value="3">3</option>
                                <option value="4">4</option>
                                <option value="5">5</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="hotelCost" class="form-label">Cost</label>
                            <input type="number" class="form-control" id="hotelCost" name="cost" required/>
                        </div>
                        <div class="mb-3">
                            <label for="hotelInfo" class="form-label">Information</label>
                            <textarea class="form-control" id="hotelInfo" name="info"></textarea>
                        </div>
                        <button type="submit" name="addHotel" class="btn btn-primary">Add Hotel
                        </button>
                    </form>
                </div>
            </div>
        </div>
        <!-- Users Section -->
        <div class="col">
            <div class="card h-100">
                <div class="card-header">Users</div>
                <div class="card-body">
                    <!-- Table -->
                    <div class="table-container">
                        <table class="table table-bordered table-hover mb-3">
                            <thead>
                            <tr>
                                <th>Id</th>
                                <th>Login</th>
                                <th>Role Name</th>
                                <th>Discount</th>
                                <th>Avatar</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php
                            $users = $component->userManager->getUsers();
                            foreach ($users as $user) {
                                $avatar = null;
                                if ($user->avatar)
                                    $avatar = base64_encode($user->avatar);

                                echo "<tr><td>$user->id</td><td>$user->login</td><td>$user->roleName</td><td>$user->discount</td><td>" . ($avatar ? "<img height='100px' src='data:image/png;base64,$avatar' alt='Avatar'/>" : "<div>No avatar</div>") . "</tr>";
                            }
                            ?>
                            </tbody>
                        </table>
                    </div>
                    <!-- Add Form -->
                    <form action="" method="post" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label for="login" class="form-label">Login</label>
                            <input type="text" class="form-control" id="login" name="login" required/>
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" class="form-control" id="password" name="password" required/>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email" required/>
                        </div>
                        <div class="mb-3">
                            <label for="roles" class="form-label">Roles</label>
                            <select class="form-select" name="roleName" id="roles" required>
                                <option value="" disabled selected>-- Choose a role --</option>
                            <?php
                            $roles = [ROLE_User, ROLE_Admin];
                            foreach ($roles as $roleName) {
                                echo "<option value='$roleName'>$roleName</option>";
                            }
                            ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="discount" class="form-label">Discount</label>
                            <input type="number" value="0.0" class="form-control" id="discount" name="discount" required/>
                        </div>
                        <div class="mb-3">
                            <label for="avatar" class="form-label">Avatar</label>
                            <input type="file" class="form-control" id="avatar" name="avatar" />
                        </div>
                        <button type="submit" name="addUser" class="btn btn-primary">Add
                            User
                        </button>
                    </form>
                </div>
            </div>
        </div>
        <!-- Images Section -->
        <div class="col">
            <div class="card h-100">
                <div class="card-header">Images</div>
                <div class="card-body">
                    <!-- Table -->
                    <form method="post" class="table-container" enctype="multipart/form-data">
                        <table class="table table-bordered table-hover mb-3">
                            <thead>
                            <tr>
                                <th>Id</th>
                                <th>Hotel</th>
                                <th>Image</th>
                                <th>Select</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php
                            $images = $component->imageRepository->getAll();
                            foreach ($images as $image) {
                                echo "<tr><td>$image->id</td><td>$image->hotelName</td><td><img height='100px' src='$image->imagePath' alt='hotel image'/></td><td><input type='checkbox' class='form-check' name='images[]' value='$image->id'/></td></tr>";
                            }
                            ?>
                            </tbody>
                        </table>
                        <!-- Delete Button -->
                        <button type="submit" name="deleteImage" class="btn btn-danger my-3">
                            Delete Selected
                        </button>
                    </form>
                    <!-- Add Form -->
                    <form action="" method="post" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label for="hotelOfImage" class="form-label">Hotel</label>
                            <select class="form-select" name="hotelName" id="hotelOfImage" required>
                                <option value="" disabled selected>-- Choose a hotel --</option>
                                <?php
                                foreach ($hotels as $hotel) {
                                    echo "<option value='$hotel->name'>$hotel->name</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="images" class="form-label">Image</label>
                            <input type="file" class="form-control" id="images" name="images[]" multiple required/>
                        </div>
                        <button type="submit" name="addImage" class="btn btn-primary">Add
                            Image
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    function updateCities() {
        const country = $("#countryOfHotel").val();

        $.ajax({
            method: "POST",
            url: "GetCities.php",
            data: { country }
        })
            .done(function(response) {
                const cities = response.cities;
                const citySelect = $('#cityOfHotel');
                citySelect.empty();

                cities.forEach(function(city) {
                    citySelect.append('<option value="' + city.name + '">' + city.name + '</option>');
                });
            })
            .fail(function(error) {
                console.log('error: ' + error);
            });
    }

    $('#countryOfHotel').on('change', updateCities);
</script>