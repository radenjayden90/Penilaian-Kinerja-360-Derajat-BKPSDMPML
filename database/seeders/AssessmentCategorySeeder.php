<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\AssessmentCategory;

class AssessmentCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            "Berorientasi Pelayanan",
            "Akuntabel",
            "Kompeten",
            "Harmonis",
            "Loyal",
            "Adaptif",
            "Kolaboratif"
        ];
        
        foreach ($categories as $index => $c) {
            AssessmentCategory::firstOrCreate(["name" => $c], [
                "display_order" => $index + 1
            ]);
        }
    }
}