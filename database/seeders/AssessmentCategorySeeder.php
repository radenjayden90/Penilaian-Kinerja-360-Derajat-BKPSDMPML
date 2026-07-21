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
        
        $weightPerCategory = 100 / count($categories);

        foreach ($categories as $index => $c) {
            AssessmentCategory::updateOrCreate(
                ["name" => $c],
                [
                    "display_order" => $index + 1,
                    "weight" => $weightPerCategory
                ]
            );
        }
    }
}