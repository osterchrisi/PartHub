<?php

namespace App\Http\Controllers;

use App\Models\SupplierData;
use App\Services\SupplierService;
use Illuminate\Http\Request;

class SupplierDataController extends Controller
{

    protected $supplierService;

    public function __construct(SupplierService $supplierService)
    {
        $this->supplierService = $supplierService;
    }
    /**
     * Create or update supplier data for a given part.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function createOrUpdateSuppliers(Request $request)
    {
        // Validate the incoming request
        $validated = $request->validate([
            'part_id' => 'required|integer',
            'suppliers' => 'required|array',
            'suppliers.*.supplier_id' => 'required|integer',
            'suppliers.*.URL' => 'nullable|url',
            'suppliers.*.SPN' => 'nullable|string|max:255',
            'suppliers.*.price' => 'nullable|numeric',
        ]);

        // Delegate supplier creation/update to the service
        $this->supplierService->createOrUpdateSuppliers($validated['part_id'], $validated['suppliers']);

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

        // Delegate supplier deletion to the service
        $this->supplierService->deleteSupplierData($validated['part_id'], $validated['supplier_id']);

        return response()->json(['success' => true, 'message' => 'Supplier data deleted successfully']);
    }
}
