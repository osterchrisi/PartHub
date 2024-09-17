<?php

namespace App\Services;

use App\Models\SupplierData;
use Illuminate\Support\Facades\Auth;


class SupplierService
{
    /**
     * Create supplier data for a part.
     *
     * @param int $part_id
     * @param array $suppliers
     * @return void
     */
    public function createSuppliers($part_id, array $suppliers)
    {
        foreach ($suppliers as $supplierData) {
            SupplierData::create([
                'part_id_fk' => $part_id,
                'supplier_id_fk' => $supplierData['supplier_id'],
                'SPN' => $supplierData['SPN'] ?? null,
                'URL' => $supplierData['URL'] ?? null,
                'price' => $supplierData['price'] ?? null,
                'supplier_data_owner_u_fk' => Auth::id(),
            ]);
        }
    }

    /**
     * Update specific supplier data row by ID.
     *
     * @param int $id
     * @param array $data
     * @return void
     */
    public function updateSupplierDataById($id, array $data)
    {
        // Update a specific supplier data row by its unique ID
        SupplierData::where('id', $id)->update($data);
    }

    /**
     * Delete specific supplier data row by ID.
     *
     * @param int $id
     * @return void
     */
    public function deleteSupplierDataById($id)
    {
        // Delete a specific supplier data row by its unique ID
        SupplierData::where('id', $id)->delete();
    }

    /**
     * Get all supplier data for a specific part.
     *
     * @param int $part_id
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getSupplierDataForPart($part_id)
    {
        return SupplierData::where('part_id_fk', $part_id)->with('supplier')->get();
    }
}

