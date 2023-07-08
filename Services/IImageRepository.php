<?php

namespace Services;

use Models\Entities\Image;
use Models\ImageDto;

interface IImageRepository
{
    /**
     * @return Image[]
     */
    public function getAll(): array;

    public function get(int $id): Image;

    public function add(ImageDto $uploadedImage): void;

    public function delete(int $id): void;
}