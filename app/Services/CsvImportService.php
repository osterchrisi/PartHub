<?php

namespace App\Services;

use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\MessageBag;
use Illuminate\Support\Collection;
use App\Services\Imports\CsvCollectionImporter;
use App\Models\BomElements;


class CsvImportService
{
    protected $errors;

    public function __construct()
    {
        $this->errors = new MessageBag();
    }

    /**
     * Retrieves the expected headers based on the import type.
     *
     * @param string $importType The type of import (e.g., 'bom', 'part').
     * @return array The array of expected headers.
     */
    public function getExpectedHeaders(string $importType): array
    {
        switch ($importType) {
            case 'bom':
                return ['part_id', 'part_name', 'quantity'];
            case 'part':
                return ['name', 'description', 'category', 'supplier', 'unit'];
            default:
                return [];
        }
    }

    /**
     * Validates that the provided headers match the expected headers.
     *
     * @param array $headers The headers from the CSV file.
     * @param array $expectedHeaders The expected headers.
     * @return bool True if headers are valid, false otherwise.
     */
    public function validateHeaders(array $headers, array $expectedHeaders): bool
    {
        foreach ($expectedHeaders as $expected) {
            if (!in_array($expected, $headers)) {
                $this->errors->add('header', "Missing expected header: {$expected}");
            }
        }

        return $this->errors->isEmpty();
    }

    /**
     * Processes a row specific to a BOM import.
     *
     * @param array $row The BOM row data to process.
     * @param int $bom_id The BOM ID to associate with.
     * @return bool True on success, false on failure.
     */
    protected function processBomRow(array $row, int $bom_id): bool
    {
        // Process the row and create BOM elements
        $conditions = [
            'part_id' => $row['part_id'] ?? null,
            'part_name' => $row['part_name'] ?? null,
        ];

        $part_id = $this->resolveForeignKey('parts', $conditions, 'part_owner_u_fk', 'part_id');

        if (!$part_id) {
            $this->flashErrors();
            return false;
        }

        BomElements::create([
            'bom_id_fk' => $bom_id,
            'part_id_fk' => $part_id,
            'element_quantity' => $row['quantity'],
        ]);

        return true;
    }


    /**
     * Processes a row specific to a Part import.
     *
     * @param array $row The Part row data to process.
     * @return bool True on success, false on failure.
     */
    protected function processPartRow(array $row): bool
    {
        // Logic for processing a Part row
        return true;
    }

    /**
     * Resolves a foreign key based on conditions and ownership.
     *
     * @param string $table The name of the table to query.
     * @param array $conditions The conditions to match against.
     * @param string $ownerColumn The column indicating ownership.
     * @param string $primaryKey The primary key column to return.
     * @return int|null The ID of the matched record, or null if no match.
     */
    public function resolveForeignKey(string $table, array $conditions, string $ownerColumn, string $primaryKey): ?int
    {
        $query = DB::table($table);

        foreach ($conditions as $column => $value) {
            if (!is_null($value)) {
                $query->where($column, $value);
            }
        }

        $user_id = auth()->user()->id;
        $query->where($ownerColumn, $user_id);

        $record = $query->first();

        if (!$record) {
            $this->errors->add('foreign_key', "No matching record found in {$table} with given conditions: " . json_encode($conditions));
            return null;
        }

        return $record->$primaryKey;
    }

    /**
     * Flashes the accumulated errors to the session for later display.
     */
    public function flashErrors(): void
    {
        session()->flash('errors', $this->errors);
    }

    /**
     * Maps the headers from the CSV file to the expected headers.
     *
     * @param array $csvHeaders The headers from the CSV file.
     * @param array $expectedHeaders The expected headers.
     * @return array An array containing the header mapping and unmatched headers.
     */
    public function mapHeaders(array $csvHeaders, array $expectedHeaders): array
    {
        $headerMapping = [];
        $unmatchedHeaders = [];

        foreach ($expectedHeaders as $expected) {
            $bestMatch = null;
            $smallestDistance = PHP_INT_MAX;

            foreach ($csvHeaders as $header) {
                $distance = levenshtein(strtolower($expected), strtolower($header));

                if ($distance < $smallestDistance) {
                    $smallestDistance = $distance;
                    $bestMatch = $header;
                }
            }

            if ($smallestDistance < 3) {
                $headerMapping[$expected] = $bestMatch;
            }
            else {
                $unmatchedHeaders[] = $expected;
            }
        }

        return ['mapping' => $headerMapping, 'unmatched' => $unmatchedHeaders];
    }

    /**
     * Maps a single row of CSV data to the expected headers.
     *
     * @param \Illuminate\Support\Collection $row The row of data to map.
     * @param array $headerMapping The mapping of expected headers to CSV headers.
     * @return \Illuminate\Support\Collection The mapped row data.
     */
    public function mapRowData(Collection $row, array $headerMapping): Collection
    {
        return collect($headerMapping)->mapWithKeys(function ($csvHeader, $expected) use ($row) {
            return [$expected => $row->get($csvHeader)];
        });
    }

    /**
     * Handles the import process of a CSV file for BOM.
     *
     * @param Collection $collection The collection of rows from the CSV file.
     * @param int $bom_id The BOM ID to associate with.
     * @throws \Exception If headers are invalid or row processing fails.
     */
    public function importBom(Collection $collection, int $bom_id)
    {
        $headers = $collection->first()->keys()->toArray();

        if (!$this->validateHeaders($headers, $this->getExpectedHeaders('bom'))) {
            throw new \Exception('Invalid headers');
        }

        $mappingResult = $this->mapHeaders($headers, $this->getExpectedHeaders('bom'));

        foreach ($collection as $row) {
            $rowData = $this->mapRowData($row, $mappingResult['mapping']);
            if (!$this->processBomRow($rowData->toArray(), $bom_id)) {
                throw new \Exception('Row processing and BOM element creation failed');
            }
        }
    }

}
