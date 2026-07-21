<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\SoftDeletes;

class Department extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $fillable = ["name", "code", "description", "is_active"];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function employees() { return $this->hasMany(Employee::class); }

    public function scopeSearch($query, $search)
    {
        if (!$search) return $query;
        $term = mb_strtolower($search, 'UTF-8');
        return $query->where(function($q) use ($term) {
            $q->where(\Illuminate\Support\Facades\DB::raw('LOWER(name)'), 'LIKE', '%' . $term . '%')
              ->orWhere(\Illuminate\Support\Facades\DB::raw('LOWER(code)'), 'LIKE', '%' . $term . '%');
        });
    }

    public function scopeFilterStatus($query, $status)
    {
        if ($status !== null && $status !== '') {
            return $query->where('is_active', $status);
        }
        return $query;
    }
}