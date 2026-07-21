<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\SoftDeletes;

class AssessmentCategory extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $fillable = ["name", "description", "display_order", "is_active"];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function indicators() { return $this->hasMany(AssessmentIndicator::class, "category_id"); }

    public function scopeSearch($query, $search)
    {
        if (!$search) return $query;
        $term = mb_strtolower($search, 'UTF-8');
        return $query->where(\Illuminate\Support\Facades\DB::raw('LOWER(name)'), 'LIKE', '%' . $term . '%');
    }

    public function scopeFilterStatus($query, $status)
    {
        if ($status !== null && $status !== '') {
            return $query->where('is_active', $status);
        }
        return $query;
    }
}