<?php
namespace App\Http\Requests\Master;
use Illuminate\Foundation\Http\FormRequest;

class UpdateAssessmentCategoryRequest extends FormRequest
{
    public function authorize(): bool { return true; }
    public function rules(): array {
        return [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'display_order' => 'required|integer|min:0',
            'is_active' => 'boolean',
        ];
    }
}
