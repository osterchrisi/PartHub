<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Image;

class ImageController extends Controller
{
    public function upload(Request $request, $type)
    {
        // dd($request);
        // Validate the request
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Store the image
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
        $directory = 'images/' . $type . '/' . $user_id;

        // Create the directory if it doesn't exist
        if (!file_exists(public_path($directory))) {
            mkdir(public_path($directory), 0755, true);
        }

        // Move the image to the directory
        $image->move(public_path($directory), $imageName);

        // Save image details to database
        $imageModel = new Image();
        $imageModel->filename = $directory . '/' . $imageName;
        $imageModel->image_owner_u_id = $user_id; // Save user ID with image if needed
        $imageModel->type = $type; // Save the type of entity
        $imageModel->save();

        return response()->json(['success' => 'Image uploaded successfully']);
    }
}
