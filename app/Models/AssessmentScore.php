<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class AssessmentScore extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = ["assessment_id", "indicator_id", "score", "comment"];

    public function assessment() { return $this->belongsTo(Assessment::class); }
    public function indicator() { return $this->belongsTo(AssessmentIndicator::class, "indicator_id"); }
}