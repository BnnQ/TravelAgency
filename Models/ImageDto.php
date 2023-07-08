<?php

namespace Models;

class ImageDto
{
    public function __construct(public mixed $uploadedFile = null, public string $hotelName = '')
    {
        //empty
    }
}