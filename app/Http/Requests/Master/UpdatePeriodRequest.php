<?php

namespace App\Http\Requests\Master;
use Illuminate\Foundation\Http\FormRequest;

class UpdatePeriodRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'status' => $this->input('status') ?: 'OPEN',
            'is_active' => $this->has('is_active') ? true : false,
        ]);
    }

    public function rules(): array {
        return [
            'name' => 'required|string|max:255',
            'month' => 'required|integer|min:1|max:12',
            'year' => 'required|integer|min:2000|max:2100',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'status' => 'nullable|string|in:OPEN,CLOSED,ARCHIVED',
            'is_active' => 'boolean',
        ];
    }
}
