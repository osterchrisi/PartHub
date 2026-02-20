<?php

namespace App\Imports;

use App\Models\BomRevisionElement;
use App\Services\CsvImportService;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class BomImport implements ToCollection, WithHeadingRow
{
    protected int $bomRevisionId;

    protected CsvImportService $csvImportService;

    public function __construct(int $bomRevisionId, CsvImportService $csvImportService)
    {
        $this->bomRevisionId = $bomRevisionId;
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
        // Drop empty/blank rows first (covers "header only", blank lines, etc.)
        $collection = $collection
            ->filter(fn ($row) => $row && $row->filter()->isNotEmpty())
            ->values();

        if ($collection->isEmpty()) {
            throw new \Exception('BOM import failed: the file contains no data rows.');
        }

        // Map headers using fuzzy matching so "Part ID" vs "part_id" still works.
        $headers = $collection->first()->keys()->toArray();

        $mappingResult = $this->csvImportService->mapHeaders(
            $headers,
            $this->csvImportService->getExpectedHeaders('bom'),
            3
        );

        // Validate if all expected headers have been successfully mapped
        if (! empty($mappingResult['unmatched'])) {
            throw new \Exception('Invalid headers: '.implode(', ', $mappingResult['unmatched']).' not found.');
        }

        // Process BOM row by row
        foreach ($collection as $row) {
            $rowData = $this->csvImportService->mapRowData($row, $mappingResult['mapping']);
            $this->processBomRow($rowData->toArray());
        }

        // If the service collected row-level errors, abort the import after parsing everything.
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
        // Identify the part either by part_id or part_name (owned by the user).
        $conditions = [
            'part_id' => $row['part_id'] ?? null,
            'part_name' => $row['part_name'] ?? null,
        ];

        $partId = $this->csvImportService->resolveForeignKey(
            'parts',
            $conditions,
            'part_owner_u_fk',
            'part_id'
        );

        if (! $partId) {
            $this->csvImportService->addCustomError(
                'foreign_key',
                'No matching record found for part with '.$this->csvImportService->formatConditionsForError($conditions)
            );

            return;
        }

        $qty = (int) ($row['quantity'] ?? 0);
        if ($qty < 1) {
            $this->csvImportService->addCustomError(
                'quantity',
                'Invalid quantity for part '.$partId.' (must be >= 1)'
            );

            return;
        }

        // Store the imported line in the revision snapshot, not in bom_elements.
        BomRevisionElement::create([
            'bom_revision_id_fk' => $this->bomRevisionId,
            'part_id_fk' => (int) $partId,
            'quantity' => $qty,
            'refdes' => null,
            'line_uuid' => null,
        ]);
    }
}
