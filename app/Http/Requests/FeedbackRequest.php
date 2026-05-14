<?php

namespace App\Http\Requests;

use App\Models\Feedback;
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
            'ratings' => ['required', 'array', 'size:'.count(Feedback::CATEGORIES)],
            'ratings.*' => ['required', Rule::in(Feedback::RATINGS)],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator): void {
            $ratings = $this->input('ratings', []);

            if (! is_array($ratings)) {
                return;
            }

            $submittedCategories = array_keys($ratings);
            $missingCategories = array_diff(Feedback::CATEGORIES, $submittedCategories);
            $extraCategories = array_diff($submittedCategories, Feedback::CATEGORIES);

            if ($missingCategories !== [] || $extraCategories !== []) {
                $validator->errors()->add('ratings', __('Please select one rating for every feedback category.'));
            }
        });
    }
}
