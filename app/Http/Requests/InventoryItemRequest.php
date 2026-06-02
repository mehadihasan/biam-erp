<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class InventoryItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'inventory_category_id' => ['required', 'exists:inventory_categories,id'],
            'inventory_unit_id' => ['required', 'exists:inventory_units,id'],
            'unit_cost' => ['nullable', 'numeric', 'min:0', 'max:9999999999.99'],
            'location' => ['nullable', 'string', 'max:255'],
            'min_threshold' => ['required', 'integer', 'min:0'],
            'initial_stock_quantity' => ['nullable', 'integer', 'min:0'],
            'description' => ['nullable', 'string'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }
}
