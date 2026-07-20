<?php
namespace App\Services;
use App\Models\Employee;
use Illuminate\Support\Facades\Hash;

class EmployeeService
{
    public function getPaginated($search = null, $departmentId = null, $positionId = null, $roleId = null, $status = null, $perPage = 10, $sortColumn = 'name', $sortDirection = 'asc')
    {
        return Employee::with(['department', 'position', 'role', 'supervisor'])
            ->search($search)
            ->filterDepartment($departmentId)
            ->filterPosition($positionId)
            ->filterRole($roleId)
            ->filterStatus($status)
            ->orderBy($sortColumn, $sortDirection)
            ->paginate($perPage);
    }

    public function create(array $data)
    {
        $data['is_active'] = $data['is_active'] ?? true;
        if (empty($data['password'])) {
            $data['password'] = Hash::make($data['nip']);
        } else {
            $data['password'] = Hash::make($data['password']);
        }
        return Employee::create($data);
    }

    public function update(Employee $employee, array $data)
    {
        $data['is_active'] = $data['is_active'] ?? false;
        if (!empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }
        $employee->update($data);
        return $employee;
    }

    public function delete(Employee $employee)
    {
        return $employee->delete();
    }
}
