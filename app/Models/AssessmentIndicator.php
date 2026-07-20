<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\SoftDeletes;

class AssessmentIndicator extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $fillable = ["category_id", "indicator", "description", "display_order", "is_active"];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function category() { return $this->belongsTo(AssessmentCategory::class, "category_id"); }

    public function scopeSearch($query, $search)
    {
        return $query->where('indicator', 'ilike', '%' . $search . '%');
    }

    public function scopeFilterStatus($query, $status)
    {
        if ($status !== null && $status !== '') {
            return $query->where('is_active', $status);
        }
        return $query;
    }

    public function scopeFilterCategory($query, $categoryId)
    {
        if ($categoryId) {
            return $query->where('category_id', $categoryId);
        }
        return $query;
    }
}