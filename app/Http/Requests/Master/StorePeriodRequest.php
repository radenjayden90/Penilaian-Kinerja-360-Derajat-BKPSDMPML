<?php

namespace App\Http\Requests\Master;

use Illuminate\Foundation\Http\FormRequest;
use Carbon\Carbon;

class StorePeriodRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    protected function prepareForValidation(): void
    {
        $startDateVal = $this->input('start_date');
        $startTimeVal = $this->input('start_time', '00:00');
        $endDateVal = $this->input('end_date');
        $endTimeVal = $this->input('end_time', '23:59');

        if ($startDateVal && !str_contains($startDateVal, ' ')) {
            $startDateVal = trim($startDateVal . ' ' . $startTimeVal);
        }
        if ($endDateVal && !str_contains($endDateVal, ' ')) {
            $endDateVal = trim($endDateVal . ' ' . $endTimeVal);
        }

        $this->merge([
            'start_date' => $startDateVal,
            'end_date' => $endDateVal,
            'status' => 'OPEN',
            'is_active' => true,
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

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $endDate = $this->input('end_date');
            if ($endDate && Carbon::parse($endDate)->isPast()) {
                $validator->errors()->add('end_date', 'Tanggal & jam selesai sudah terlewat! Silakan pilih tanggal & jam yang akan datang.');
            }
        });
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Nama periode wajib diisi.',
            'month.required' => 'Bulan wajib diisi.',
            'year.required' => 'Tahun wajib diisi.',
            'start_date.required' => 'Tanggal & jam mulai wajib diisi.',
            'start_date.date' => 'Format tanggal & jam mulai tidak valid.',
            'end_date.required' => 'Tanggal & jam selesai wajib diisi.',
            'end_date.date' => 'Format tanggal & jam selesai tidak valid.',
            'end_date.after' => 'Tanggal & jam selesai harus setelah tanggal & jam mulai.',
        ];
    }
}
