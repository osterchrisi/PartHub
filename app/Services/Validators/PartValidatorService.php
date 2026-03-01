<?php

namespace App\Services\Validators;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class PartValidatorService
{
    public function validate(array $data, string $httpMethod): array
    {
        // Check if creating completely new or updating
        $isUpdating = in_array($httpMethod, ['PUT', 'PATCH']);

        \Log::info('Validating part data', ['data' => $data, 'http_method' => $httpMethod]);

        // Special case to not break my supplier rules
        $supplierIdRules = [
            'nullable',
            'integer',
            Rule::exists('suppliers', 'supplier_id')
                ->where('supplier_owner_u_fk', Auth::id()),
        ];

        if (! $isUpdating) {
            $supplierIdRules[] = 'required_with:suppliers.*.URL,suppliers.*.SPN,suppliers.*.price';
        }

        // TODO: After updating DBSController remove the column names-y things here
        // TODO: Why are there two category checks and also URL and Price (top level) are maybe irrelevant?
        $rules = [
            'part_name' => $isUpdating ? 'nullable|string|max:255' : 'required|string|max:255',
            'part_id' => $isUpdating ? 'required|integer' : 'sometimes|integer',
            'quantity' => 'nullable|integer',
            'to_location' => [
                'nullable',
                'integer',
                Rule::exists('locations', 'location_id')->where('location_owner_u_fk', Auth::id()),
            ],
            'comment' => 'nullable|string',
            'description' => 'nullable|string',
            'footprint' => [
                'nullable',
                'integer',
                Rule::exists('footprints', 'footprint_id')->where('footprint_owner_u_fk', Auth::id()),
            ],
            'category' => [
                'nullable',
                'integer',
                Rule::exists('part_categories', 'category_id')->where('part_category_owner_u_fk', Auth::id()),
            ],
            'manufacturer' => [
                'nullable',
                'integer',
                Rule::exists('manufacturers', 'manufacturer_id')->where('manufacturer_owner_u_fk', Auth::id()),
            ],
            'part_category_fk' => [
                'nullable',
                'integer',
                Rule::exists('part_categories', 'category_id')->where('part_category_owner_u_fk', Auth::id()),
            ],
            'min_quantity' => 'nullable|integer',
            'suppliers' => 'nullable|array',
            'suppliers.*.supplier_id' => $supplierIdRules,
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
