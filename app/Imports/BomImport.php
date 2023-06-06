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

    public function __construct($bom_id){
        $this->bom_id = $bom_id;
    }
    public function headingRow(): int
    {
        return 1; // Set the heading row index (1 for Excel files with headers)
    }
    public function model(array $row)
    {
        $part_name = $row['Part Number'];
        $part_id = $row['Part ID'];
        $quantity = $row['quantity'];

        // Check if both part number and part ID are provided
        if (!empty($part_name) && !empty($part_id)) {
            // Perform a database query to verify if the part number and part ID match
            $part = Part::where('part_number', $part_name)
                        ->where('part_id', $part_id)
                        ->first();

            if (!$part) {
                // Part number and part ID do not match, handle the error
                throw new \Exception('Part number and part ID do not match for row: ' . print_r($row, true));
            }
        } else if (empty($part_name) && empty($part_id)) {
            // Both part number and part ID are empty, handle the error
            throw new \Exception('Both part number and part ID are missing for row: ' . print_r($row, true));
        } else {
            // Either part number or part ID is provided, handle accordingly
            $part = $part_name ? Part::where('part_number', $part_name)->first() : Part::find($part_id);

            if (!$part) {
                // Part number or part ID does not exist, handle the error
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