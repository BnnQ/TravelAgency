<?php

namespace Services;

use Models\Entities\Image;
use mysqli;
use QueryFailedException;
use StatementPrepareFailedException;
use Utils\MySqlUtils;

class MySqlImageRepository implements IImageRepository
{
    public function __construct(private readonly mysqli $context)
    {
        //empty
    }

    /**
     * @inheritDoc
     * @throws QueryFailedException
     */
    public function getAll(): array
    {
        $query = "SELECT Images.Id, Images.ImagePath, H.Name as HotelName FROM Images JOIN Hotels H on H.Id = Images.HotelId";
        $response = $this->context->query($query);
        if (!$response)
            throw new QueryFailedException($query, $this->context->error);

        $images = [];
        while ($row = $response->fetch_assoc()) {
            $images = Image::parseFromAssoc($row);
            $images[] = $images;
        }

        $response->free_result();
        return $images;
    }

    /**
     * @throws StatementPrepareFailedException
     * @throws QueryFailedException
     */
    public function get(int $id): Image
    {
        $query = "SELECT Images.Id, Images.ImagePath, H.Name as HotelName FROM Images JOIN Hotels H on H.Id = Images.HotelId WHERE Images.Id = ?";
        $response = MySqlUtils::prepareAndGetResult($this->context, $query, 'i', $id);

        $image = Image::parseFromAssoc($response->fetch_assoc());
        $response->free_result();
        return $image;
    }

    /**
     * @throws StatementPrepareFailedException
     * @throws QueryFailedException
     */
    public function add(Image $image): void
    {
        $query = "SELECT Id from Hotels WHERE Name = ?";
        $response = MySqlUtils::prepareAndGetResult($this->context, $query, 's', $image->hotelName);
        $hotelId = $response->fetch_assoc()['Id'];
        $response->free_result();

        $query = "INSERT INTO Images (HotelId, ImagePath) VALUES (?, ?)";
        MySqlUtils::prepareAndExecute($this->context, $query, 'is', $hotelId, $image->imagePath);
    }

    /**
     * @throws StatementPrepareFailedException
     * @throws QueryFailedException
     */
    public function delete(int $id): void
    {
        $query = "DELETE FROM Images WHERE Id = ?";
        MySqlUtils::prepareAndExecute($this->context, $query, 'i', $id);
    }

}