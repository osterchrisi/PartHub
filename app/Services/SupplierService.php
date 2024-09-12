<?php 

namespace App\Services;

use App\Models\SupplierData;

class SupplierService
{
    /**
     * Create or update supplier data for a part.
     *
     * @param int $part_id
     * @param array $suppliers
     * @return void
     */
    public function createOrUpdateSuppliers($part_id, array $suppliers)
    {
        foreach ($suppliers as $supplierData) {
            // Check if supplier data already exists for the part
            $existingSupplierData = SupplierData::where('part_id_fk', $part_id)
                ->where('supplier_id_fk', $supplierData['supplier_id'])
                ->first();

            if ($existingSupplierData) {
                // Update existing supplier data
                $existingSupplierData->update([
                    'URL' => $supplierData['URL'] ?? null,
                    'SPN' => $supplierData['SPN'] ?? null,
                    'price' => $supplierData['price'] ?? null,
                ]);
            } else {
                // Create new supplier data
                SupplierData::create([
                    'part_id_fk' => $part_id,
                    'supplier_id_fk' => $supplierData['supplier_id'],
                    'URL' => $supplierData['URL'] ?? null,
                    'SPN' => $supplierData['SPN'] ?? null,
                    'price' => $supplierData['price'] ?? null,
                ]);
            }
        }
    }

    /**
     * Delete supplier data for a part and supplier.
     *
     * @param int $part_id
     * @param int $supplier_id
     * @return void
     */
    public function deleteSupplierData($part_id, $supplier_id)
    {
        SupplierData::where('part_id_fk', $part_id)
            ->where('supplier_id_fk', $supplier_id)
            ->delete();
    }
}
