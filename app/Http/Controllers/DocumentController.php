<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Traits\UploadsFiles;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DocumentController extends Controller
{
    use UploadsFiles;

    /**
     * Uploads a PDF document and stores it on the server.
     *
     * @param  \Illuminate\Http\Request  $request  The HTTP request object.
     * @param  string  $type  The type of entity the document belongs to (e.g., 'part', 'location', etc.).
     * @param  int  $id  The ID of the entity the document belongs to.
     * @return \Illuminate\Http\JsonResponse A JSON response indicating the success of the upload.
     *
     * @throws \Illuminate\Validation\ValidationException If validation fails for the document upload.
     */
    public function upload(Request $request, $type, $id)
    {
        $validationRules = ['required', 'mimes:pdf', 'max:2048'];
        $filePath = $this->uploadFile($request, 'document', 'document',  $type, $id, $validationRules);

        // Save document details to database
        $documentModel = new Document();
        $documentModel->filename = $filePath;
        $documentModel->document_owner_u_id = auth()->id(); // User that owns this document / uploaded it
        $documentModel->type = $type;                       // Type = part, location, ...
        $documentModel->associated_id = $id;                // ID of the entity
        $documentModel->save();

        return response()->json(['success' => 'Document uploaded successfully']);
    }

    public function getDocumentsByTypeAndId($type, $id)
    {
        // Retrieve documents associated with the entity ID
        $documents = Document::where('associated_id', $id)
            ->where('type', $type)
            ->where('document_owner_u_id', auth()->id())
            ->get();

        return response()->json($documents);
    }

    public function delete($type, $id)
    {
        // Find the document by ID and type
        $document = Document::where('id', $id)
            ->where('type', $type)
            ->where('document_owner_u_id', auth()->id())
            ->first();

        if (! $document) {
            return response()->json(['error' => 'Document not found or not authorized'], 404);
        }

        DB::beginTransaction();
        $this->deleteFile($document->filename);

        // Delete the document record from the database
        $document->delete();
        DB::commit();

        return response()->json(['success' => 'Document deleted successfully']);
    }
}
