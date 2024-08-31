<?php
namespace App\Services;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\MessageBag;

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
     * Resolves a foreign key based on specified conditions and ownership.
     *
     * Queries the specified table to find a record that matches the provided conditions
     * and is owned by the authenticated user. Returns the primary key of the matched
     * record or `null` if no match is found. Generates a user-friendly error message.
     *
     * @param string $table The name of the table to query.
     * @param array $conditions The conditions to match against.
     * @param string $ownerColumn The column indicating ownership.
     * @param string $primaryKey The primary key column to return.
     * @param string $entityName A friendly name for the entity being queried (e.g., 'Part').
     * @return int|null The ID of the matched record, or null if no match.
     */
    public function resolveForeignKey(string $table, array $conditions, string $ownerColumn, string $primaryKey, string $entityName): ?int
    {
        $query = DB::table($table);

        // Add each non-null condition to the query
        foreach ($conditions as $column => $value) {
            if (!is_null($value)) {
                $query->where($column, $value);
            }
        }

        // Ensure the record belongs to the authenticated user
        $user_id = auth()->user()->id;
        $query->where($ownerColumn, $user_id);

        // Fetch the first matching record
        $record = $query->first();

        // If no record is found or multiple conditions are provided and don't match, return null
        if (!$record || (count(array_filter($conditions)) > 1 && !$this->conditionsMatch($conditions, $record))) {
            $this->addCustomError('foreign_key', "No matching record found for {$entityName} with " . $this->formatConditionsForError($conditions));
            return null;
        }

        return $record->$primaryKey;
    }

    /**
     * Formats the conditions array into a user-friendly string for error messages.
     *
     * @param array $conditions The conditions used in the query.
     * @return string A formatted string describing the conditions.
     */
    protected function formatConditionsForError(array $conditions): string
    {
        $formattedConditions = [];

        foreach ($conditions as $key => $value) {
            if ($value !== null) {
                $formattedConditions[] = "{$key}: {$value}";
            }
        }

        return implode(', ', $formattedConditions);
    }

    /**
     * Checks if all provided conditions match the found record.
     *
     * @param array $conditions The conditions used in the query.
     * @param object $record The database record found.
     * @return bool True if all conditions match, false otherwise.
     */
    protected function conditionsMatch(array $conditions, $record): bool
    {
        foreach ($conditions as $column => $value) {
            if (!is_null($value) && $value !== $record->$column) {
                return false;
            }
        }

        return true;
    }

    /**
     * Adds a custom error message to the errors collection.
     *
     * @param string $key The error key.
     * @param string $message The error message.
     */
    public function addCustomError(string $key, string $message): void
    {
        // Use a more user-friendly key instead of "foreign_key"
        $friendlyKey = $this->getFriendlyKeyName($key);

        // Add the user-friendly error message
        $this->errors->add($friendlyKey, $message);
    }

    /**
     * Converts a technical key into a more user-friendly name.
     *
     * @param string $key The technical key.
     * @return string The user-friendly key name.
     */
    protected function getFriendlyKeyName(string $key): string
    {
        $keyMap = [
            'foreign_key' => 'Reference Error',
            // Add more key mappings as needed
        ];

        return $keyMap[$key] ?? ucfirst(str_replace('_', ' ', $key));
    }

    public function getErrors(): array
    {
        return $this->errors->toArray();
    }

    // Currently omits the friendly key name actually...
    public function formatErrorsForDisplay(): string
    {
        $errorMessages = [];

        // \Log::info($this->errors);

        foreach ($this->errors->toArray() as $key => $messages) {
            foreach ($messages as $message) {
                // Remove JSON brackets and other technical syntax
                $errorMessages[] = $message;
            }
        }

        // Join the error messages into a single string with line breaks or commas
        return implode('. ', $errorMessages) . '.';
    }

    /**
     * Maps the headers from the CSV file to the expected headers.
     *
     * @param array $csvHeaders The headers from the CSV file.
     * @param array $expectedHeaders The expected headers.
     * @return array An array containing the header mapping and unmatched headers.
     */
    public function mapHeaders(array $csvHeaders, array $expectedHeaders, int $distanceThreshold): array
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

            if ($smallestDistance < $distanceThreshold) {
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
}
