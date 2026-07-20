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
        
        Employee::firstOrCreate([
            "email" => "admin@bkpsdm.go.id"
        ], [
            "nip" => "198001012005011001",
            "name" => "Admin BKPSDM",
            "password" => Hash::make("password"),
            "role_id" => $role?->id,
            "is_active" => true,
        ]);
    }
}