<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StockChangeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
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
    }
}
