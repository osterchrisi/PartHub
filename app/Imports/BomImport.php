<?php

namespace App\Imports;

use App\Models\BomElements;
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
        $this->importBom($collection);
    }

    /**
     * Handles the import process of a CSV file for BOM.
     *
     * @param Collection $collection The collection of rows from the CSV file.
     * @throws \Exception If headers are invalid or row processing fails.
     */
    public function importBom(Collection $collection)
    {
        $headers = $collection->first()->keys()->toArray();

        if (!$this->csvImportService->validateHeaders($headers, $this->csvImportService->getExpectedHeaders('bom'))) {
            throw new \Exception('Invalid headers');
        }

        $mappingResult = $this->csvImportService->mapHeaders($headers, $this->csvImportService->getExpectedHeaders('bom'));

        foreach ($collection as $row) {
            $rowData = $this->csvImportService->mapRowData($row, $mappingResult['mapping']);
            if (!$this->processBomRow($rowData->toArray())) {
                throw new \Exception('Row processing and BOM element creation failed');
            }
        }
    }

    /**
     * Processes a row specific to a BOM import.
     *
     * @param array $row The BOM row data to process.
     * @return bool True on success, false on failure.
     */
    protected function processBomRow(array $row): bool
    {
        // Process the row and create BOM elements
        $conditions = [
            'part_id' => $row['part_id'] ?? null,
            'part_name' => $row['part_name'] ?? null,
        ];

        $part_id = $this->csvImportService->resolveForeignKey('parts', $conditions, 'part_owner_u_fk', 'part_id');

        if (!$part_id) {
            $this->csvImportService->flashErrors();
            return false;
        }

        BomElements::create([
            'bom_id_fk' => $this->bom_id,
            'part_id_fk' => $part_id,
            'element_quantity' => $row['quantity'],
        ]);

        return true;
    }
}