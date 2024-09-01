<?php

namespace App\Traits;

use Illuminate\Http\Request;

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
        $directory = 'storage/images/' . $type . '/' . $userId . '/' . $id;

        // Ensure the directory exists
        if (!file_exists(public_path($directory))) {
            mkdir(public_path($directory), 0755, true);
        }

        // Move the file to the public storage directory
        $filePath = $directory . '/' . $fileName;
        $file->move(public_path($directory), $fileName);

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
        $fullPath = public_path($filePath);

        if (file_exists($fullPath)) {
            unlink($fullPath);
        }
    }
}
