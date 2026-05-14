<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class MealOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->session()->get('cadre_auth') === true;
    }

    public function rules(): array
    {
        return [
            'order_date' => ['required', 'date', 'after:today'],
            'meal_type' => ['required', Rule::in(['breakfast', 'lunch', 'supper'])],
            'menu_item' => ['required', 'string', 'max:100'],
            'quantity' => ['required', 'integer', 'min:1', 'max:20'],
        ];
    }
}
