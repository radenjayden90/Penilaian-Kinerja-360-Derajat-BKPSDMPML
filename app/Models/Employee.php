<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Enums\EmployeeRole;

class Employee extends Authenticatable
{
    use HasFactory, Notifiable, HasUuids, SoftDeletes;

    protected $fillable = [
        "nip", "name", "email", "password", "phone", "gender", "avatar", "birth_date",
        "address", "department_id", "position_id", "supervisor_id", "role_id", "is_active",
    ];

    protected $hidden = [
        "password",
        "remember_token",
    ];

    protected function casts(): array
    {
        return [
            "email_verified_at" => "datetime",
            "password" => "hashed",
            "is_active" => "boolean",
            "birth_date" => "date",
        ];
    }

    public function scopeSearch($query, $search)
    {
        if (!$search) return $query;
        $term = mb_strtolower($search, 'UTF-8');
        return $query->where(function($q) use ($term) {
            $q->where(\Illuminate\Support\Facades\DB::raw('LOWER(name)'), 'LIKE', '%' . $term . '%')
              ->orWhere(\Illuminate\Support\Facades\DB::raw('LOWER(nip)'), 'LIKE', '%' . $term . '%')
              ->orWhere(\Illuminate\Support\Facades\DB::raw('LOWER(email)'), 'LIKE', '%' . $term . '%');
        });
    }

    public function scopeFilterStatus($query, $status)
    {
        if ($status !== null && $status !== '') {
            return $query->where('is_active', $status);
        }
        return $query;
    }

    public function scopeFilterDepartment($query, $departmentId)
    {
        if ($departmentId) {
            return $query->where('department_id', $departmentId);
        }
        return $query;
    }

    public function scopeFilterPosition($query, $positionId)
    {
        if ($positionId) {
            return $query->where('position_id', $positionId);
        }
        return $query;
    }

    public function scopeFilterRole($query, $roleId)
    {
        if ($roleId) {
            return $query->where('role_id', $roleId);
        }
        return $query;
    }



    public function role() { return $this->belongsTo(Role::class); }
    public function department() { return $this->belongsTo(Department::class); }
    public function position() { return $this->belongsTo(Position::class); }
    public function supervisor() { return $this->belongsTo(Employee::class, "supervisor_id"); }
    public function subordinates() { return $this->hasMany(Employee::class, "supervisor_id"); }
    public function assessments() { return $this->hasMany(Assessment::class, "employee_id"); }
    public function results() { return $this->hasMany(AssessmentResult::class, "employee_id"); }
    public function assessmentResult() { return $this->hasOne(AssessmentResult::class, "employee_id")->latest('created_at'); }

    public function isKepalaBkpsdm(): bool
    {
        if ($this->nip === '196803231990031012') {
            return true;
        }

        $posName = mb_strtolower($this->position->name ?? '', 'UTF-8');
        if (str_contains($posName, 'kepala bkpsdm') || str_contains($posName, 'kepala badan kepegawaian')) {
            return true;
        }

        return $this->isHead() && !str_contains($posName, 'kepala bidang') && !str_contains($posName, 'kabid') && !str_contains($posName, 'kepala sub');
    }

    public function isAdmin(): bool
    {
        $roleName = $this->role->name ?? '';
        return in_array($roleName, [EmployeeRole::SUPER_ADMIN->value, EmployeeRole::ADMIN->value]);
    }

    public function isHead(): bool
    {
        return ($this->role->name ?? '') === EmployeeRole::HEAD->value;
    }

    public function isEmployee(): bool
    {
        return ($this->role->name ?? '') === EmployeeRole::EMPLOYEE->value;
    }

    public function hasRole(string $role): bool
    {
        return ($this->role->name ?? '') === $role;
    }
}