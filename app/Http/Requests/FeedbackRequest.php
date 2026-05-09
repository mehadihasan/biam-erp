<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class FeedbackRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->session()->get('cadre_auth') === true;
    }

    public function rules(): array
    {
        return [
            'options' => ['required', 'array', 'min:1'],
            'options.*' => ['string', Rule::in([
                'Room cleanliness',
                'Meal quality',
                'Staff behavior',
                'Maintenance support',
                'Billing concern',
                'Booking experience',
            ])],
        ];
    }
}
