<?php

namespace Services;

use Models\Entities\Country;

interface ICountryRepository
{
    /**
     * @return Country[]
     */
    public function getAll(): array;

    public function add(Country $country): void;

    public function delete(int $id): void;
}