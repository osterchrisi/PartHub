<?php

namespace App\Http\Controllers;

use App\Models\Image;
use App\Services\ImageService;
use App\Traits\UploadsFiles;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ImageController extends Controller
{
    use UploadsFiles;

    /**
     * Uploads an image file and stores it on the server.
     *
     * @param  \Illuminate\Http\Request  $request  The HTTP request object.
     * @param  string  $type  The type of entity the image belongs to (e.g., 'part', 'location', etc.).
     * @param  int  $id  The ID of the entity the image belongs to.
     * @return \Illuminate\Http\JsonResponse A JSON response indicating the success of the upload.
     *
     * @throws \Illuminate\Validation\ValidationException If validation fails for the image upload.
     */
    public function upload(Request $request, $type, $id)
    {
        $validationRules = ['required', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'];
        $uploadResult = $this->uploadFile($request, 'image', 'image', $type, $id, $validationRules);
        $filePath = $uploadResult['filePath'];
        $originalFilename = $uploadResult['originalFilename'];

        // Save image details to database
        $imageModel = new Image();
        $imageModel->filename = $filePath;
        $imageModel->display_name = $originalFilename;
        $imageModel->image_owner_u_id = auth()->id();   // User that owns this image / uploaded it
        $imageModel->type = $type;                      // Type = part, location, ...
        $imageModel->associated_id = $id;               // ID of the entity
        $imageModel->save();

        ImageService::createThumbnail(dirname($filePath), basename($filePath), pathinfo($filePath, PATHINFO_FILENAME));

        return response()->json(['success' => 'Image uploaded successfully']);
    }

    public function getImagesByTypeAndId($type, $id)
    {
        // Retrieve images associated with the part ID
        $images = Image::where('associated_id', $id)
            ->where('type', $type)                      //type = part, location, supplier, ...
            ->where('image_owner_u_id', auth()->id())
            ->orderBy('order', 'asc')
            ->get();

        return response()->json($images);
    }

    public function deleteImage($type, $id)
    {
        try {
            // Find the image by ID, type, and owner
            $image = Image::where('id', $id)
                ->where('type', $type)
                ->where('image_owner_u_id', auth()->id())
                ->first();

            if (! $image) {
                return response()->json(['error' => 'Image not found or not authorized'], 404);
            }

            DB::beginTransaction();

            // Delete the image file
            $this->deleteFile($image->filename);

            // Delete the thumbnail file if it exists
            $thumbnailPath = str_replace(
                basename($image->filename),
                'thumbnails/'.pathinfo($image->filename, PATHINFO_FILENAME).'.webp',
                $image->filename
            );
            $this->deleteFile($thumbnailPath);

            // Delete the image record from the database
            $image->delete();

            DB::commit();

            return response()->json(['success' => 'Image deleted successfully']);
        } catch (\Exception $e) {
            DB::rollback();

            return response()->json(['error' => 'An error occurred: '.$e->getMessage()], 500);
        }
    }

    public function reorderImages(Request $request, $type, $id)
    {
        $imageOrder = $request->input('imageOrder');

        // Loop through the imageOrder array and update each image's order in the database
        foreach ($imageOrder as $orderData) {
            $imageId = $orderData['id'];
            $newOrder = $orderData['order'];

            // Update the image's order in the database
            Image::where('id', $imageId)->update(['order' => $newOrder]);
        }

        // Fetch and return only the first image (lowest order)
        $mainImage = Image::where('associated_id', $id)
            ->where('type', $type)
            ->orderBy('order', 'asc')
            ->first();

        return response()->json($mainImage);
    }
}
