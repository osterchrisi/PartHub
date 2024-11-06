<?php

namespace App\Services\Validators;

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class PartValidatorService
{
    public function validate(array $data): array
    {
        $rules = [
            'part_name' => 'required|string|max:255',
            'quantity' => 'nullable|integer',
            'to_location' => 'nullable|integer',
            'comment' => 'nullable|string',
            'description' => 'nullable|string',
            'footprint' => 'nullable|string',
            'category' => 'nullable|integer',
            'min_quantity' => 'nullable|integer',
            'suppliers' => 'nullable|array',
            'suppliers.*.supplier_id' => 'nullable|integer|required_with:suppliers.*.URL,suppliers.*.SPN,suppliers.*.price',
            'suppliers.*.URL' => 'nullable|url',
            'suppliers.*.SPN' => 'nullable|string|max:255',
            'suppliers.*.price' => 'nullable|numeric',
        ];

        $validator = Validator::make($data, $rules);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        return $validator->validated();
    }
}
