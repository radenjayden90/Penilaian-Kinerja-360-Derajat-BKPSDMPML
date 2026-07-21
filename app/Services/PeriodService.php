<?php

namespace App\Services;

use App\Models\Period;
use Illuminate\Support\Facades\DB;
use App\Enums\PeriodStatus;
use Carbon\Carbon;

class PeriodService
{
    public function getPaginated($search = null, $month = null, $year = null, $status = null, $perPage = 10, $sortColumn = 'year', $sortDirection = 'desc')
    {
        // Auto-close any expired periods before listing
        Period::autoCloseExpiredPeriods();

        return Period::search($search)
            ->filterMonth($month)
            ->filterYear($year)
            ->filterStatus($status)
            ->orderBy($sortColumn, $sortDirection)
            ->paginate($perPage);
    }

    public function create(array $data)
    {
        Period::autoCloseExpiredPeriods();

        $endDate = Carbon::parse($data['end_date']);
        
        // If end_date is in the past, it cannot be active and is automatically CLOSED
        if ($endDate->isPast()) {
            $data['is_active'] = false;
            $data['status'] = PeriodStatus::CLOSED->value;
        } else {
            // Automatically activate new period on save if valid future end date
            $data['is_active'] = true;
            $data['status'] = PeriodStatus::OPEN->value;
        }
        
        return DB::transaction(function () use ($data) {
            if ($data['is_active']) {
                Period::where('is_active', true)->update([
                    'is_active' => false,
                    'status' => PeriodStatus::CLOSED->value,
                ]);
            }
            return Period::create($data);
        });
    }

    public function update(Period $period, array $data)
    {
        Period::autoCloseExpiredPeriods();

        $endDate = Carbon::parse($data['end_date']);

        if ($endDate->isPast()) {
            $data['is_active'] = false;
            $data['status'] = PeriodStatus::CLOSED->value;
        } else {
            // Automatically set as active when admin saves
            $data['is_active'] = $data['is_active'] ?? true;
            if ($data['is_active']) {
                $data['status'] = PeriodStatus::OPEN->value;
            }
        }
        
        return DB::transaction(function () use ($period, $data) {
            if ($data['is_active']) {
                Period::where('is_active', true)->where('id', '!=', $period->id)->update([
                    'is_active' => false,
                    'status' => PeriodStatus::CLOSED->value,
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
