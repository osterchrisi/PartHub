<?php

namespace App\Services\Validators;

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class StockValidatorService
{
    public function validate(array $data): array
    {
        $rules = [
            'stock_changes' => 'required|array',
            'stock_changes.*.quantity' => 'required|integer|min:1',             // Must be an integer and at least 1
            'stock_changes.*.to_location' => 'nullable|integer',                // Optional integer
            'stock_changes.*.from_location' => 'nullable|integer',              // Optional integer
            'stock_changes.*.comment' => 'nullable|string|max:255',             // Optional, max 255 characters
            'stock_changes.*.part_id' => 'required|integer',                    // Required integer
            'stock_changes.*.change' => 'required|string|in:1,0,-1',            // Required and must be one of the defined types
            'stock_changes.*.bom_id' => 'nullable|integer',                     // Optional, can be empty or integer
            'stock_changes.*.assemble_quantity' => 'nullable|integer|min:1',    // Optional, integer if provided, at least 1
            'stock_changes.*.status' => 'nullable|string|in:gtg,ntg,stg',       // Optional status with specific values
            'stock_changes.*.to_quantity' => 'nullable|integer',                // Optional integer, can be empty
            'stock_changes.*.from_quantity' => 'nullable|integer',              // Optional integer, can be empty
        ];

        $validator = Validator::make($data, $rules);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        return $validator->validated();
    }
}
