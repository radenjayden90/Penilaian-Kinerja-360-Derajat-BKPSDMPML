<?php

namespace App\Http\Requests\Transaction;

use Illuminate\Foundation\Http\FormRequest;

class StoreAssessmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'target_id' => 'required|exists:employees,id',
            'type' => 'required|in:SUPERIOR,PEER,SUBORDINATE',
            'scores' => 'required|array',
            'scores.*' => 'required',
            'general_notes' => 'nullable|string',
        ];
    }
}
