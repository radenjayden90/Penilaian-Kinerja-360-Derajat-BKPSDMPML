<?php
namespace App\Http\Requests\Master;
use Illuminate\Foundation\Http\FormRequest;

class StoreEmployeeRequest extends FormRequest
{
    public function authorize(): bool { return true; }
    public function rules(): array {
        return [
            'nip' => 'required|string|max:50|unique:employees,nip',
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:employees,email',
            'password' => 'nullable|string|min:6',
            'phone' => 'nullable|string|max:20',
            'gender' => 'nullable|string|in:L,P',
            'birth_date' => 'nullable|date',
            'address' => 'nullable|string',
            'department_id' => 'required|exists:departments,id',
            'position_id' => 'required|exists:positions,id',
            'role_id' => 'required|exists:roles,id',
            'supervisor_id' => 'nullable|exists:employees,id',
            'is_active' => 'boolean',
        ];
    }
}
