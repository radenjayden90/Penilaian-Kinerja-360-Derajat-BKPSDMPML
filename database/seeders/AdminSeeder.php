<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Employee;
use App\Models\Role;
use App\Enums\EmployeeRole;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        $role = Role::where("name", EmployeeRole::ADMIN->value)->first();
        
        Employee::updateOrCreate([
            "nip" => "198001012005011001"
        ], [
            "email" => "admin@pemalang.go.id",
            "name" => "Administrator BKPSDM",
            "password" => Hash::make("198001012005011001"),
            "role_id" => $role?->id,
            "is_active" => true,
        ]);
    }
}