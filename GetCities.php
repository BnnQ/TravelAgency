<?php

use Services\ICityRepository;

require_once 'DependencyContainer.php';
require_once 'vendor/autoload.php';

class GetCities
{
    public function __construct(public readonly ICityRepository $cityRepository)
    {
        $cities = $cityRepository->getByCountryName($_POST['country']);

        $response = [
            'cities' => $cities
        ];

        header('Content-Type: application/json');
        echo json_encode($response);
    }
}

$component = DependencyContainer::getContainer()->get(GetCities::class);