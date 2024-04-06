<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Image;

class ImageController extends Controller
{
    /**
     * Uploads an image file and stores it on the server.
     *
     * @param \Illuminate\Http\Request $request The HTTP request object.
     * @param string $type The type of entity the image belongs to (e.g., 'part', 'location', etc.).
     * @param int $id The ID of the entity the image belongs to.
     * @return \Illuminate\Http\JsonResponse A JSON response indicating the success of the upload.
     *
     * @throws \Illuminate\Validation\ValidationException If validation fails for the image upload.
     */
    public function upload(Request $request, $type, $id)
    {
        // Validate the request
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Retrieve image from request
        $image = $request->file('image');

        // Retrieve the original filename
        $originalFilename = $image->getClientOriginalName();

        // Extract the filename without extension
        $filenameWithoutExtension = pathinfo($originalFilename, PATHINFO_FILENAME);

        // Sanitize the filename to remove special characters
        $sanitizedFilename = preg_replace("/[^a-zA-Z0-9_.]/", "", $filenameWithoutExtension);

        // Generate a unique filename using the sanitized original filename and current timestamp
        $imageName = $sanitizedFilename . '_' . time() . '.' . $image->extension();
        $user_id = auth()->id();

        // Define the directory path based on the type of entity
        $directory = 'storage/images/' . $type . '/' . $user_id . '/' . $id; // Ends up e.g. /stoarge/images/part/355/filename.jpg

        // Create the directory if it doesn't exist
        if (!file_exists(public_path($directory))) {
            mkdir(public_path($directory), 0755, true);
        }

        // Move the image to the directory
        $image->move(public_path($directory), $imageName);

        // Save image details to database
        $imageModel = new Image();
        $imageModel->filename = $directory . '/' . $imageName;
        $imageModel->image_owner_u_id = $user_id;   // User that owns this image / uploaded it
        $imageModel->type = $type;                  // Type = part, location, ...
        $imageModel->associated_id = $id;           // ID of the entity
        $imageModel->save();

        return response()->json(['success' => 'Image uploaded successfully']);
    }

    public function getImagesByTypeAndId($type, $id)
    {
        // Retrieve images associated with the part ID
        $images = Image::where('associated_id', $id)
            ->where('type', $type)
            ->get();

        return response()->json($images);
    }

}
