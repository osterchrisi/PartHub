<?php

namespace App\Services\Validators;

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class PartValidatorService
{
    public function validate(array $data, string $httpMethod): array
    {
        // Check if creating completely new or updating
        $isUpdating = in_array($httpMethod, ['PUT', 'PATCH']);

        //TODO: After updating DBSController remove the column names-y things here
        $rules = [
            'part_name' => $isUpdating ? 'nullable|string|max:255' : 'required|string|max:255',
            'part_id' => $isUpdating ? 'required|integer' : 'sometimes|integer',
            'quantity' => 'nullable|integer',
            'to_location' => 'nullable|integer',
            'comment' => 'nullable|string',
            'description' => 'nullable|string',
            'footprint' => 'nullable|string',
            'category' => 'nullable|integer',
            'part_category_fk' => 'nullable|integer',
            'min_quantity' => 'nullable|integer',
            'suppliers' => 'nullable|array',
            'suppliers.*.supplier_id' => $isUpdating ? 'nullable|integer' : 'nullable|integer|required_with:suppliers.*.URL,suppliers.*.SPN,suppliers.*.price',
            // Supplier URL
            'suppliers.*.URL' => 'nullable|url',
            'URL' => 'nullable|url',
            'suppliers.*.SPN' => 'nullable|string|max:255',
            // Supplier Price
            'suppliers.*.price' => 'nullable|numeric',
            'price' => 'nullable|numeric',
        ];

        $validator = Validator::make($data, $rules);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        return $validator->validated();
    }
}
