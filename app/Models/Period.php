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
        "start_date" => "date",
        "end_date" => "date",
        "is_active" => "boolean",
        "status" => PeriodStatus::class,
    ];

    public function assessments() { return $this->hasMany(Assessment::class); }
    public function results() { return $this->hasMany(AssessmentResult::class); }

    public function scopeSearch($query, $search)
    {
        return $query->where('name', 'ilike', '%' . $search . '%');
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