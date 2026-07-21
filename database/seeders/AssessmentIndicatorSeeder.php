<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\AssessmentIndicator;
use App\Models\AssessmentCategory;

class AssessmentIndicatorSeeder extends Seeder
{
    public function run(): void
    {
        $indicatorsData = [
            "Berorientasi Pelayanan" => [
                "Memahami dan memenuhi kebutuhan pihak yang dilayani.",
                "Ramah, cekatan, solutif, dan dapat diandalkan dalam memberikan pelayanan.",
                "Melakukan perbaikan dalam pelaksanaan tugas dan pelayanan tiada henti."
            ],
            "Akuntabel" => [
                "Melaksanakan tugas dengan jujur, bertanggung jawab, cermat, disiplin, dan berintegritas tinggi.",
                "Menggunakan kekayaan dan barang milik negara secara bertanggung jawab, efektif, dan efisien.",
                "Tidak menyalahgunakan kewenangan jabatan."
            ],
            "Kompeten" => [
                "Meningkatkan kompetensi diri untuk menjawab tantangan tugas yang selalu berubah.",
                "Membantu orang lain belajar.",
                "Melaksanakan tugas dengan kualitas terbaik."
            ],
            "Harmonis" => [
                "Menghargai setiap orang apapun latar belakangnya.",
                "Suka menolong orang lain baik dalam pelaksanaan tugas, pelayanan, maupun kegiatan kemasyarakatan.",
                "Membangun lingkungan kerja yang kondusif."
            ],
            "Loyal" => [
                "Memegang teguh ideologi Pancasila, Undang-Undang Dasar Negara Republik Indonesia Tahun 1945, setia kepada Negara Kesatuan Republik Indonesia serta pemerintahan yang sah.",
                "Menjaga nama baik sesama aparatur sipil negara, pimpinan, instansi, dan negara.",
                "Menjaga rahasia jabatan dan negara."
            ],
            "Adaptif" => [
                "Cepat menyesuaikan diri menghadapi perubahan.",
                "Terus berinovasi dan mengembangkan kreativitas.",
                "Bertindak proaktif dalam pelaksanaan tugas dan memberikan pelayanan."
            ],
            "Kolaboratif" => [
                "Memberi kesempatan kepada berbagai pihak untuk berkontribusi.",
                "Terbuka dalam bekerja sama untuk menghasilkan nilai tambah.",
                "Menggerakkan pemanfaatan berbagai sumber daya untuk tujuan bersama."
            ],
        ];

        foreach ($indicatorsData as $categoryName => $indicators) {
            $cat = AssessmentCategory::where("name", $categoryName)->first();
            if ($cat) {
                foreach ($indicators as $index => $indicatorText) {
                    AssessmentIndicator::firstOrCreate(
                        [
                            "indicator" => $indicatorText, 
                            "category_id" => $cat->id
                        ],
                        [
                            "display_order" => $index + 1,
                            "is_active" => true
                        ]
                    );
                }
            }
        }
    }
}