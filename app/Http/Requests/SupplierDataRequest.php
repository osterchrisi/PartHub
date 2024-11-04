<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SupplierDataRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'part_id' => 'required|integer',
            'suppliers' => 'required|array',
            'suppliers.*.supplier_id' => 'required|integer|required_with:suppliers.*.URL,suppliers.*.SPN,suppliers.*.price',
            'suppliers.*.URL' => 'nullable|url',
            'suppliers.*.SPN' => 'nullable|string|max:255',
            'suppliers.*.price' => 'nullable|numeric',
            'URL' => 'nullable|url',
            'SPN' => 'nullable|string|max:255',
            'price' => 'nullable|numeric',
        ];
    }
}
