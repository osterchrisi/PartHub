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
     * @param string $inputName The name of the file input field.
     * @param string $fileType The type of the file being uploaded (e.g., 'image', 'document').
     * @param string $type The type of resource the file is associated with (e.g., 'part', 'location').
     * @param int $id The ID of the resource the file is associated with.
     * @param array $validationRules The validation rules to apply to the file upload.
     * @return string $filePath The path where the file was stored.
     */
    protected function uploadFile(Request $request, $inputName, $fileType, $type, $id, array $validationRules)
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

        // Determine the base directory based on the file type
        switch ($fileType) {
            case 'document':
                $baseDirectory = 'files/documents';
                break;
            case 'image':
            default:
                $baseDirectory = 'files/images';
                break;
        }

        // Define the full directory path based on the file type, resource type, and resource ID
        $directory = $baseDirectory . '/' . $type . '/' . $userId . '/' . $id;

        // Store the file in the storage/app/files/ directory
        $filePath = $file->storeAs($directory, $fileName, 'local');

        return $filePath; // Return the relative path as it is stored in the database
    }

    /**
     * Handle file deletion.
     *
     * @param string $filePath
     * @return void
     */
    protected function deleteFile($filePath)
    {
        if (Storage::disk('local')->exists($filePath)) {
            Storage::disk('local')->delete($filePath);
        }
    }
}
