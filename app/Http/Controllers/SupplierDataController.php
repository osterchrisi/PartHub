<?php

namespace App\Http\Controllers;

use App\Models\SupplierData;
use Illuminate\Http\Request;

class SupplierDataController extends Controller
{
    public function createEntry(Request $request)
    {
        $validated = $request->validate([
            'part_id' => 'required|integer',
            'supplier_id' => 'required|integer',
            'URL' => 'nullable|url',
            'SPN' => 'nullable|string|max:255',
            'price' => 'nullable|numeric',
        ]);

        $newSupplierData = SupplierData::create([
            'part_id_fk' => $validated['part_id'],
            'supplier_id_fk' => $validated['supplier_id'],
            'URL' => $validated['URL'] ?? null,
            'SPN' => $validated['SPN'] ?? null,
            'price' => $validated['price'] ?? null,
        ]);

        return $newSupplierData->id; // Return the ID of the new entry
    }
}
