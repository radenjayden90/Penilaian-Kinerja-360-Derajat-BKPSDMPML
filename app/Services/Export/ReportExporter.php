<?php

namespace App\Services\Export;

use App\Models\Period;
use App\Models\Department;
use App\Models\AssessmentCategory;
use App\Models\AssessmentIndicator;
use App\Models\Assessment;
use Illuminate\Support\Collection;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;
use PhpOffice\PhpSpreadsheet\Chart\Chart;
use PhpOffice\PhpSpreadsheet\Chart\DataSeries;
use PhpOffice\PhpSpreadsheet\Chart\DataSeriesValues;
use PhpOffice\PhpSpreadsheet\Chart\Legend;
use PhpOffice\PhpSpreadsheet\Chart\PlotArea;
use PhpOffice\PhpSpreadsheet\Chart\Title;
use Illuminate\Support\Facades\Auth;

class ReportExporter
{
    protected Collection $results;
    protected ?Period $period;
    protected ?Department $department;

    public function __construct(Collection $results, ?Period $period = null, ?Department $department = null)
    {
        $this->results = $results;
        $this->period = $period;
        $this->department = $department;
    }

    public function export(): Spreadsheet
    {
        $spreadsheet = new Spreadsheet();

        // 1. Setup Document Properties & Metadata
        $spreadsheet->getProperties()
            ->setCreator('BKPSDM Kabupaten Pemalang')
            ->setLastModifiedBy(Auth::user()->name ?? 'Administrator')
            ->setTitle('Rekapitulasi Penilaian ASN 360°')
            ->setSubject('Laporan Penilaian ASN')
            ->setDescription('Laporan hasil penilaian ASN berbasis 360 Degree Feedback.')
            ->setCompany('BKPSDM Kabupaten Pemalang');

        $spreadsheet->getDefaultStyle()->getFont()->setName('Calibri')->setSize(11);

        // 2. Sheet 1: Ringkasan Penilaian
        $sheet1 = $spreadsheet->getActiveSheet();
        $sheet1->setTitle('Ringkasan Penilaian');
        $this->buildSheetRekapitulasi($sheet1);

        // 3. Sheet 2: Detail Penilaian
        $sheet2 = $spreadsheet->createSheet();
        $sheet2->setTitle('Detail Penilaian');
        $this->buildSheetDetailKategori($sheet2);

        // 4. Sheet 3: Grafik Penilaian
        $sheet3 = $spreadsheet->createSheet();
        $sheet3->setTitle('Grafik Penilaian');
        $this->buildSheetGrafikPenilaian($sheet3);

        // 5. Sheet 4: Riwayat Penilai
        $sheet4 = $spreadsheet->createSheet();
        $sheet4->setTitle('Riwayat Penilai');
        $this->buildSheetRiwayatPenilai($sheet4);

        $spreadsheet->setActiveSheetIndex(0);

        return $spreadsheet;
    }

