<?php

namespace App\Imports;

use App\Models\Bom;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class BomImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        // Logic to transform and create BOM model
        // Example:
        return new Bom([
            'bom_name' => $row['bom_name'],
            'bom_description' => $row['bom_description'],
            'owner' => auth()->user()->id,
        ]);
    }

    public function headingRow(): int
    {
        return 1; // Set the heading row index (1 for Excel files with headers)
    }
}
