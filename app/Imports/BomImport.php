<?php

namespace App\Imports;

use App\Models\Bom;
use App\Models\BomElements;
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
        $bom = new BomElements([
            'bom_id_fk' => $this->bom_id,
            'part_id_fk' => $row['part'],
            'element_quantity' => $row['quantity']
        ]);
        return $bom;
    }
}