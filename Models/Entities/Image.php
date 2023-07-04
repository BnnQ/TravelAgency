<?php

namespace Models\Entities;

class Image
{
    public function __construct(public ?int $id, public string $hotelName, public string $imagePath)
    {
        //empty
    }

    public static function parseFromAssoc(array $associativeArray): Image {
        return new Image(id: @$associativeArray['id'] ?? @$associativeArray['Id'], hotelName: @$associativeArray['hotelName'] ?? @$associativeArray['HotelName'], imagePath: @$associativeArray['imagePath'] ?? @$associativeArray['ImagePath']);
    }
}