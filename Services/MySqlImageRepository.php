<?php

namespace Services;

use Models\Entities\Image;
use Models\ImageDto;
use mysqli;
use QueryFailedException;
use StatementPrepareFailedException;
use Utils\MySqlUtils;
require_once "Utils\Utils.php";

const PathToImageDirectory = "wwwroot/hotelImages/";

class MySqlImageRepository implements IImageRepository
{
    public function __construct(private readonly mysqli $context, private readonly ITokenGenerator $tokenGenerator)
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
            $image = Image::parseFromAssoc($row);
            $images[] = $image;
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
    public function add(ImageDto $uploadedImage): void
    {
        $query = "SELECT Id from Hotels WHERE Name = ?";
        $response = MySqlUtils::prepareAndGetResult($this->context, $query, 's', $uploadedImage->hotelName);
        $hotelId = $response->fetch_assoc()['Id'];
        $response->free_result();

        $pathToHotelImageDirectory = PathToImageDirectory."$hotelId/";
        if (!is_dir($pathToHotelImageDirectory))
            mkdir(directory: $pathToHotelImageDirectory, recursive: true);

        $pathToImage = $pathToHotelImageDirectory.$this->tokenGenerator->generateToken(8).".jpg";
        move_uploaded_file($uploadedImage->uploadedFile['tmp_name'], $pathToImage);

        $query = "INSERT INTO Images (HotelId, ImagePath) VALUES (?, ?)";
        MySqlUtils::prepareAndExecute($this->context, $query, 'is', $hotelId, $pathToImage);
    }

    /**
     * @throws StatementPrepareFailedException
     * @throws QueryFailedException
     */
    public function delete(int $id): void
    {
        #region Deleting image file from server
        $query = "SELECT ImagePath FROM Images WHERE Id = ?";
        $response = MySqlUtils::prepareAndGetResult($this->context, $query, 'i', $id);
        $pathToImage = $response->fetch_assoc()['ImagePath'];
        $response->free_result();
        unlink($pathToImage);

        $directoryName = dirname($pathToImage);
        if (isDirectoryEmpty($directoryName)) {
            rmdir($directoryName);
        }
        #endregion

        #region Deleting image from DB
        $query = "DELETE FROM Images WHERE Id = ?";
        MySqlUtils::prepareAndExecute($this->context, $query, 'i', $id);
        #endregion
    }

}