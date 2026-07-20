<?php

namespace App\Services;

use App\Models\Department;

class DepartmentService
{
    public function getPaginated($search = null, $status = null, $perPage = 10, $sortColumn = 'name', $sortDirection = 'asc')
    {
        return Department::search($search)
            ->filterStatus($status)
            ->orderBy($sortColumn, $sortDirection)
            ->paginate($perPage);
    }

    public function create(array $data)
    {
        if (isset($data['code'])) {
            $data['code'] = strtoupper($data['code']);
        }
        $data['is_active'] = $data['is_active'] ?? true;
        return Department::create($data);
    }

    public function update(Department $department, array $data)
    {
        if (isset($data['code'])) {
            $data['code'] = strtoupper($data['code']);
        }
        $data['is_active'] = $data['is_active'] ?? false;
        $department->update($data);
        return $department;
    }

    public function delete(Department $department)
    {
        return $department->delete();
    }
}
