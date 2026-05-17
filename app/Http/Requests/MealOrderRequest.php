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
        $submittedMealTypes = $this->input('meal_types', []);
        $submittedMealTypes = is_array($submittedMealTypes) ? $submittedMealTypes : [];

        $mealTypes = array_values(array_unique(array_filter(
            $submittedMealTypes,
            fn ($mealType): bool => is_string($mealType)
        )));

        return [
            'order_date' => ['required', 'date', 'after_or_equal:today'],
            'meal_types' => ['required', 'array', 'min:1'],
            'meal_types.*' => ['required', Rule::in(['breakfast', 'lunch', 'dinner'])],
            'quantities' => ['required', 'array'],
            'quantities.breakfast' => [in_array('breakfast', $mealTypes, true) ? 'required' : 'nullable', 'integer', 'min:1', 'max:20'],
            'quantities.lunch' => [in_array('lunch', $mealTypes, true) ? 'required' : 'nullable', 'integer', 'min:1', 'max:20'],
            'quantities.dinner' => [in_array('dinner', $mealTypes, true) ? 'required' : 'nullable', 'integer', 'min:1', 'max:20'],
        ];
    }
}
