<?php

namespace App\Services;
use App\Models\AssessmentCategory;

class AssessmentCategoryService
{
    public function getPaginated($search = null, $status = null, $perPage = 10, $sortColumn = 'display_order', $sortDirection = 'asc')
    {
        return AssessmentCategory::search($search)
            ->filterStatus($status)
            ->orderBy($sortColumn, $sortDirection)
            ->paginate($perPage);
    }

    public function create(array $data)
    {
        $data['is_active'] = $data['is_active'] ?? true;
        return AssessmentCategory::create($data);
    }

    public function update(AssessmentCategory $category, array $data)
    {
        $data['is_active'] = $data['is_active'] ?? false;
        $category->update($data);
        return $category;
    }

    public function delete(AssessmentCategory $category)
    {
        return $category->delete();
    }
}
