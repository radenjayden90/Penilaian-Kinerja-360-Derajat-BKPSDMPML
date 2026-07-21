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
                "Memahami dan secara proaktif memenuhi kebutuhan masyarakat atau rekan kerja.",
                "Selalu bersikap ramah, cekatan, solutif, dan dapat diandalkan dalam memberikan pelayanan.",
                "Terus melakukan perbaikan tiada henti terhadap proses pelayanan atau pekerjaan."
            ],
            "Akuntabel" => [
                "Melaksanakan tugas dengan jujur, bertanggung jawab, cermat, disiplin, dan berintegritas tinggi.",
                "Menggunakan fasilitas dan barang milik instansi secara bertanggung jawab, efektif, dan efisien.",
                "Tidak menyalahgunakan kewenangan jabatan untuk kepentingan pribadi atau golongan."
            ],
            "Kompeten" => [
                "Aktif meningkatkan kompetensi diri untuk menjawab tantangan pekerjaan yang selalu berubah.",
                "Bersedia meluangkan waktu untuk membantu rekan kerja lain belajar dan berkembang.",
                "Selalu berupaya melaksanakan tugas yang diberikan dengan kualitas hasil terbaik."
            ],
            "Harmonis" => [
                "Menghargai setiap orang terlepas dari perbedaan latar belakang, suku, agama, maupun ras.",
                "Suka menolong orang lain yang sedang mengalami kesulitan baik terkait pekerjaan maupun hal lain.",
                "Mampu membangun dan menjaga lingkungan kerja yang kondusif, aman, dan nyaman."
            ],
            "Loyal" => [
                "Memegang teguh nilai-nilai Pancasila, UUD 1945, serta setia kepada instansi dan negara.",
                "Menjaga nama baik sesama rekan kerja, pimpinan, instansi, dan negara dalam setiap tindakan.",
                "Dapat dipercaya untuk menjaga rahasia jabatan, instansi, dan kerahasiaan data pekerjaan."
            ],
            "Adaptif" => [
                "Cepat menyesuaikan diri menghadapi perubahan lingkungan, kebijakan, atau teknologi baru.",
                "Terus berinovasi dan antusias mengembangkan kreativitas dalam mencari solusi atas masalah.",
                "Bertindak proaktif dan selalu mengambil inisiatif positif tanpa harus selalu menunggu perintah."
            ],
            "Kolaboratif" => [
                "Memberi kesempatan dan apresiasi kepada berbagai pihak untuk ikut berkontribusi.",
                "Terbuka dalam bekerja sama dengan rekan lintas bidang untuk menghasilkan karya/nilai tambah.",
                "Mampu menggerakkan dan mensinergikan pemanfaatan berbagai sumber daya untuk tujuan bersama."
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