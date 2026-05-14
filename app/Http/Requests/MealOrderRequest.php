<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class MealOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->session()->get('cadre_auth') === true
            || $this->session()->get('guest_verified') === true;
    }

    public function rules(): array
    {
        return [
            'order_date' => ['required', 'date', 'after:today'],
            'meal_types' => ['required', 'array', 'min:1'],
            'meal_types.*' => ['required', Rule::in(['breakfast', 'lunch', 'supper'])],
            'quantity' => ['required', 'integer', 'min:1', 'max:20'],
        ];
    }
}
