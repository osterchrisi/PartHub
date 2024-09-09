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
            'suppliers' => 'required|array',
            'suppliers.*.supplier_id' => 'required|integer',
            'suppliers.*.URL' => 'nullable|url',
            'suppliers.*.SPN' => 'nullable|string|max:255',
            'suppliers.*.price' => 'nullable|numeric',
        ]);

        foreach ($validated['suppliers'] as $supplierData) {
            SupplierData::create([
                'part_id_fk' => $validated['part_id'],
                'supplier_id_fk' => $supplierData['supplier_id'],
                'URL' => $supplierData['URL'] ?? null,
                'SPN' => $supplierData['SPN'] ?? null,
                'price' => $supplierData['price'] ?? null,
            ]);
        }

        return response()->json(['success' => true]);
    }

}
