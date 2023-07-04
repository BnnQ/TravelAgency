<?php

namespace Pages;

use DependencyContainer;
use Models\Entities\City;
use Models\Entities\Country;
use Models\Entities\Hotel;
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
        foreach ($countries as $countryId) {
            $component->countryRepository->delete($countryId);
        }
    } else {
        $errorMessage = "Select at least one country.";
        include "ErrorToast.php";
    }

    Router::redirectToLocalPageByKey('admin');
}
else if (isset($_POST['addCountry'])) {
    $country = Country::parseFromAssoc($_POST);
    $component->countryRepository->add($country);

    Router::redirectToLocalPageByKey('admin');
}
else if (isset($_POST['deleteCity'])) {
    if (isset($_POST['cities'])) {
        $cities = $_POST['cities'];
        foreach ($cities as $cityId) {
            $component->cityRepository->delete($cityId);
        }

        Router::redirectToLocalPageByKey('admin');
    } else {
        $errorMessage = "Select at least one city.";
        include "ErrorToast.php";
    }
}
else if (isset($_POST['addCity'])) {
    $city = City::parseFromAssoc($_POST);
    $component->cityRepository->add($city);

    Router::redirectToLocalPageByKey('admin');
}
else if (isset($_POST['deleteHotel'])) {
    if (isset($_POST['hotels'])) {
        $hotels = $_POST['hotels'];
        foreach ($hotels as $hotelId) {
            $component->hotelRepository->delete($hotelId);
        }

        Router::redirectToLocalPageByKey('admin');
    } else {
        $errorMessage = "Select at least one hotel.";
        include "ErrorToast.php";
    }
}
else if (isset($_POST['addHotel'])) {
    $hotel = Hotel::parseFromAssoc($_POST);
    $component->hotelRepository->add($hotel);
    Router::redirectToLocalPageByKey('admin');
}

?>

<link rel="stylesheet" type="text/css" href="/TravelAgency/wwwroot/stylesheets/admin.css"/>
<div id="body" class="container my-5">
    <h1 class="text-center mb-4">Admin Panel</h1>
    <div class="row row-cols-1 row-cols-md-3 g-4">
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
                            <!-- PHP code to loop through countries and display them in the table -->
                            <?php
                            $countries = $component->countryRepository->getAll();
                            foreach ($countries as $countryId) {
                                echo "<tr><td>$countryId->id</td><td>$countryId->name</td><td><input type='checkbox' class='form-check' name='countries[]' value='$countryId->id'/></td></tr>";
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
                            <!-- PHP code to loop through countries and display them in the table -->
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
                                foreach ($countries as $countryId) {
                                    echo "<option value='$countryId->name'>$countryId->name</option>";
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
                            <!-- PHP code to loop through countries and display them in the table -->
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
                            <label for="countryName" class="form-label">Name</label>
                            <input type="text" class="form-control" id="countryName" name="name" required/>
                        </div>
                        <div class="mb-3">
                            <label for="countryOfHotel" class="form-label">Country</label>
                            <select class="form-select" name="countryName" id="countryOfHotel" required>
                                <option value="" disabled selected>-- Choose a country --</option>
                                <?php
                                foreach ($countries as $countryId) {
                                    echo "<option value='$countryId->name'>$countryId->name</option>";
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