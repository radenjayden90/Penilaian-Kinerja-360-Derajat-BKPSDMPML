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
        return $query->where('name', 'ilike', '%' . $search . '%')
                     ->orWhere('nip', 'ilike', '%' . $search . '%')
                     ->orWhere('email', 'ilike', '%' . $search . '%');
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
    public function assessmentResult() { return $this->hasOne(AssessmentResult::class, "employee_id")->latestOfMany(); }
}