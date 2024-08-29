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

    public function validateHeaders(array $headers, array $expectedHeaders): bool
    {
        foreach ($expectedHeaders as $expected) {
            if (!in_array($expected, $headers)) {
                $this->errors->add('header', "Missing expected header: {$expected}");
            }
        }

        return $this->errors->isEmpty();
    }

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

    protected function processBomRow(array $row): bool
    {
        // Logic for processing a BOM row
        // Use $this->resolveForeignKey to match and validate foreign keys
        // Add to $this->errors if anything fails

        return true; // Return true if successful, otherwise false
    }

    protected function processPartRow(array $row): bool
    {
        // Logic for processing a Part row
        // Similar to BOM processing

        return true;
    }

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

    protected function resolveForeignKey(string $table, string $column, string $value): ?int
    {
        $user_id = auth()->user()->id;
        $owner_column = "{$table}_owner_u_fk";

        $record = DB::table($table)
            ->where($column, $value)
            ->where($owner_column, $user_id)
            ->first();

        if (!$record) {
            $this->errors->add('foreign_key', "No matching record found in {$table} for {$column}: {$value}");
            return null;
        }

        return $record->id;
    }

    public function flashErrors(): void
    {
        session()->flash('errors', $this->errors);
    }

    protected function mapHeaders(array $csvHeaders, array $expectedHeaders): array
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