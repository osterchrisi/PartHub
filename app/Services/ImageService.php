<?php

namespace App\Services;

use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;

class ImageService
{
    public static function createThumbnail($directory, $imageName, $imageNameWithoutExtension)
    {
        // Open and scale image
        $filepath = storage_path('app/'.$directory.'/'.$imageName);
        $manager = new ImageManager(new Driver());
        $image = $manager->read($filepath)->coverDown(100, 100);

        // Saving location
        $thumbnail_directory = storage_path('app/'.$directory.'/thumbnails');
        $thumbnail_filepath = $thumbnail_directory.'/'.$imageNameWithoutExtension.'.webp';

        // Create the directory if it doesn't exist
        if (! file_exists($thumbnail_directory)) {
            mkdir($thumbnail_directory, 0755, true);
        }

        // Save and return
        $image->save($thumbnail_filepath);

        return $thumbnail_filepath;
    }
}
