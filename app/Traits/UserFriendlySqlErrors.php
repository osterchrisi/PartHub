<?php

namespace App\Traits;

use Illuminate\Support\MessageBag;

trait UserFriendlySqlErrors
{
    protected $errors;

    protected $includeKey = false;

    public function __construct()
    {
        $this->errors = new MessageBag();
    }

    public function hasErrors(): bool
    {
        return !$this->errors->isEmpty();
    }


    /**
     * Prepares the error messages for formatting.
     *
     * @return $this
     */
    public function formatErrors(): self
    {
        // Perform any additional pre-formatting here if needed
        return $this;
    }

    /**
     * Adds a custom error message to the errors collection.
     *
     * @param  string  $key  The error key.
     * @param  string  $message  The error message.
     */
    public function addCustomError(string $key, string $message): void
    {
        $this->errors->add($key, $message);
    }

    /**
     * Converts a technical key into a more user-friendly name for displaying an error message to a user.
     *
     * @param  string  $key  The technical key.
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

    /**
     * Returns the 'raw' errors as an array.
     *
     * @return array The array of errors.
     */
    public function getErrors(): array
    {
        return $this->errors->toArray();
    }

    /**
     * Enables the inclusion of keys in the error messages.
     *
     * @return $this
     */
    public function withKey(): string
    {
        $this->includeKey = true;

        return $this->formatErrorsForDisplay();
    }

    /**
     * Disables the inclusion of keys in the error messages.
     *
     * @return $this
     */
    public function withoutKey(): string
    {
        $this->includeKey = false;

        return $this->formatErrorsForDisplay();
    }

    /**
     * Formats errors for display by converting the array to a string.
     *
     * @return string The formatted error message string.
     */
    protected function formatErrorsForDisplay(): string
    {
        $errorMessages = [];

        foreach ($this->errors->toArray() as $key => $messages) {
            $friendlyKey = $this->getFriendlyKeyName($key);

            foreach ($messages as $message) {
                if ($this->includeKey) {
                    $errorMessages[] = "{$friendlyKey}: {$message}";
                }
                else {
                    $errorMessages[] = $message;
                }
            }
        }

        return implode("<br>", $errorMessages);
    }

    /**
     * Formats the conditions array into a user-friendly string for error messages.
     *
     * @param  array  $conditions  The conditions used in the query.
     * @return string A formatted string describing the conditions.
     */
    public function formatConditionsForError(array $conditions): string
    {
        $formattedConditions = [];

        foreach ($conditions as $key => $value) {
            if ($value !== null) {
                if ($key == "part_id") {
                    $key = "ID";
                }
                else if ($key == "part_name") {
                    $key = "Part Name";
                }
                $formattedConditions[] = "{$key}: {$value}";
            }
        }

        return implode(', ', $formattedConditions);
    }

    /**
     * Flashes the accumulated errors to the session for later display.
     */
    public function flashErrors(): void
    {
        session()->flash('errors', $this->errors);
    }
}