    protected function buildSheetRekapitulasi(Worksheet $sheet): void
    {
        $results = $this->results;
        $period = $this->period;
        $department = $this->department;

        // 1. Header Banner (Height ~68pt / 90px)
        $sheet->mergeCells('A1:J1');
        $sheet->setCellValue('A1', "    REKAPITULASI HISTORI HASIL PENILAIAN KINERJA ASN 360°\n    Badan Kepegawaian dan Pengembangan Sumber Daya Manusia - Kabupaten Pemalang");
        $sheet->getStyle('A1')->applyFromArray([
            'font' => ['bold' => true, 'size' => 15, 'color' => ['rgb' => 'FFFFFF']],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
                'wrapText' => true,
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '2563EB'],
            ],
        ]);
        $sheet->getRowDimension(1)->setRowHeight(68);

        // Logo Pemalang
        $logoPath = public_path('images/logo-pemalang.png');
        if (file_exists($logoPath)) {
            try {
                $drawing = new Drawing();
                $drawing->setName('Logo Pemalang');
                $drawing->setPath($logoPath);
                $drawing->setHeight(48);
                $drawing->setCoordinates('A1');
                $drawing->setOffsetX(15);
                $drawing->setOffsetY(10);
                $drawing->setWorksheet($sheet);
            } catch (\Exception $e) {}
        }

        // Header info card
        $sheet->mergeCells('A2:J2');
        $sheet->setCellValue('A2', 'PERIODE: ' . strtoupper($period->name ?? 'SEMUA PERIODE') . ' | UNIT KERJA: ' . strtoupper($department->name ?? 'SEMUA OPD') . ' | TANGGAL CETAK: ' . date('d/m/Y') . ' | JAM EXPORT: ' . date('H.i') . ' WIB');
        $sheet->getStyle('A2')->applyFromArray([
            'font' => ['bold' => true, 'size' => 9, 'color' => ['rgb' => '1E40AF']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT, 'vertical' => Alignment::VERTICAL_CENTER],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'DBEAFE']],
        ]);
        $sheet->getRowDimension(2)->setRowHeight(20);

        $sheet->getRowDimension(3)->setRowHeight(10);

        // 2. Filter / Report Info Block
        $infoData = [
            ['Periode Penilaian', ': ' . ($period->name ?? 'Semua Periode'), 'Total Pegawai', ': ' . $results->count() . ' Orang'],
            ['Unit Kerja / OPD', ': ' . ($department->name ?? 'Semua Unit Kerja'), 'Rata-Rata Nilai Akhir', ': ' . number_format($results->avg('final_score') ?? 0, 2)],
        ];

        $row = 4;
        foreach ($infoData as $info) {
            $sheet->setCellValue('A' . $row, $info[0]);
            $sheet->setCellValue('B' . $row, $info[1]);
            $sheet->mergeCells('B' . $row . ':E' . $row);

            $sheet->setCellValue('F' . $row, $info[2]);
            $sheet->setCellValue('G' . $row, $info[3]);
            $sheet->mergeCells('G' . $row . ':J' . $row);

            $sheet->getStyle('A' . $row)->applyFromArray([
                'font' => ['bold' => true, 'color' => ['rgb' => '0F172A']],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F8FAFC']],
            ]);
            $sheet->getStyle('F' . $row)->applyFromArray([
                'font' => ['bold' => true, 'color' => ['rgb' => '0F172A']],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F8FAFC']],
            ]);

            $sheet->getRowDimension($row)->setRowHeight(22);
            $row++;
        }

        $sheet->getStyle('A4:J5')->applyFromArray([
            'borders' => [
                'outline' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'E2E8F0']],
                'inside' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'F1F5F9']],
            ]
        ]);

        $sheet->getRowDimension(6)->setRowHeight(12);

        // 3. Dashboard KPI Summary Cards (Row 7-8)
        $avgScore = number_format($results->avg('final_score') ?? 0, 2);
        $maxScore = number_format($results->max('final_score') ?? 0, 2);
        $minScore = number_format($results->min('final_score') ?? 0, 2);

        $sheet->mergeCells('A7:C7'); $sheet->setCellValue('A7', 'TOTAL PEGAWAI');
        $sheet->mergeCells('A8:C8'); $sheet->setCellValue('A8', $results->count());

        $sheet->mergeCells('D7:F7'); $sheet->setCellValue('D7', 'RATA-RATA NILAI AKHIR');
        $sheet->mergeCells('D8:F8'); $sheet->setCellValue('D8', $avgScore);

        $sheet->mergeCells('G7:H7'); $sheet->setCellValue('G7', 'NILAI TERTINGGI');
        $sheet->mergeCells('G8:H8'); $sheet->setCellValue('G8', $maxScore);

        $sheet->mergeCells('I7:J7'); $sheet->setCellValue('I7', 'NILAI TERENDAH');
        $sheet->mergeCells('I8:J8'); $sheet->setCellValue('I8', $minScore);

        $sheet->getStyle('A7:J7')->applyFromArray([
            'font' => ['bold' => true, 'size' => 9, 'color' => ['rgb' => '1E40AF']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'DBEAFE']],
        ]);
        $sheet->getStyle('A8:J8')->applyFromArray([
            'font' => ['bold' => true, 'size' => 14, 'color' => ['rgb' => '1E3A8A']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'EFF6FF']],
        ]);

        $sheet->getStyle('A7:J8')->applyFromArray([
            'borders' => [
                'allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'BFDBFE']],
            ]
        ]);
        $sheet->getRowDimension(7)->setRowHeight(18);
        $sheet->getRowDimension(8)->setRowHeight(32);

        $sheet->getRowDimension(9)->setRowHeight(14);

        // 4. Main Table Headers (Row 10)
        $headers = ['No', 'NIP', 'Nama Pegawai', 'Unit Kerja', 'Jabatan', 'Nilai Atasan', 'Nilai Rekan', 'Nilai Bawahan', 'Nilai Akhir 360°', 'Kategori Predikat'];
        foreach ($headers as $colIdx => $h) {
            $colLetter = chr(65 + $colIdx);
            $sheet->setCellValue($colLetter . '10', $h);
        }

        $sheet->getStyle('A10:J10')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '2563EB']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
        ]);
        $sheet->getRowDimension(10)->setRowHeight(26);

        // Freeze Panes directly below table header
        $sheet->freezePane('A11');

        // Table Body
        $row = 11;
        foreach ($results as $index => $res) {
            $catVal = is_object($res->category) ? ($res->category->label() ?? $res->category->value) : $res->category;
            $catLabel = strtoupper(str_replace('_', ' ', (string)($catVal ?? '-')));
            $bgColor = ($index % 2 == 0) ? 'FFFFFF' : 'F8FAFC';

            $sheet->setCellValue('A' . $row, $index + 1);
            $sheet->setCellValue('B' . $row, $res->employee->nip ?? '-');
            $sheet->setCellValue('C' . $row, $res->employee->name ?? '-');
            $sheet->setCellValue('D' . $row, $res->employee->department->name ?? '-');
            $posName = $res->employee->position->name ?? '-';
            if (stripos($posName, 'kepala bidang') !== false) {
                $posName = 'Kepala Bidang';
            }
            $sheet->setCellValue('E' . $row, $posName);
            
            $subAvg = number_format($res->subordinate_average ?? 0, 2);
            $subWeight = ($res->subordinate_weight ?? 0) * 100;
            $sheet->setCellValue('F' . $row, "{$subAvg} ({$subWeight}%)");

            $peerAvg = number_format($res->peer_average ?? 0, 2);
            $peerWeight = ($res->peer_weight ?? 0) * 100;
            $sheet->setCellValue('G' . $row, "{$peerAvg} ({$peerWeight}%)");

            if (($res->superior_weight ?? 0) > 0) {
                $supAvg = number_format($res->superior_average ?? 0, 2);
                $supWeight = ($res->superior_weight ?? 0) * 100;
                $sheet->setCellValue('H' . $row, "{$supAvg} ({$supWeight}%)");
            } else {
                $sheet->setCellValue('H' . $row, '-');
            }

            $sheet->setCellValue('I' . $row, $res->final_score ?? 0);
            $sheet->setCellValue('J' . $row, $catLabel);

            $sheet->getStyle('A' . $row . ':J' . $row)->applyFromArray([
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $bgColor]],
                'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
            ]);

            $sheet->getStyle('A' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('B' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('F' . $row . ':I' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

            $sheet->getStyle('I' . $row)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_00);

            AssessmentResultExporter::applyPredicateStyle($sheet, 'J' . $row, $catLabel);

            $sheet->getRowDimension($row)->setRowHeight(24);
            $row++;
        }

        $sheet->getStyle('A10:J' . ($row - 1))->applyFromArray([
            'borders' => [
                'allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'E2E8F0']],
                'outline' => ['borderStyle' => Border::BORDER_MEDIUM, 'color' => ['rgb' => '2563EB']],
            ]
        ]);

        // 5. Score Scale Clarification Footnote
        $noteRow = $row;
        $sheet->mergeCells('A' . $noteRow . ':J' . $noteRow);
        $sheet->setCellValue('A' . $noteRow, '* Catatan: Skor Atasan, Sejawat, dan Bawahan ditampilkan dalam skala 1–10, sedangkan Nilai Akhir 360° merupakan hasil konversi bobot ke skala 100.');
        $sheet->getStyle('A' . $noteRow)->applyFromArray([
            'font' => ['size' => 9, 'italic' => true, 'color' => ['rgb' => '64748B']],
            'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
        ]);
        $sheet->getRowDimension($noteRow)->setRowHeight(18);

        // 6. Executive Footer Card (Row + 3)
        $footerStart = $noteRow + 3;
        $footerEnd = $footerStart + 4;

        $sheet->mergeCells('A' . $footerStart . ':J' . $footerEnd);
        $sheet->setCellValue('A' . $footerStart, 
            "DOKUMEN RESMI PELAPORAN PENILAIAN KINERJA ASN 360° - BKPSDM KABUPATEN PEMALANG\n" .
            "Diekspor Oleh : " . (Auth::user()->name ?? 'Administrator') . "  |  Tanggal Export : " . date('d F Y') . "  |  Jam Export : " . date('H.i') . " WIB\n" .
            "Sistem Informasi SIKINERJA 360°  |  Versi System : v1.0  |  Status Operasional : Validated & Authenticated"
        );

        $sheet->getStyle('A' . $footerStart . ':J' . $footerEnd)->applyFromArray([
            'font' => ['size' => 9, 'italic' => true, 'color' => ['rgb' => '475569']],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
                'wrapText' => true,
            ],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F8FAFC']],
            'borders' => [
                'allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'E2E8F0']],
            ],
        ]);

        foreach (range('A', 'J') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $sheet->getPageSetup()->setOrientation(PageSetup::ORIENTATION_LANDSCAPE);
        $sheet->getPageSetup()->setPaperSize(PageSetup::PAPERSIZE_A4);
        $sheet->getPageSetup()->setFitToWidth(1);
        $sheet->getPageSetup()->setFitToHeight(0);
        $sheet->getPageSetup()->setRowsToRepeatAtTopByStartAndEnd(10, 10);
    }

    protected function buildSheetDetailKategori(Worksheet $sheet): void
    {
        $results = $this->results;

        $sheet->mergeCells('A1:G1');
        $sheet->setCellValue('A1', 'DETAIL PENILAIAN BERAKHLAK / KATEGORI PEGAWAI');
        $sheet->getStyle('A1')->applyFromArray([
            'font' => ['bold' => true, 'size' => 14, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '2563EB']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
        ]);
        $sheet->getRowDimension(1)->setRowHeight(34);

        $categories = AssessmentCategory::where('is_active', true)->orderBy('display_order')->get();
        $employeeIds = $results->pluck('employee_id')->filter();
        $periodIds = $results->pluck('period_id')->filter();

        $assessments = Assessment::with(['scores.indicator'])
            ->whereIn('employee_id', $employeeIds)
            ->whereIn('period_id', $periodIds)
            ->where('status', 'SUBMITTED')
            ->get();

        $headers = ['No', 'NIP', 'Nama Pegawai', 'Kategori Assessment', 'Nilai Atasan', 'Nilai Rekan', 'Nilai Bawahan'];
        foreach ($headers as $idx => $h) {
            $colLetter = chr(65 + $idx);
            $sheet->setCellValue($colLetter . '3', $h);
        }

        $sheet->getStyle('A3:G3')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '2563EB']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
        ]);
        $sheet->getRowDimension(3)->setRowHeight(26);

        $sheet->freezePane('A4');

        $row = 4;
        $counter = 1;
        foreach ($results as $res) {
            $empAssessments = $assessments->where('employee_id', $res->employee_id)->where('period_id', $res->period_id);

            foreach ($categories as $cat) {
                $catScoresAtasan = collect();
                $catScoresRekan = collect();
                $catScoresBawahan = collect();

                foreach ($empAssessments as $asm) {
                    $matchingScores = $asm->scores->filter(fn($sc) => $sc->indicator?->category_id == $cat->id);
                    if ($matchingScores->count() > 0) {
                        $avg = $matchingScores->avg('score');
                        if ($asm->assessment_type?->value == 'SUBORDINATE' || $asm->assessment_type == 'SUBORDINATE') {
                            $catScoresAtasan->push($avg);
                        } elseif ($asm->assessment_type?->value == 'PEER' || $asm->assessment_type == 'PEER') {
                            $catScoresRekan->push($avg);
                        } elseif ($asm->assessment_type?->value == 'SUPERIOR' || $asm->assessment_type == 'SUPERIOR') {
                            $catScoresBawahan->push($avg);
                        }
                    }
                }

                $avgAtasan = $catScoresAtasan->isNotEmpty() ? $catScoresAtasan->avg() : 0;
                $avgRekan = $catScoresRekan->isNotEmpty() ? $catScoresRekan->avg() : 0;
                $avgBawahan = $catScoresBawahan->isNotEmpty() ? $catScoresBawahan->avg() : 0;

                $bgColor = ($counter % 2 == 0) ? 'FFFFFF' : 'F8FAFC';

                $sheet->setCellValue('A' . $row, $counter);
                $sheet->setCellValue('B' . $row, $res->employee->nip ?? '-');
                $sheet->setCellValue('C' . $row, $res->employee->name ?? '-');
                $sheet->setCellValue('D' . $row, $cat->name);
                $sheet->setCellValue('E' . $row, $avgAtasan);
                $sheet->setCellValue('F' . $row, $avgRekan);
                $sheet->setCellValue('G' . $row, $avgBawahan);

                $sheet->getStyle('A' . $row . ':G' . $row)->applyFromArray([
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $bgColor]],
                    'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
                ]);

                $sheet->getStyle('A' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('B' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('E' . $row . ':G' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                $sheet->getStyle('E' . $row)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_00);
                $sheet->getStyle('F' . $row)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_00);
                $sheet->getStyle('G' . $row)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_00);

                $sheet->getRowDimension($row)->setRowHeight(24);
                $row++;
                $counter++;
            }
        }

        $sheet->getStyle('A3:G' . ($row - 1))->applyFromArray([
            'borders' => [
                'allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'E2E8F0']],
            ]
        ]);

        foreach (range('A', 'G') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $sheet->getPageSetup()->setOrientation(PageSetup::ORIENTATION_LANDSCAPE);
        $sheet->getPageSetup()->setPaperSize(PageSetup::PAPERSIZE_A4);
        $sheet->getPageSetup()->setFitToWidth(1);
        $sheet->getPageSetup()->setRowsToRepeatAtTopByStartAndEnd(3, 3);
    }

    protected function buildSheetGrafikPenilaian(Worksheet $sheet): void
    {
        $results = $this->results;

        $sheet->mergeCells('A1:H1');
        $sheet->setCellValue('A1', 'DASHBOARD & GRAFIK GRAFIK PENILAIAN REKAPITULASI 360°');
        $sheet->getStyle('A1')->applyFromArray([
            'font' => ['bold' => true, 'size' => 14, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '2563EB']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
        ]);
        $sheet->getRowDimension(1)->setRowHeight(34);

        $avgScore = number_format($results->avg('final_score') ?? 0, 2);
        $maxScore = number_format($results->max('final_score') ?? 0, 2);
        $minScore = number_format($results->min('final_score') ?? 0, 2);

        $sheet->mergeCells('A3:C3'); $sheet->setCellValue('A3', 'TOTAL PEGAWAI');
        $sheet->mergeCells('A4:C4'); $sheet->setCellValue('A4', $results->count());

        $sheet->mergeCells('D3:F3'); $sheet->setCellValue('D3', 'RATA-RATA NILAI REKAP');
        $sheet->mergeCells('D4:F4'); $sheet->setCellValue('D4', $avgScore);

        $sheet->mergeCells('G3:H3'); $sheet->setCellValue('G3', 'NILAI TERTINGGI');
        $sheet->mergeCells('G4:H4'); $sheet->setCellValue('G4', $maxScore);

        $sheet->getStyle('A3:H3')->applyFromArray([
            'font' => ['bold' => true, 'size' => 9, 'color' => ['rgb' => '1E40AF']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'DBEAFE']],
        ]);
        $sheet->getStyle('A4:H4')->applyFromArray([
            'font' => ['bold' => true, 'size' => 16, 'color' => ['rgb' => '1E3A8A']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'EFF6FF']],
        ]);

        $sheet->getStyle('A3:H4')->applyFromArray([
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'BFDBFE']]],
        ]);
        $sheet->getRowDimension(3)->setRowHeight(18);
        $sheet->getRowDimension(4)->setRowHeight(32);

        // Chart Source Data Block (Rows 26+)
        $sheet->setCellValue('A26', 'Nama Pegawai');
        $sheet->setCellValue('B26', 'Nilai Akhir 360°');

        $chartRow = 27;
        foreach ($results->take(8) as $res) {
            $sheet->setCellValue('A' . $chartRow, $res->employee->name ?? ('Pegawai ' . ($chartRow - 26)));
            $sheet->setCellValue('B' . $chartRow, $res->final_score ?? 0);
            $chartRow++;
        }

        try {
            $dataSeriesLabels = [
                new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, '\'Grafik Penilaian\'!$B$26', null, 1),
            ];
            $xAxisTickValues = [
                new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, '\'Grafik Penilaian\'!$A$27:$A$' . ($chartRow - 1), null, $chartRow - 27),
            ];
            $dataSeriesValues = [
                new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER, '\'Grafik Penilaian\'!$B$27:$B$' . ($chartRow - 1), null, $chartRow - 27),
            ];

            $series = new DataSeries(
                DataSeries::TYPE_BARCHART,
                DataSeries::GROUPING_CLUSTERED,
                range(0, count($dataSeriesValues) - 1),
                $dataSeriesLabels,
                $xAxisTickValues,
                $dataSeriesValues
            );
            $series->setPlotDirection(DataSeries::DIRECTION_COL);

            $plotArea = new PlotArea(null, [$series]);
            $legend = new Legend(Legend::POSITION_RIGHT, null, false);
            $title = new Title('Perbandingan Nilai Akhir Penilaian Kinerja 360° Antar Pegawai');

            $chart = new Chart('chart_rekap_360', $title, $legend, $plotArea);
            $chart->setTopLeftPosition('A6');
            $chart->setBottomRightPosition('H24');

            $sheet->addChart($chart);
        } catch (\Exception $e) {}

        foreach (range('A', 'H') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $sheet->getPageSetup()->setOrientation(PageSetup::ORIENTATION_LANDSCAPE);
        $sheet->getPageSetup()->setPaperSize(PageSetup::PAPERSIZE_A4);
        $sheet->getPageSetup()->setFitToWidth(1);
    }

    protected function buildSheetRiwayatPenilai(Worksheet $sheet): void
    {
        $results = $this->results;
        $employeeIds = $results->pluck('employee_id')->filter();
        $periodIds = $results->pluck('period_id')->filter();

        $sheet->mergeCells('A1:G1');
        $sheet->setCellValue('A1', 'RIWAYAT DAN DAFTAR PENILAI 360°');
        $sheet->getStyle('A1')->applyFromArray([
            'font' => ['bold' => true, 'size' => 14, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '2563EB']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
        ]);
        $sheet->getRowDimension(1)->setRowHeight(34);

        $headers = ['No', 'Nama Pegawai Dinilai', 'Nama Penilai', 'Hubungan / Tipe Penilai', 'Tanggal Penilaian', 'Status Penilaian'];
        foreach ($headers as $idx => $h) {
            $colLetter = chr(65 + $idx);
            $sheet->setCellValue($colLetter . '3', $h);
        }

        $sheet->getStyle('A3:G3')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '2563EB']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
        ]);
        $sheet->getRowDimension(3)->setRowHeight(26);

        $sheet->freezePane('A4');

        $assessments = Assessment::with(['assessor', 'employee'])
            ->whereIn('employee_id', $employeeIds)
            ->whereIn('period_id', $periodIds)
            ->get();

        $row = 4;
        foreach ($assessments as $index => $asm) {
            $typeStr = match($asm->assessment_type?->value ?? $asm->assessment_type) {
                'SUBORDINATE' => 'Atasan',
                'PEER' => 'Rekan Kerja',
                'SUPERIOR' => 'Bawahan',
                default => 'Penilai',
            };

            $bgColor = ($index % 2 == 0) ? 'FFFFFF' : 'F8FAFC';
            $statusLabel = strtoupper($asm->status?->value ?? $asm->status ?? 'SUBMITTED');

            $sheet->setCellValue('A' . $row, $index + 1);
            $sheet->setCellValue('B' . $row, $asm->employee->name ?? '-');
            $sheet->setCellValue('C' . $row, $asm->assessor->name ?? 'Penilai ' . ($index + 1));
            $sheet->setCellValue('D' . $row, $typeStr);
            $sheet->setCellValue('E' . $row, $asm->submitted_at ? $asm->submitted_at->format('d/m/Y H.i') : '-');
            $sheet->setCellValue('F' . $row, $statusLabel);

            $sheet->getStyle('A' . $row . ':F' . $row)->applyFromArray([
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $bgColor]],
                'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
            ]);

            $sheet->getStyle('A' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('D' . $row . ':F' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

            if ($statusLabel === 'SUBMITTED' || $statusLabel === 'SELESAI') {
                $sheet->getStyle('F' . $row)->applyFromArray([
                    'font' => ['bold' => true, 'color' => ['rgb' => '166534']],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'DCFCE7']],
                ]);
            } else {
                $sheet->getStyle('F' . $row)->applyFromArray([
                    'font' => ['bold' => true, 'color' => ['rgb' => '854D0E']],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'FEF9C3']],
                ]);
            }

            $sheet->getRowDimension($row)->setRowHeight(24);
            $row++;
        }

        $sheet->getStyle('A3:F' . ($row - 1))->applyFromArray([
            'borders' => [
                'allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'E2E8F0']],
            ]
        ]);

        foreach (range('A', 'F') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $sheet->getPageSetup()->setOrientation(PageSetup::ORIENTATION_LANDSCAPE);
        $sheet->getPageSetup()->setPaperSize(PageSetup::PAPERSIZE_A4);
        $sheet->getPageSetup()->setFitToWidth(1);
        $sheet->getPageSetup()->setRowsToRepeatAtTopByStartAndEnd(3, 3);
    }
}
