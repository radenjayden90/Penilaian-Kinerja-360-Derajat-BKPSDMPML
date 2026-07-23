<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Department;

class DepartmentSeeder extends Seeder
{
    public function run(): void
    {
        $departments = [
            ["name" => "BKPSDM Kabupaten Pemalang", "code" => "BKPSDM"],
            ["name" => "Sekretariat", "code" => "SEKRETARIAT"],
            ["name" => "Bidang Pengadaan, Pemberhentian dan Informasi Kepegawaian", "code" => "PPIK"],
            ["name" => "Bidang Mutasi dan Promosi", "code" => "MUTASI"],
            ["name" => "Bidang Penilaian dan Evaluasi Kinerja Aparatur", "code" => "PEKA"],
            ["name" => "Bidang Pengembangan Sumber Daya Manusia", "code" => "PSDM"],
        ];
        
        foreach ($departments as $d) {
            Department::updateOrCreate(["code" => $d["code"]], [
                "name" => $d["name"],
                "is_active" => true
            ]);
        }
    }
}