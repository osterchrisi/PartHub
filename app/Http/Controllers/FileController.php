<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;


class FileController extends Controller
{
    /**
     * Serve the original image file from storage.
     *
     * @param string $type
     * @param int $userId
     * @param int $id
     * @param string $filename
     * @return \Illuminate\Http\Response
     */
    public function serveFile($type, $userId, $id, $filename)
    {
        $path = "files/images/{$type}/{$userId}/{$id}/{$filename}";

        if (!Storage::disk('local')->exists($path)) {
            abort(404);
        }

        return response()->file(storage_path("app/{$path}"));
    }

    /**
     * Serve the thumbnail image file from storage.
     *
     * @param string $type
     * @param int $userId
     * @param int $id
     * @param string $filename
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
