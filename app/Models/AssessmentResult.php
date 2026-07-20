<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use App\Enums\ResultCategory;
use App\Enums\CalculationStatus;

class AssessmentResult extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        "employee_id", "period_id", 
        "superior_average", "peer_average", "subordinate_average", 
        "superior_weight", "peer_weight", "subordinate_weight",
        "final_score", "category", "status", "pending_reason", "calculated_at"
    ];

    protected $casts = [
        "superior_average" => "float",
        "peer_average" => "float",
        "subordinate_average" => "float",
        "superior_weight" => "float",
        "peer_weight" => "float",
        "subordinate_weight" => "float",
        "final_score" => "float",
        "category" => ResultCategory::class,
        "status" => CalculationStatus::class,
        "calculated_at" => "datetime",
    ];

    public function employee() { return $this->belongsTo(Employee::class); }
    public function period() { return $this->belongsTo(Period::class); }
}