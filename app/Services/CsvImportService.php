<?php

namespace App\Services;

use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\MessageBag;
use Illuminate\Support\Collection;
use App\Services\Imports\CsvCollectionImporter;


class CsvImportService
{
    protected $errors;

    public function __construct()
    {
        $this->errors = new MessageBag();
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
     * Processes a single row of data based on the import type.
     *
     * @param array $row The row of data to process.
     * @param string $importType The type of import (e.g., 'bom', 'part').
     * @return bool True on success, false on failure.
     */
    public function processRow(array $row, string $importType): bool
    {
        // Example: Handle specific import type (BOMs, Parts, etc.)
        switch ($importType) {
            case 'bom':
                return $this->processBomRow($row);
            case 'part':
                return $this->processPartRow($row);
            default:
                $this->errors->add('import', "Unknown import type: {$importType}");
                return false;
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
        // Logic for processing a BOM row
        // Use $this->resolveForeignKey to match and validate foreign keys
        // Add to $this->errors if anything fails

        return true; // Return true if successful, otherwise false
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
        // Similar to BOM processing

        return true;
    }

    /**
     * Handles the full import process of a CSV file.
     *
     * @param mixed $file The file to be imported.
     * @param string $importType The type of import (e.g., 'bom', 'part').
     * @return bool True on success, false on failure.
     */
    public function importFile($file, string $importType): bool
    {
        DB::beginTransaction();

        try {
            $collection = Excel::toCollection(new CsvCollectionImporter, $file)->first();

            $headers = $collection->first()->keys()->toArray();

            if (!$this->validateHeaders($headers, $this->getExpectedHeaders($importType))) {
                throw new \Exception('Invalid headers');
            }

            foreach ($collection as $row) {
                if (!$this->processRow($row, $importType)) {
                    throw new \Exception('Row processing failed');
                }
            }

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollback();
            $this->errors->add('transaction', $e->getMessage());
            return false;
        }
    }

    /**
     * Retrieves the expected headers based on the import type.
     *
     * @param string $importType The type of import (e.g., 'bom', 'part').
     * @return array The array of expected headers.
     */
    protected function getExpectedHeaders(string $importType): array
    {
        // Define expected headers for each import type
        switch ($importType) {
            case 'bom':
                return ['part_id', 'part_name', 'quantity', 'denominators'];
            case 'part':
                return ['name', 'description', 'category', 'supplier', 'unit'];
            default:
                return [];
        }
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

            if ($smallestDistance < 3) { // Arbitrary threshold for similarity
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
        // Create a new Collection where keys are the expected headers
        return collect($headerMapping)->mapWithKeys(function ($csvHeader, $expected) use ($row) {
            return [$expected => $row->get($csvHeader)];
        });
    }



}

// this for flashing the errors:
// @if (session('errors'))
//     <div class="alert alert-danger">
//         <ul>
//             @foreach (session('errors')->all() as $error)
//                 <li>{{ $error }}</li>
//             @endforeach
//         </ul>
//     </div>
// @endif

// this for flashing success:
// session()->flash('success', 'Your BOM was imported successfully!');
// @if (session('success'))
//     <div class="alert alert-success">
//         {{ session('success') }}
//     </div>
// @endif