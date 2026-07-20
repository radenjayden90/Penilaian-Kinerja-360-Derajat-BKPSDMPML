<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Position;
use App\Models\Department;

class PositionSeeder extends Seeder
{
    public function run(): void
    {
        $dept = Department::first();
        if (!$dept) return;

        $positions = [
            ["department_id" => $dept->id, "name" => "Kepala Bidang", "level" => "Eselon III"],
            ["department_id" => $dept->id, "name" => "Analis SDM Aparatur", "level" => "Fungsional"],
        ];
        
        foreach ($positions as $p) {
            Position::firstOrCreate(["name" => $p["name"], "department_id" => $dept->id], $p);
        }
    }
}