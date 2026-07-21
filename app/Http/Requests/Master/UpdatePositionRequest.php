<?php

namespace App\Http\Requests\Master;
use Illuminate\Foundation\Http\FormRequest;

class UpdatePositionRequest extends FormRequest
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
            'department_id' => 'required|exists:departments,id',
            'name' => 'required|string|max:255',
            'level' => 'required|integer|min:1|max:10',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ];
    }
}
