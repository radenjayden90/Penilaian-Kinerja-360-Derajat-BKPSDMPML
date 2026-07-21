<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Enums\PeriodStatus;

class Period extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $fillable = [
        "name", "month", "year", "start_date", "end_date", "is_active", "status"
    ];

    protected $casts = [
        "start_date" => "datetime",
        "end_date" => "datetime",
        "is_active" => "boolean",
        "status" => PeriodStatus::class,
    ];

    /**
     * Auto close active periods whose end_date has passed.
     */
    public static function autoCloseExpiredPeriods(): int
    {
        return static::where('is_active', true)
            ->where('end_date', '<=', \Carbon\Carbon::now())
            ->update([
                'is_active' => false,
                'status' => PeriodStatus::CLOSED->value,
            ]);
    }

    /**
     * Helper to get current active open period, automatically closing expired ones first.
     */
    public static function getActivePeriod()
    {
        static::autoCloseExpiredPeriods();
        return static::where('is_active', true)
            ->where('status', PeriodStatus::OPEN->value)
            ->where('end_date', '>', \Carbon\Carbon::now())
            ->first();
    }

    public function assessments() { return $this->hasMany(Assessment::class); }
    public function results() { return $this->hasMany(AssessmentResult::class); }

    public function scopeSearch($query, $search)
    {
        if (!$search) return $query;
        $term = mb_strtolower($search, 'UTF-8');
        return $query->where(\Illuminate\Support\Facades\DB::raw('LOWER(name)'), 'LIKE', '%' . $term . '%');
    }

    public function scopeFilterStatus($query, $status)
    {
        if ($status) {
            return $query->where('status', $status);
        }
        return $query;
    }

    public function scopeFilterMonth($query, $month)
    {
        if ($month) {
            return $query->where('month', $month);
        }
        return $query;
    }

    public function scopeFilterYear($query, $year)
    {
        if ($year) {
            return $query->where('year', $year);
        }
        return $query;
    }
}