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
     * @param  \Illuminate\Support\Collection  $collection  The collection of rows from the CSV file.
     *
     * @throws \Exception If headers are invalid or row processing fails.
     */
    public function collection(Collection $collection)
    {
        $this->importBom($collection);
    }

    /**
     * Handles the import process of a CSV file for BOM.
     *
     * @param  Collection  $collection  The collection of rows from the CSV file.
     *
     * @throws \Exception If headers are invalid or row processing fails.
     */
    public function importBom(Collection $collection)
    {
        // Collect headers
        $headers = $collection->first()->keys()->toArray();

        // Map CSV headers to expected headers using Levenshtein distance
        $mappingResult = $this->csvImportService->mapHeaders($headers, $this->csvImportService->getExpectedHeaders('bom'), 3);

        // Validate if all expected headers have been successfully mapped
        if (! empty($mappingResult['unmatched'])) {
            throw new \Exception('Invalid headers: '.implode(', ', $mappingResult['unmatched']).' not found.');
        }

        // Process BOM row by row
        foreach ($collection as $row) {
            $rowData = $this->csvImportService->mapRowData($row, $mappingResult['mapping']);
            $this->processBomRow($rowData->toArray());
        }

        // After processing all rows, check for errors
        if ($this->csvImportService->hasErrors()) {
            // Throw an exception with the accumulated errors
            $formattedErrors = $this->csvImportService->formatErrors()->withoutKey();
            throw new \Exception('BOM import failed with the following errors:<br>'.$formattedErrors);
        }
    }

    /**
     * Processes a row specific to a BOM import.
     *
     * @param  array  $row  The BOM row data to process.
     * @return bool True on success, false on failure.
     */
    protected function processBomRow(array $row): void
    {
        // Process the row and create BOM elements
        $conditions = [
            'part_id' => $row['part_id'] ?? null,
            'part_name' => $row['part_name'] ?? null,
        ];

        $part_id = $this->csvImportService->resolveForeignKey('parts', $conditions, 'part_owner_u_fk', 'part_id');

        if (! $part_id) {
            // Add the error to the service (but don't throw an exception yet)
            $this->csvImportService->addCustomError('foreign_key', 'No matching record found for part with '.$this->csvImportService->formatConditionsForError($conditions));

            return;
        }

        BomElements::create([
            'bom_id_fk' => $this->bom_id,
            'part_id_fk' => $part_id,
            'element_quantity' => $row['quantity'],
        ]);
    }
}
