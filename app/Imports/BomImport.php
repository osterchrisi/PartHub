<?php

namespace App\Imports;

use App\Services\CsvImportService;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class BomImport implements ToCollection, WithHeadingRow
{
    protected $bom_id;
    protected $csvImportService;

    public function __construct($bom_id, CsvImportService $csvImportService)
    {
        $this->bom_id = $bom_id;
        $this->csvImportService = $csvImportService;
    }

    /**
     * Handles the collection of data from the imported CSV file.
     *
     * @param \Illuminate\Support\Collection $collection The collection of rows from the CSV file.
     * @throws \Exception If headers are invalid or row processing fails.
     */
    public function collection(Collection $collection)
    {
        $this->csvImportService->importBom($collection, $this->bom_id);
    }
}
