<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Department;

class DepartmentSeeder extends Seeder
{
    public function run(): void
    {
        $departments = [
            ["name" => "Bidang Mutasi dan Promosi", "code" => "MUT"],
            ["name" => "Bidang Pengembangan Kompetensi Aparatur", "code" => "BANGKOM"],
            ["name" => "Bidang Pengadaan, Pemberhentian dan Informasi", "code" => "PPI"],
        ];
        
        foreach ($departments as $d) {
            Department::firstOrCreate(["code" => $d["code"]], $d);
        }
    }
}