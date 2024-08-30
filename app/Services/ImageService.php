<?php

namespace App\Services;

use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;

class ImageService
{
    public static function createThumbnail($directory, $imageName, $imageNameWithoutExtension)
    {
        // Open and scale image
        $filepath = $directory.'/'.$imageName;
        $manager = new ImageManager(new Driver());
        $image = $manager->read($filepath);
        $image->coverDown(100, 100);

        // Saving location
        $thumbnail_directory = $directory.'/thumbnails';
        $thumbnail_filepath = $thumbnail_directory.'/'.$imageNameWithoutExtension;
        // Create the directory if it doesn't exist
        if (! file_exists(public_path($thumbnail_directory))) {
            mkdir(public_path($thumbnail_directory), 0755, true);
        }

        // Save and return
        $image->save($thumbnail_filepath.'.'.'webp');

        return $thumbnail_filepath;
    }
}
