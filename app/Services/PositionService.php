<?php
namespace App\Services;
use App\Models\Position;

class PositionService
{
    public function getPaginated($search = null, $departmentId = null, $status = null, $perPage = 10, $sortColumn = 'name', $sortDirection = 'asc')
    {
        return Position::with('department')
            ->search($search)
            ->filterDepartment($departmentId)
            ->filterStatus($status)
            ->orderBy($sortColumn, $sortDirection)
            ->paginate($perPage);
    }

    public function create(array $data)
    {
        $data['is_active'] = $data['is_active'] ?? true;
        return Position::create($data);
    }

    public function update(Position $position, array $data)
    {
        $data['is_active'] = $data['is_active'] ?? false;
        $position->update($data);
        return $position;
    }

    public function delete(Position $position)
    {
        return $position->delete();
    }
}
