<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Enums\EmployeeRole;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            ["name" => EmployeeRole::SUPER_ADMIN->value, "description" => "Super Administrator System"],
            ["name" => EmployeeRole::ADMIN->value, "description" => "Administrator BKPSDM"],
            ["name" => EmployeeRole::HEAD->value, "description" => "Kepala Bidang / Pimpinan"],
            ["name" => EmployeeRole::EMPLOYEE->value, "description" => "Pegawai Biasa"],
        ];
        
        foreach ($roles as $r) {
            Role::firstOrCreate(["name" => $r["name"]], $r);
        }
    }
}