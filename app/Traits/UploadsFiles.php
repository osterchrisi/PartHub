<?php

namespace App\Traits;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

trait UploadsFiles
{
    /**
     * Handle file upload and storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param string $inputName
     * @param string $type
     * @param int $id
     * @param array $validationRules
     * @return string $filePath
     */
    protected function uploadFile(Request $request, $inputName, $type, $id, array $validationRules)
    {
        // Validate the request
        $request->validate([
            $inputName => $validationRules,
        ]);

        // Retrieve the file from the request
        $file = $request->file($inputName);

        // Generate a unique filename using the original name and current timestamp
        $originalFilename = $file->getClientOriginalName();
        $filenameWithoutExtension = pathinfo($originalFilename, PATHINFO_FILENAME);
        $sanitizedFilename = preg_replace('/[^a-zA-Z0-9_.]/', '', $filenameWithoutExtension);
        $fileName = $sanitizedFilename . '_' . time() . '.' . $file->extension();
        $userId = auth()->id();

        // Define the directory path based on the type of entity
        $directory = 'files/' . $type . '/' . $userId . '/' . $id;

        // Store the file in the storage/app/ directory
        $filePath = $file->storeAs($directory, $fileName);

        // Return the full storage path
        return Storage::path($filePath);
    }

    /**
     * Handle file deletion.
     *
     * @param string $filePath
     * @return void
     */
    protected function deleteFile($filePath)
    {
        if (Storage::exists($filePath)) {
            Storage::delete($filePath);
        }
    }
}
