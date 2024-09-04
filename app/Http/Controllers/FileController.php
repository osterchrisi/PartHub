<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

/**
 * FileController serves all files from storage
 */
class FileController extends Controller
{
    /**
     * Serve a file from storage.
     *
     * @param  string  $fileType  The type of file ('images', 'documents', etc.).
     * @param  string  $type  The type of resource the file is associated with ('part', 'location', etc.).
     * @param  int  $userId  The ID of the user who owns the file.
     * @param  int  $id  The ID of the resource the file is associated with.
     * @param  string  $filename  The name of the file.
     * @return \Illuminate\Http\Response
     */
    public function serveFile($fileType, $type, $userId, $id, $filename)
    {
        // Ensure the authenticated user matches the file owner
        if (Auth::id() !== (int)$userId) {
            abort(403, 'Unauthorized access to this file.');
        }
    
        // Construct the file path
        $path = "files/{$fileType}/{$type}/{$userId}/{$id}/{$filename}";
    
        // Check if the file exists in storage
        if (! Storage::disk('local')->exists($path)) {
            abort(404);
        }
    
        // Serve the file
        return response()->file(storage_path("app/{$path}"));
    }

    /**
     * Serve the thumbnail image file from storage.
     *
     * @param  string  $type
     * @param  int  $userId
     * @param  int  $id
     * @param  string  $filename
     * @return \Illuminate\Http\Response
     */
    public function serveThumbnail($type, $userId, $id, $filename)
    {
        $path = "files/images/{$type}/{$userId}/{$id}/thumbnails/{$filename}";

        if (! Storage::disk('local')->exists($path)) {
            abort(404);
        }

        return response()->file(storage_path("app/{$path}"));
    }
}
