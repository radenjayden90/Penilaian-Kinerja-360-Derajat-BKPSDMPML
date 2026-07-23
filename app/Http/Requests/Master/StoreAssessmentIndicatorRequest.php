<?php

namespace App\Http\Requests\Master;
use Illuminate\Foundation\Http\FormRequest;

class StoreAssessmentIndicatorRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'is_active' => $this->has('is_active') ? true : false,
        ]);
    }

    public function rules(): array {
        return [
            'category_id' => 'required|exists:assessment_categories,id',
            'indicator' => 'required|string|max:255',
            'description' => 'nullable|string',
            'display_order' => 'required|integer|min:0',
            'is_active' => 'boolean',
        ];
    }
}
