<?php

namespace App\Imports;

use App\Models\Bom;
use App\Models\BomElements;
use App\Models\Part;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class BomImport implements ToModel, WithHeadingRow
{
    protected $bom_id;

    public function __construct($bom_id)
    {
        $this->bom_id = $bom_id;
    }
    public function headingRow(): int
    {
        return 1; // Set the heading row index (1 for Excel files with headers)
    }
    public function model(array $row)
    {
        // Column names get formatted like this by Excel plugin
        $part_name = $row['part_name'];
        $part_id = $row['part_id'];
        $quantity = $row['quantity'];
        $user_id = auth()->user()->id;

        // Check if both part number and part ID are provided
        if (!empty($part_name) && !empty($part_id)) {
            // Perform query to verify if the part number and part ID match
            $part = Part::where('part_name', $part_name)
                ->where('part_id', $part_id)
                ->where('part_owner_u_fk', $user_id)
                ->first();

            if (!$part) {
                // Part number and part ID do not match,
                throw new \Exception('Part number and part ID do not match for row: ' . print_r($row, true));
            }
        }
        else if (empty($part_name) && empty($part_id)) {
            // Both part number and part ID are empty
            throw new \Exception('Both part number and part ID are missing for row: ' . print_r($row, true));
        }
        else {
            // Either part number or part ID is provided, handle accordingly
            $part = $part_name
                ? Part::where('part_name', $part_name)
                    ->where('part_owner_u_fk', $user_id)
                    ->first()
                : Part::where('part_id', $part_id)
                    ->where('part_owner_u_fk', $user_id)
                    ->first();

            if (!$part) {
                // Part number or part ID does not exist
                throw new \Exception('Invalid part number or part ID for row: ' . print_r($row, true));
            }
        }

        $bom = new BomElements([
            'bom_id_fk' => $this->bom_id,
            'part_id_fk' => $part->part_id,
            'element_quantity' => $quantity
        ]);
        return $bom;
    }
}