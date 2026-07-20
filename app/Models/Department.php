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
        return $query->where('name', 'ilike', '%' . $search . '%')
                     ->orWhere('code', 'ilike', '%' . $search . '%');
    }

    public function scopeFilterStatus($query, $status)
    {
        if ($status !== null && $status !== '') {
            return $query->where('is_active', $status);
        }
        return $query;
    }
}