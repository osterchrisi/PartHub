<?php

namespace App\Http\Controllers;

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
     * Create supplier data for a given part.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function create(Request $request)
    {
        // Validate the incoming request
        $validated = $request->validate([
            'part_id' => 'required|integer',
            'suppliers' => 'required|array',
            'suppliers.*.supplier_id' => 'required|integer',
            'suppliers.*.URL' => 'nullable|string', // Could also be URL for the validator but seems user-unfriendly
            'suppliers.*.SPN' => 'nullable|string|max:255',
            'suppliers.*.price' => 'nullable|numeric',
        ]);

        $this->supplierService->createSuppliers($validated['part_id'], $validated['suppliers']);

        return response()->json(['success' => true, 'message' => 'Suppliers added/updated successfully']);
    }
}
