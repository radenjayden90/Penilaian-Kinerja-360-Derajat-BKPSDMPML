<?php

namespace App\Services;
use App\Models\AssessmentIndicator;

class AssessmentIndicatorService
{
    public function getPaginated($search = null, $categoryId = null, $status = null, $perPage = 10, $sortColumn = 'display_order', $sortDirection = 'asc')
    {
        return AssessmentIndicator::with('category')
            ->search($search)
            ->filterCategory($categoryId)
            ->filterStatus($status)
            ->orderBy($sortColumn, $sortDirection)
            ->paginate($perPage);
    }

    public function create(array $data)
    {
        $data['is_active'] = $data['is_active'] ?? true;
        return AssessmentIndicator::create($data);
    }

    public function update(AssessmentIndicator $indicator, array $data)
    {
        $data['is_active'] = $data['is_active'] ?? false;
        $indicator->update($data);
        return $indicator;
    }

    public function delete(AssessmentIndicator $indicator)
    {
        return $indicator->delete();
    }
}
