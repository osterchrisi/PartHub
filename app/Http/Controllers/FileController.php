<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;

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
        $path = "files/{$fileType}/{$type}/{$userId}/{$id}/{$filename}";

        if (!Storage::disk('local')->exists($path)) {
            abort(404);
        }

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

        if (!Storage::disk('local')->exists($path)) {
            abort(404);
        }

        return response()->file(storage_path("app/{$path}"));
    }
}
