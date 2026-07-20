<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\AssessmentIndicator;
use App\Models\AssessmentCategory;

class AssessmentIndicatorSeeder extends Seeder
{
    public function run(): void
    {
        $cat = AssessmentCategory::where("name", "Berorientasi Pelayanan")->first();
        if ($cat) {
            AssessmentIndicator::firstOrCreate(["indicator" => "Memahami dan memenuhi kebutuhan masyarakat", "category_id" => $cat->id]);
            AssessmentIndicator::firstOrCreate(["indicator" => "Ramah, cekatan, solutif, dan dapat diandalkan", "category_id" => $cat->id]);
        }
    }
}