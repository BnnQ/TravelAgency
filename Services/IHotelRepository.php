<?php

namespace Services;

use Models\Entities\Hotel;

interface IHotelRepository
{
    /**
     * @return Hotel[]
     */
    public function getAll(): array;

    public function add(Hotel $hotel): void;

    public function delete(int $id): void;
}