<?php

namespace test_is74\Helpers;

class ImageHelper
{
    public static function saveUploadedImage(int $tariff_id) : void
    {
        $filePath = APP_DIR . "static/upload/tariff_$tariff_id.jpg";
        if (file_exists($filePath))
        {
            unlink($filePath);
        }

        $source = $_FILES["image"]["tmp_name"];
        move_uploaded_file($source, $filePath);

        self::compressImage($filePath);
    }

    public static function compressImage(string $filePath) : void
    {
        $info = getimagesize($filePath);

        switch ($info["mime"])
        {
            case "image/jpeg":
                $image = imagecreatefromjpeg($filePath);
                break;

            case "image/png":
                $image = imagecreatefrompng($filePath);
                break;

            default:
                return;
        }

        imagejpeg($image, $filePath, 75);
    }
}