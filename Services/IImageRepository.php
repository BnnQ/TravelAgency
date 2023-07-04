<?php

namespace Services;

use Models\Entities\Image;

interface IImageRepository
{
    /**
     * @return Image[]
     */
    public function getAll(): array;

    public function get(int $id): Image;

    public function add(Image $image): void;

    public function delete(int $id): void;
}