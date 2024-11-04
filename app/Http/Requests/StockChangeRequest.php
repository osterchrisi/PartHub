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
            'stock_changes.*.quantity' => 'required|integer|min:1',           // Must be an integer and at least 1
            'stock_changes.*.to_location' => 'nullable|integer',              // Optional integer
            'stock_changes.*.from_location' => 'nullable|integer',            // Optional integer
            'stock_changes.*.comment' => 'nullable|string|max:255',           // Optional, max 255 characters
            'stock_changes.*.part_id' => 'required|integer',                  // Required integer
            'stock_changes.*.change' => 'required|string|in:1,0,-1', // Required and must be one of the defined types
        ];
    }
}
