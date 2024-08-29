<?php

namespace App\Imports;

use App\Models\BomElements;
use App\Services\CsvImportService;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Facades\DB;

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
        // Get headers from the first row of the collection
        $headers = $collection->first()->keys()->toArray();

        \Log::error($headers);

        // Validate headers
        if (!$this->csvImportService->validateHeaders($headers, $this->getExpectedHeaders())) {
            throw new \Exception('Invalid headers');
        }

        // Optional: Map headers if necessary
        $mappingResult = $this->csvImportService->mapHeaders($headers, $this->getExpectedHeaders());

        // Process each row
        foreach ($collection as $row) {
            $rowData = $this->csvImportService->mapRowData($row, $mappingResult['mapping']);
            if (!$this->processRowAndCreateBomElement($rowData)) {
                throw new \Exception('Row processing and BOM element creation failed');
            }
        }
    }

    /**
     * Defines the expected headers for a BOM import.
     *
     * @return array An array of expected header strings.
     */
    protected function getExpectedHeaders(): array
    {
        // Define the expected headers for BOM import
        return ['part_id', 'part_name', 'quantity'];// 'denominators'];
    }

    /**
     * Processes a single row of data and creates a BOM element.
     *
     * @param \Illuminate\Support\Collection $rowData The data for a single row, mapped by headers.
     * @return bool True on success, false on failure.
     */
    protected function processRowAndCreateBomElement(Collection $rowData): bool
    {
        // Prepare conditions for resolving the foreign key
        $conditions = [
            'part_id' => $rowData->get('part_id'),
            'part_name' => $rowData->get('part_name'),
        ];

        // Explicitly specify the owner column and primary key
        $ownerColumn = 'part_owner_u_fk';
        $primaryKey = 'part_id';

        // Attempt to resolve the part using either part_id or part_name
        $part_id = $this->csvImportService->resolveForeignKey('parts', $conditions, $ownerColumn, $primaryKey);

        if (!$part_id) {
            // Foreign key resolution failed, log the error and return false
            $this->csvImportService->flashErrors();
            return false;
        }

        // Create the BOM element using the resolved part_id
        BomElements::create([
            'bom_id_fk' => $this->bom_id,
            'part_id_fk' => $part_id,
            'element_quantity' => $rowData->get('quantity'),
        ]);

        return true;
    }


}
