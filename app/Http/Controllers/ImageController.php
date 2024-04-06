<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Image;

class ImageController extends Controller
{
    public function upload(Request $request, $type)
    {
        // Validate the request
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);
    
        // Store the image
        $image = $request->file('image');
        $imageName = time().'.'.$image->extension(); // Generate unique filename
        $user_id = auth()->id();
    
        // Define the directory path based on the type of entity
        $directory = 'images/'.$type.'/'.$user_id;
    
        // Create the directory if it doesn't exist
        if (!file_exists(public_path($directory))) {
            mkdir(public_path($directory), 0755, true);
        }
    
        // Move the image to the directory
        $image->move(public_path($directory), $imageName);
    
        // Save image details to database
        $imageModel = new Image();
        $imageModel->filename = $directory.'/'.$imageName;
        $imageModel->image_owner_u_id = $user_id; // Save user ID with image if needed
        $imageModel->type = $type; // Save the type of entity
        $imageModel->save();
    
        return response()->json(['success'=>'Image uploaded successfully']);
    }
}
