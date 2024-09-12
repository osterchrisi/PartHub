<?php

namespace App\Http\Controllers;

use App\Models\SupplierData;
use Illuminate\Http\Request;

class SupplierDataController extends Controller
{
    /**
     * Create or update supplier data for a given part.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function createOrUpdateSupplierData(Request $request)
    {
        // Validate the incoming request
        $validated = $request->validate([
            'part_id' => 'required|integer',
            'suppliers' => 'required|array',
            'suppliers.*.supplier_id' => 'required|integer',   // Supplier ID is required for each row
            'suppliers.*.URL' => 'nullable|url',
            'suppliers.*.SPN' => 'nullable|string|max:255',
            'suppliers.*.price' => 'nullable|numeric',
        ]);

        // Loop through each supplier and either create or update the supplier data
        foreach ($validated['suppliers'] as $supplierData) {
            // Check if this supplier data already exists for the given part
            $existingSupplierData = SupplierData::where('part_id_fk', $validated['part_id'])
                ->where('supplier_id_fk', $supplierData['supplier_id'])
                ->first();

            if ($existingSupplierData) {
                // Update the existing supplier data
                $existingSupplierData->update([
                    'URL' => $supplierData['URL'] ?? null,
                    'SPN' => $supplierData['SPN'] ?? null,
                    'price' => $supplierData['price'] ?? null,
                ]);
            }
            else {
                // Create new supplier data if it doesn't exist
                SupplierData::create([
                    'part_id_fk' => $validated['part_id'],
                    'supplier_id_fk' => $supplierData['supplier_id'],
                    'URL' => $supplierData['URL'] ?? null,
                    'SPN' => $supplierData['SPN'] ?? null,
                    'price' => $supplierData['price'] ?? null,
                ]);
            }
        }

        return response()->json(['success' => true, 'message' => 'Suppliers added/updated successfully']);
    }

    /**
     * Get all suppliers for a given part.
     *
     * @param int $part_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function getSupplierDataForPart($part_id)
    {
        // Fetch all suppliers related to the part
        $suppliers = SupplierData::where('part_id_fk', $part_id)->get();

        return response()->json($suppliers);
    }

    /**
     * Delete supplier data for a specific supplier and part.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteSupplierData(Request $request)
    {
        $validated = $request->validate([
            'part_id' => 'required|integer',
            'supplier_id' => 'required|integer',
        ]);

        SupplierData::where('part_id_fk', $validated['part_id'])
            ->where('supplier_id_fk', $validated['supplier_id'])
            ->delete();

        return response()->json(['success' => true, 'message' => 'Supplier data deleted successfully']);
    }
}
