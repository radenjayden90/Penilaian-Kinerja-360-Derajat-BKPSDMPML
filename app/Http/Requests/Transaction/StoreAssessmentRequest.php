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
            'scores.*.score' => 'required|integer|min:1|max:10',
            'scores.*.comment' => 'nullable|string',
            'general_notes' => 'nullable|string', // General note, optional (not in schema, but user asked for "Catatan Keseluruhan". We'll need to handle it or save it somewhere if we have a column, wait, Assessment schema doesn't have a note column. Let me check the migration again).
        ];
    }
}
