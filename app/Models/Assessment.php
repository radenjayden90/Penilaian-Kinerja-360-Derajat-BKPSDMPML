<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use App\Enums\AssessmentType;
use App\Enums\AssessmentStatus;

class Assessment extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = ["period_id", "assessor_id", "employee_id", "assessment_type", "status", "notes", "submitted_at"];

    protected $casts = [
        "submitted_at" => "datetime",
        "assessment_type" => AssessmentType::class,
        "status" => AssessmentStatus::class,
    ];

    public function period() { return $this->belongsTo(Period::class); }
    public function assessor() { return $this->belongsTo(Employee::class, "assessor_id"); }
    public function employee() { return $this->belongsTo(Employee::class, "employee_id"); }
    public function scores() { return $this->hasMany(AssessmentScore::class); }
}