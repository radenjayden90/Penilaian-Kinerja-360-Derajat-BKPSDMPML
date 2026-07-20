<?php
namespace App\Services;
use App\Models\Period;
use Illuminate\Support\Facades\DB;
use App\Enums\PeriodStatus;

class PeriodService
{
    public function getPaginated($search = null, $month = null, $year = null, $status = null, $perPage = 10, $sortColumn = 'year', $sortDirection = 'desc')
    {
        return Period::search($search)
            ->filterMonth($month)
            ->filterYear($year)
            ->filterStatus($status)
            ->orderBy($sortColumn, $sortDirection)
            ->paginate($perPage);
    }

    public function create(array $data)
    {
        $data['is_active'] = $data['is_active'] ?? false;
        
        return DB::transaction(function () use ($data) {
            if ($data['is_active']) {
                Period::where('is_active', true)->update([
                    'is_active' => false,
                    'status' => 'CLOSED'
                ]);
            }
            return Period::create($data);
        });
    }

    public function update(Period $period, array $data)
    {
        $data['is_active'] = $data['is_active'] ?? false;
        
        return DB::transaction(function () use ($period, $data) {
            if ($data['is_active'] && !$period->is_active) {
                Period::where('is_active', true)->where('id', '!=', $period->id)->update([
                    'is_active' => false,
                    'status' => 'CLOSED'
                ]);
            }
            $period->update($data);
            return $period;
        });
    }

    public function delete(Period $period)
    {
        return $period->delete();
    }
}
