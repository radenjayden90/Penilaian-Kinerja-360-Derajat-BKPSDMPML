<?php

namespace App\Services\Export;

use App\Models\Employee;
use App\Models\Assessment;
use App\Models\AssessmentCategory;
use App\Models\AssessmentIndicator;
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

class AssessmentHistoryExporter
{
    protected Employee $employee;
    protected Collection $results;

    public function __construct(Employee $employee, Collection $results)
    {
        $this->employee = $employee;
        $this->results = $results;
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
        $this->buildSheetRingkasan($sheet1);

        // 3. Sheet 2: Detail Penilaian
        $sheet2 = $spreadsheet->createSheet();
        $sheet2->setTitle('Detail Penilaian');
        $this->buildSheetDetailPenilaian($sheet2);

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

    protected function buildSheetRingkasan(Worksheet $sheet): void
    {
        $employee = $this->employee;
        $results = $this->results;

        // Position level check
        $posName = strtolower($employee->position?->name ?? '');
        $isKabid = ($employee->position?->level == '2' || str_contains($posName, 'kepala bidang') || str_contains($posName, 'kabid') || str_contains($posName, 'sekretaris'));

        // 1. Header Banner (Height ~68pt / 90px)
        $sheet->mergeCells('A1:G1');
        $sheet->setCellValue('A1', "    REKAPITULASI HISTORI HASIL PENILAIAN KINERJA ASN 360°\n    Badan Kepegawaian dan Pengembangan Sumber Daya Manusia - Kabupaten Pemalang");
        $sheet->getStyle('A1')->applyFromArray([
            'font' => ['bold' => true, 'size' => 14, 'color' => ['rgb' => 'FFFFFF']],
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
        $sheet->mergeCells('A2:G2');
        $sheet->setCellValue('A2', 'TOTAL HISTORI: ' . $results->count() . ' PERIODE | TANGGAL CETAK: ' . date('d/m/Y') . ' | JAM EXPORT: ' . date('H.i') . ' WIB');
        $sheet->getStyle('A2')->applyFromArray([
            'font' => ['bold' => true, 'size' => 9, 'color' => ['rgb' => '1E40AF']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT, 'vertical' => Alignment::VERTICAL_CENTER],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'DBEAFE']],
        ]);
        $sheet->getRowDimension(2)->setRowHeight(20);

        $sheet->getRowDimension(3)->setRowHeight(10);

        // 2. Section 1: Informasi Pegawai
        $sheet->mergeCells('A4:G4');
        $sheet->setCellValue('A4', 'INFORMASI PEGAWAI');
        $sheet->getStyle('A4')->applyFromArray([
            'font' => ['bold' => true, 'size' => 12, 'color' => ['rgb' => '0F172A']],
            'borders' => ['bottom' => ['borderStyle' => Border::BORDER_MEDIUM, 'color' => ['rgb' => '2563EB']]],
        ]);
        $sheet->getRowDimension(4)->setRowHeight(22);

        $infoData = [
            ['Nama Pegawai', ': ' . ($employee->name ?? '-'), 'Total Evaluasi', ': ' . $results->count() . ' Periode'],
            ['NIP', ': ' . ($employee->nip ?? '-'), 'Rata-Rata Nilai', ': ' . number_format($results->avg('final_score') ?? 0, 2)],
            ['Jabatan', ': ' . ($employee->position?->name ?? '-'), 'Unit Kerja', ': ' . ($employee->department?->name ?? '-')],
        ];

        $row = 5;
        foreach ($infoData as $info) {
            $sheet->setCellValue('A' . $row, $info[0]);
            $sheet->setCellValue('B' . $row, $info[1]);
            $sheet->mergeCells('B' . $row . ':D' . $row);
            
            $sheet->setCellValue('E' . $row, $info[2]);
            $sheet->setCellValue('F' . $row, $info[3]);
            $sheet->mergeCells('F' . $row . ':G' . $row);

            $sheet->getStyle('A' . $row)->applyFromArray([
                'font' => ['bold' => true, 'color' => ['rgb' => '0F172A']],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F8FAFC']],
            ]);
            $sheet->getStyle('E' . $row)->applyFromArray([
                'font' => ['bold' => true, 'color' => ['rgb' => '0F172A']],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F8FAFC']],
            ]);

            $sheet->getRowDimension($row)->setRowHeight(22);
            $row++;
        }

        $sheet->getStyle('A5:G7')->applyFromArray([
            'borders' => [
                'outline' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'E2E8F0']],
                'inside' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'F1F5F9']],
            ]
        ]);

        $sheet->getRowDimension(8)->setRowHeight(12);

        // 3. Section 2: Ringkasan Hasil Penilaian (4 KPI Cards)
        $latestResult = $results->first();
        $catEnum = $latestResult?->category instanceof \App\Enums\ResultCategory ? $latestResult->category : \App\Enums\ResultCategory::tryFrom($latestResult?->category ?? '');
        $latestPredkat = $catEnum ? $catEnum->label() : strtoupper((string)($latestResult?->category ?? '-'));

        $sheet->mergeCells('A9:B9'); $sheet->setCellValue('A9', 'TOTAL PERIODE');
        $sheet->mergeCells('A10:B10'); $sheet->setCellValue('A10', $results->count());

        $sheet->mergeCells('C9:D9'); $sheet->setCellValue('C9', 'RATA-RATA NILAI');
        $sheet->mergeCells('C10:D10'); $sheet->setCellValue('C10', number_format($results->avg('final_score') ?? 0, 2));

        $sheet->mergeCells('E9:F9'); $sheet->setCellValue('E9', 'NILAI TERAKHIR');
        $sheet->mergeCells('E10:F10'); $sheet->setCellValue('E10', number_format($latestResult?->final_score ?? 0, 2));

        $sheet->setCellValue('G9', 'PREDIKAT TERAKHIR');
        $sheet->setCellValue('G10', $latestPredkat);

        $sheet->getStyle('A9:G9')->applyFromArray([
            'font' => ['bold' => true, 'size' => 9, 'color' => ['rgb' => '1E40AF']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'DBEAFE']],
        ]);
        $sheet->getStyle('A10:F10')->applyFromArray([
            'font' => ['bold' => true, 'size' => 16, 'color' => ['rgb' => '1E3A8A']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'EFF6FF']],
        ]);
        AssessmentResultExporter::applyPredicateStyle($sheet, 'G10', $latestPredkat);

        $sheet->getStyle('A9:G10')->applyFromArray([
            'borders' => [
                'allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'BFDBFE']],
            ]
        ]);
        $sheet->getRowDimension(9)->setRowHeight(18);
        $sheet->getRowDimension(10)->setRowHeight(32);

        $sheet->getRowDimension(11)->setRowHeight(14);

        // 4. Section 3: Histori Penilaian (Table with Freeze Pane)
        $sheet->mergeCells('A12:G12');
        $sheet->setCellValue('A12', 'HISTORI PENILAIAN PERIODE');
        $sheet->getStyle('A12')->applyFromArray([
            'font' => ['bold' => true, 'size' => 12, 'color' => ['rgb' => '0F172A']],
        ]);
        $sheet->getRowDimension(12)->setRowHeight(22);

        if ($isKabid) {
            $headers = ['No', 'Periode Penilaian', 'Skor Atasan (50%)', 'Skor Sejawat (30%)', 'Skor Bawahan (20%)', 'Nilai Akhir 360°', 'Kategori Predikat'];
        } else {
            $headers = ['No', 'Periode Penilaian', 'Skor Atasan (50%)', 'Skor Sejawat (50%)', 'Skor Bawahan (-)', 'Nilai Akhir 360°', 'Kategori Predikat'];
        }

        foreach ($headers as $colIdx => $h) {
            $colLetter = chr(65 + $colIdx);
            $sheet->setCellValue($colLetter . '13', $h);
        }

        $sheet->getStyle('A13:G13')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '2563EB']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
        ]);
        $sheet->getRowDimension(13)->setRowHeight(26);

        // Enable Freeze Panes directly below header
        $sheet->freezePane('A14');

        $row = 14;
        foreach ($results as $index => $res) {
            $catEnum = $res->category instanceof \App\Enums\ResultCategory ? $res->category : \App\Enums\ResultCategory::tryFrom($res->category ?? '');
            $catLabel = $catEnum ? $catEnum->label() : strtoupper((string)($res->category ?? '-'));
            $bgColor = ($index % 2 == 0) ? 'FFFFFF' : 'F8FAFC';

            $sheet->setCellValue('A' . $row, $index + 1);
            $sheet->setCellValue('B' . $row, $res->period->name ?? '-');
            $sheet->setCellValue('C' . $row, $res->subordinate_average ?? 0);
            $sheet->setCellValue('D' . $row, $res->peer_average ?? 0);
            $sheet->setCellValue('E' . $row, $isKabid ? ($res->superior_average ?? 0) : '-');
            $sheet->setCellValue('F' . $row, $res->final_score ?? 0);
            $sheet->setCellValue('G' . $row, $catLabel);

            $sheet->getStyle('A' . $row . ':G' . $row)->applyFromArray([
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $bgColor]],
                'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
            ]);

            $sheet->getStyle('A' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('C' . $row . ':F' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            
            $sheet->getStyle('C' . $row)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_00);
            $sheet->getStyle('D' . $row)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_00);
            if ($isKabid) {
                $sheet->getStyle('E' . $row)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_00);
            } else {
                $sheet->getStyle('E' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            }
            $sheet->getStyle('F' . $row)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_00);

            AssessmentResultExporter::applyPredicateStyle($sheet, 'G' . $row, $catLabel);

            $sheet->getRowDimension($row)->setRowHeight(24);
            $row++;
        }

        $sheet->getStyle('A13:G' . ($row - 1))->applyFromArray([
            'borders' => [
                'allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'E2E8F0']],
                'outline' => ['borderStyle' => Border::BORDER_MEDIUM, 'color' => ['rgb' => '2563EB']],
            ]
        ]);

        // 5. Score Scale Clarification Footnote
        $noteRow = $row;
        $sheet->mergeCells('A' . $noteRow . ':G' . $noteRow);
        $sheet->setCellValue('A' . $noteRow, '* Catatan: Skor Atasan, Sejawat, dan Bawahan menggunakan skala 1–10. Nilai Akhir merupakan hasil konversi bobot ke skala 100.');
        $sheet->getStyle('A' . $noteRow)->applyFromArray([
            'font' => ['size' => 9, 'italic' => true, 'color' => ['rgb' => '64748B']],
            'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
        ]);
        $sheet->getRowDimension($noteRow)->setRowHeight(18);

        // 6. Executive Footer Card (Row + 3)
        $footerStart = $noteRow + 3;
        $footerEnd = $footerStart + 4;

        $sheet->mergeCells('A' . $footerStart . ':G' . $footerEnd);
        $sheet->setCellValue('A' . $footerStart, 
            "DOKUMEN RESMI PELAPORAN PENILAIAN KINERJA ASN 360° - BKPSDM KABUPATEN PEMALANG\n" .
            "Diekspor Oleh : " . (Auth::user()->name ?? 'Administrator') . "  |  Tanggal Export : " . date('d F Y') . "  |  Jam Export : " . date('H.i') . " WIB\n" .
            "Sistem Informasi SIKINERJA 360°  |  Versi System : v1.0  |  Status Operasional : Validated & Authenticated"
        );

        $sheet->getStyle('A' . $footerStart . ':G' . $footerEnd)->applyFromArray([
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

        foreach (range('A', 'G') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $sheet->getPageSetup()->setOrientation(PageSetup::ORIENTATION_LANDSCAPE);
        $sheet->getPageSetup()->setPaperSize(PageSetup::PAPERSIZE_A4);
        $sheet->getPageSetup()->setFitToWidth(1);
        $sheet->getPageSetup()->setFitToHeight(0);
        $sheet->getPageSetup()->setRowsToRepeatAtTopByStartAndEnd(13, 13);
    }

    protected function buildSheetDetailPenilaian(Worksheet $sheet): void
    {
        $employee = $this->employee;
        $results = $this->results;

        $sheet->mergeCells('A1:F1');
        $sheet->setCellValue('A1', 'DETAIL SKOR PER KATEGORI (SEMUA PERIODE)');
        $sheet->getStyle('A1')->applyFromArray([
            'font' => ['bold' => true, 'size' => 14, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '2563EB']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
        ]);
        $sheet->getRowDimension(1)->setRowHeight(34);

        $categories = AssessmentCategory::where('is_active', true)->orderBy('display_order')->get();
        $periodIds = $results->pluck('period_id')->filter();

        $assessments = Assessment::with(['scores.indicator'])
            ->where('employee_id', $employee->id)
            ->whereIn('period_id', $periodIds)
            ->where('status', 'SUBMITTED')
            ->get();

        $headers = ['No', 'Nama Kategori Assessment', 'Nilai Atasan (1-10)', 'Nilai Rekan Kerja (1-10)', 'Nilai Bawahan (1-10)', 'Nilai Rata-Rata Histori'];
        foreach ($headers as $idx => $h) {
            $colLetter = chr(65 + $idx);
            $sheet->setCellValue($colLetter . '3', $h);
        }

        $sheet->getStyle('A3:F3')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '2563EB']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
        ]);
        $sheet->getRowDimension(3)->setRowHeight(26);

        $sheet->freezePane('A4');

        $row = 4;
        foreach ($categories as $index => $cat) {
            $catScoresAtasan = collect();
            $catScoresRekan = collect();
            $catScoresBawahan = collect();

            foreach ($assessments as $asm) {
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
            
            $validAvgs = collect([$avgAtasan, $avgRekan, $avgBawahan])->filter(fn($v) => $v > 0);
            $catOverallAvg = $validAvgs->isNotEmpty() ? $validAvgs->avg() : 0;

            $bgColor = ($index % 2 == 0) ? 'FFFFFF' : 'F8FAFC';

            $sheet->setCellValue('A' . $row, $index + 1);
            $sheet->setCellValue('B' . $row, $cat->name);
            $sheet->setCellValue('C' . $row, $avgAtasan);
            $sheet->setCellValue('D' . $row, $avgRekan);
            $sheet->setCellValue('E' . $row, $avgBawahan);
            $sheet->setCellValue('F' . $row, $catOverallAvg);

            $sheet->getStyle('A' . $row . ':F' . $row)->applyFromArray([
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $bgColor]],
                'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
            ]);

            $sheet->getStyle('A' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('C' . $row . ':F' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

            $sheet->getStyle('C' . $row)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_00);
            $sheet->getStyle('D' . $row)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_00);
            $sheet->getStyle('E' . $row)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_00);
            $sheet->getStyle('F' . $row)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_00);

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

    protected function buildSheetGrafikPenilaian(Worksheet $sheet): void
    {
        $results = $this->results;
        $latestResult = $results->first();

        $sheet->mergeCells('A1:H1');
        $sheet->setCellValue('A1', 'DASHBOARD & GRAFIK TREN PENILAIAN HISTORI 360°');
        $sheet->getStyle('A1')->applyFromArray([
            'font' => ['bold' => true, 'size' => 14, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '2563EB']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
        ]);
        $sheet->getRowDimension(1)->setRowHeight(34);

        $catEnum = $latestResult?->category instanceof \App\Enums\ResultCategory ? $latestResult->category : \App\Enums\ResultCategory::tryFrom($latestResult?->category ?? '');
        $latestPredkat = $catEnum ? $catEnum->label() : strtoupper((string)($latestResult?->category ?? '-'));

        $sheet->mergeCells('A3:C3'); $sheet->setCellValue('A3', 'RATA-RATA NILAI HISTORI');
        $sheet->mergeCells('A4:C4'); $sheet->setCellValue('A4', number_format($results->avg('final_score') ?? 0, 2));

        $sheet->mergeCells('D3:F3'); $sheet->setCellValue('D3', 'PREDIKAT TERAKHIR');
        $sheet->mergeCells('D4:F4'); $sheet->setCellValue('D4', $latestPredkat);

        $sheet->mergeCells('G3:H3'); $sheet->setCellValue('G3', 'TOTAL PERIODE');
        $sheet->mergeCells('G4:H4'); $sheet->setCellValue('G4', $results->count());

        $sheet->getStyle('A3:H3')->applyFromArray([
            'font' => ['bold' => true, 'size' => 9, 'color' => ['rgb' => '1E40AF']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'DBEAFE']],
        ]);
        $sheet->getStyle('A4:F4')->applyFromArray([
            'font' => ['bold' => true, 'size' => 16, 'color' => ['rgb' => '1E3A8A']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'EFF6FF']],
        ]);
        AssessmentResultExporter::applyPredicateStyle($sheet, 'D4', $latestPredkat);

        $sheet->getStyle('A3:H4')->applyFromArray([
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'BFDBFE']]],
        ]);
        $sheet->getRowDimension(3)->setRowHeight(18);
        $sheet->getRowDimension(4)->setRowHeight(32);

        // Chart Source Data (Rows 26+)
        $sheet->setCellValue('A26', 'Periode');
        $sheet->setCellValue('B26', 'Nilai Akhir 360°');
        
        $chartRow = 27;
        foreach ($results->take(5) as $res) {
            $sheet->setCellValue('A' . $chartRow, $res->period->name ?? ('Periode ' . $chartRow));
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
            $title = new Title('Tren Perubahan Nilai Akhir Penilaian Kinerja 360° per Periode');

            $chart = new Chart('chart_history_360', $title, $legend, $plotArea);
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

    protected function buildSheetDetailIndikator(Worksheet $sheet): void
    {
        $employee = $this->employee;
        $results = $this->results;

        $sheet->mergeCells('A1:G1');
        $sheet->setCellValue('A1', 'DETAIL SKOR PER INDIKATOR (SEMUA PERIODE)');
        $sheet->getStyle('A1')->applyFromArray([
            'font' => ['bold' => true, 'size' => 14, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '2563EB']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
        ]);
        $sheet->getRowDimension(1)->setRowHeight(34);

        $indicators = AssessmentIndicator::with('category')->where('is_active', true)->orderBy('display_order')->get();
        $periodIds = $results->pluck('period_id')->filter();

        $assessments = Assessment::with(['scores'])
            ->where('employee_id', $employee->id)
            ->whereIn('period_id', $periodIds)
            ->where('status', 'SUBMITTED')
            ->get();

        $headers = ['No', 'Kategori', 'Indikator Penilaian', 'Nilai Atasan', 'Nilai Rekan', 'Nilai Bawahan', 'Nilai Rata-Rata'];
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
        foreach ($indicators as $index => $ind) {
            $scoresAtasan = collect();
            $scoresRekan = collect();
            $scoresBawahan = collect();

            foreach ($assessments as $asm) {
                $scoreItem = $asm->scores->firstWhere('indicator_id', $ind->id);
                if ($scoreItem) {
                    $v = $scoreItem->score;
                    if ($asm->assessment_type?->value == 'SUBORDINATE' || $asm->assessment_type == 'SUBORDINATE') {
                        $scoresAtasan->push($v);
                    } elseif ($asm->assessment_type?->value == 'PEER' || $asm->assessment_type == 'PEER') {
                        $scoresRekan->push($v);
                    } elseif ($asm->assessment_type?->value == 'SUPERIOR' || $asm->assessment_type == 'SUPERIOR') {
                        $scoresBawahan->push($v);
                    }
                }
            }

            $avgAtasan = $scoresAtasan->isNotEmpty() ? $scoresAtasan->avg() : 0;
            $avgRekan = $scoresRekan->isNotEmpty() ? $scoresRekan->avg() : 0;
            $avgBawahan = $scoresBawahan->isNotEmpty() ? $scoresBawahan->avg() : 0;
            
            $validAvgs = collect([$avgAtasan, $avgRekan, $avgBawahan])->filter(fn($v) => $v > 0);
            $overallAvg = $validAvgs->isNotEmpty() ? $validAvgs->avg() : 0;

            $bgColor = ($index % 2 == 0) ? 'FFFFFF' : 'F8FAFC';

            $sheet->setCellValue('A' . $row, $index + 1);
            $sheet->setCellValue('B' . $row, $ind->category?->name ?? '-');
            $sheet->setCellValue('C' . $row, $ind->indicator);
            $sheet->setCellValue('D' . $row, $avgAtasan);
            $sheet->setCellValue('E' . $row, $avgRekan);
            $sheet->setCellValue('F' . $row, $avgBawahan);
            $sheet->setCellValue('G' . $row, $overallAvg);

            $sheet->getStyle('A' . $row . ':G' . $row)->applyFromArray([
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $bgColor]],
                'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
            ]);

            $sheet->getStyle('A' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('D' . $row . ':G' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

            $sheet->getStyle('D' . $row)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_00);
            $sheet->getStyle('E' . $row)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_00);
            $sheet->getStyle('F' . $row)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_00);
            $sheet->getStyle('G' . $row)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_00);

            $sheet->getRowDimension($row)->setRowHeight(24);
            $row++;
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

    protected function buildSheetRiwayatPenilai(Worksheet $sheet): void
    {
        $employee = $this->employee;
        $results = $this->results;
        $periodIds = $results->pluck('period_id')->filter();

        $sheet->mergeCells('A1:F1');
        $sheet->setCellValue('A1', 'RIWAYAT DAN DAFTAR PENILAI 360° (SEMUA PERIODE)');
        $sheet->getStyle('A1')->applyFromArray([
            'font' => ['bold' => true, 'size' => 14, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '2563EB']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
        ]);
        $sheet->getRowDimension(1)->setRowHeight(34);

        $headers = ['No', 'Nama Penilai', 'Hubungan / Tipe Penilai', 'Tanggal Penilaian', 'Jam Submit', 'Status Penilaian'];
        foreach ($headers as $idx => $h) {
            $colLetter = chr(65 + $idx);
            $sheet->setCellValue($colLetter . '3', $h);
        }

        $sheet->getStyle('A3:F3')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '2563EB']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
        ]);
        $sheet->getRowDimension(3)->setRowHeight(26);

        $sheet->freezePane('A4');

        $assessments = Assessment::with(['assessor'])
            ->where('employee_id', $employee->id)
            ->whereIn('period_id', $periodIds)
            ->get();

        $row = 4;
        foreach ($assessments as $index => $asm) {
            $typeStr = match($asm->assessment_type?->value ?? $asm->assessment_type) {
                'SUBORDINATE' => 'Atasan',
                'PEER' => 'Rekan Kerja',
                'SUPERIOR' => 'Bawahan',
                'SELF' => 'Diri Sendiri',
                default => 'Penilai',
            };

            $bgColor = ($index % 2 == 0) ? 'FFFFFF' : 'F8FAFC';
            $statusLabel = strtoupper($asm->status?->value ?? $asm->status ?? 'SUBMITTED');

            $sheet->setCellValue('A' . $row, $index + 1);
            $sheet->setCellValue('B' . $row, $asm->assessor->name ?? 'Penilai ' . ($index + 1));
            $sheet->setCellValue('C' . $row, $typeStr);
            $sheet->setCellValue('D' . $row, $asm->submitted_at ? $asm->submitted_at->format('d/m/Y') : '-');
            $sheet->setCellValue('E' . $row, $asm->submitted_at ? $asm->submitted_at->format('H.i \W\I\B') : '-');
            $sheet->setCellValue('F' . $row, $statusLabel);

            $sheet->getStyle('A' . $row . ':F' . $row)->applyFromArray([
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $bgColor]],
                'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
            ]);

            $sheet->getStyle('A' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('C' . $row . ':F' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

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
