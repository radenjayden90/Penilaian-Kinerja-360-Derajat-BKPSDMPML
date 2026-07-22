<?php

namespace App\Services\Export;

use App\Models\AssessmentResult;
use App\Models\Assessment;
use App\Models\AssessmentCategory;
use App\Models\AssessmentIndicator;
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

class AssessmentResultExporter
{
    protected AssessmentResult $result;

    public function __construct(AssessmentResult $result)
    {
        $this->result = $result;
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

        $spreadsheet->setActiveSheetIndex(0);

        return $spreadsheet;
    }

    protected function buildSheetRingkasan(Worksheet $sheet): void
    {
        $result = $this->result;
        $employee = $result->employee;
        $period = $result->period;

        // Role check
        $roleName = strtoupper($employee->role->name ?? '');
        $isHead = ($roleName === 'HEAD');

        // 1. Header Banner (Height ~68pt / 90px)
        $sheet->mergeCells('A1:F1');
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

        // Add Logo inside header banner
        $logoPath = public_path('images/logo-pemalang.png');
        if (file_exists($logoPath)) {
            try {
                $drawing = new Drawing();
                $drawing->setName('Logo Pemalang');
                $drawing->setDescription('Logo Kabupaten Pemalang');
                $drawing->setPath($logoPath);
                $drawing->setHeight(48);
                $drawing->setCoordinates('A1');
                $drawing->setOffsetX(15);
                $drawing->setOffsetY(10);
                $drawing->setWorksheet($sheet);
            } catch (\Exception $e) {}
        }

        // Right side info card inside header row 2
        $sheet->mergeCells('A2:F2');
        $sheet->setCellValue('A2', 'PERIODE: ' . strtoupper($period->name ?? '-') . ' | TANGGAL CETAK: ' . date('d/m/Y') . ' | JAM EXPORT: ' . date('H.i') . ' WIB');
        $sheet->getStyle('A2')->applyFromArray([
            'font' => ['bold' => true, 'size' => 9, 'color' => ['rgb' => '1E40AF']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT, 'vertical' => Alignment::VERTICAL_CENTER],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'DBEAFE']],
        ]);
        $sheet->getRowDimension(2)->setRowHeight(20);

        $sheet->getRowDimension(3)->setRowHeight(10);

        // 2. Section 1: Informasi Pegawai
        $sheet->mergeCells('A4:F4');
        $sheet->setCellValue('A4', 'INFORMASI PEGAWAI');
        $sheet->getStyle('A4')->applyFromArray([
            'font' => ['bold' => true, 'size' => 12, 'color' => ['rgb' => '0F172A']],
            'borders' => ['bottom' => ['borderStyle' => Border::BORDER_MEDIUM, 'color' => ['rgb' => '2563EB']]],
        ]);
        $sheet->getRowDimension(4)->setRowHeight(22);

        $infoData = [
            ['Nama Pegawai', ': ' . ($employee->name ?? '-'), 'Total Evaluasi', ': 1 Periode'],
            ['NIP', ': ' . ($employee->nip ?? '-'), 'Rata-Rata Nilai', ': ' . number_format($result->final_score ?? 0, 2)],
            ['Jabatan', ': ' . ($employee->position?->name ?? '-'), 'Kategori Predikat', ': ' . strtoupper(($result->category instanceof \App\Enums\ResultCategory ? $result->category->label() : $result->category) ?? '-')],
            ['Unit Kerja', ': ' . ($employee->department?->name ?? '-'), 'Status Penilaian', ': ' . strtoupper($result->status?->value ?? $result->status ?? 'SELESAI')],
        ];

        $row = 5;
        foreach ($infoData as $info) {
            $sheet->setCellValue('A' . $row, $info[0]);
            $sheet->setCellValue('B' . $row, $info[1]);
            $sheet->mergeCells('B' . $row . ':C' . $row);
            
            $sheet->setCellValue('D' . $row, $info[2]);
            $sheet->setCellValue('E' . $row, $info[3]);
            $sheet->mergeCells('E' . $row . ':F' . $row);

            $sheet->getStyle('A' . $row)->applyFromArray([
                'font' => ['bold' => true, 'color' => ['rgb' => '0F172A']],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F8FAFC']],
            ]);
            $sheet->getStyle('D' . $row)->applyFromArray([
                'font' => ['bold' => true, 'color' => ['rgb' => '0F172A']],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F8FAFC']],
            ]);

            $sheet->getRowDimension($row)->setRowHeight(22);
            $row++;
        }

        $sheet->getStyle('A5:F8')->applyFromArray([
            'borders' => [
                'outline' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'E2E8F0']],
                'inside' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'F1F5F9']],
            ]
        ]);

        $sheet->getRowDimension(9)->setRowHeight(12);

        // 3. Section 2: Ringkasan Hasil Penilaian (KPI Dashboard Cards)
        $catEnum = $result->category instanceof \App\Enums\ResultCategory ? $result->category : \App\Enums\ResultCategory::tryFrom($result->category);
        $catLabel = $catEnum ? $catEnum->label() : strtoupper((string)$result->category);

        $sheet->mergeCells('A10:B10'); $sheet->setCellValue('A10', 'SKOR ATASAN');
        $sheet->mergeCells('A11:B11'); $sheet->setCellValue('A11', number_format($result->subordinate_average ?? 0, 2));

        $sheet->mergeCells('C10:D10'); $sheet->setCellValue('C10', 'SKOR SEJAWAT');
        $sheet->mergeCells('C11:D11'); $sheet->setCellValue('C11', number_format($result->peer_average ?? 0, 2));

        $sheet->setCellValue('E10', 'NILAI AKHIR');
        $sheet->setCellValue('E11', number_format($result->final_score ?? 0, 2));

        $sheet->setCellValue('F10', 'PREDIKAT');
        $sheet->setCellValue('F11', $catLabel);

        $sheet->getStyle('A10:F10')->applyFromArray([
            'font' => ['bold' => true, 'size' => 9, 'color' => ['rgb' => '1E40AF']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'DBEAFE']],
        ]);
        $sheet->getStyle('A11:E11')->applyFromArray([
            'font' => ['bold' => true, 'size' => 16, 'color' => ['rgb' => '1E3A8A']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'EFF6FF']],
        ]);
        self::applyPredicateStyle($sheet, 'F11', $catLabel);

        $sheet->getStyle('A10:F11')->applyFromArray([
            'borders' => [
                'allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'BFDBFE']],
            ]
        ]);
        $sheet->getRowDimension(10)->setRowHeight(18);
        $sheet->getRowDimension(11)->setRowHeight(32);

        $sheet->getRowDimension(12)->setRowHeight(14);

        // 4. Section 3: Histori & Rincian Penilaian (Table with Freeze Pane)
        $sheet->mergeCells('A13:F13');
        $sheet->setCellValue('A13', 'HISTORI & RINCIAN KOMPONEN PENILAIAN');
        $sheet->getStyle('A13')->applyFromArray([
            'font' => ['bold' => true, 'size' => 12, 'color' => ['rgb' => '0F172A']],
        ]);
        $sheet->getRowDimension(13)->setRowHeight(22);

        // Table Headers (Row 14)
        $headers = ['No', 'Komponen Penilaian', 'Bobot (%)', 'Skor Rata-Rata (1-10)', 'Skor Terbobot (10-100)', 'Keterangan'];
        foreach ($headers as $colIdx => $header) {
            $colLetter = chr(65 + $colIdx);
            $sheet->setCellValue($colLetter . '14', $header);
        }

        $sheet->getStyle('A14:F14')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '2563EB']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
        ]);
        $sheet->getRowDimension(14)->setRowHeight(26);

        // Remove freeze pane so user can scroll normally

        // Table Content
        if ($isHead) {
            $rowsData = [
                [1, 'Penilaian Atasan (Kepala BKPSDM)', 50, $result->subordinate_average ?? 0, ($result->subordinate_average ?? 0) * 10 * 0.50, 'Bobot 50%'],
                [2, 'Penilaian Sejawat (Rekan Kepala Bidang)', 30, $result->peer_average ?? 0, ($result->peer_average ?? 0) * 10 * 0.30, 'Bobot 30%'],
                [3, 'Penilaian Bawahan (Staf Divisi)', 20, $result->superior_average ?? 0, ($result->superior_average ?? 0) * 10 * 0.20, 'Bobot 20%'],
            ];
        } else {
            $rowsData = [
                [1, 'Penilaian Atasan (Kepala Bidang)', 50, $result->subordinate_average ?? 0, ($result->subordinate_average ?? 0) * 10 * 0.50, 'Bobot 50%'],
                [2, 'Penilaian Sejawat (Rekan Staff)', 50, $result->peer_average ?? 0, ($result->peer_average ?? 0) * 10 * 0.50, 'Bobot 50%'],
            ];
        }

        $row = 15;
        foreach ($rowsData as $index => $rowData) {
            $bgColor = ($index % 2 == 0) ? 'FFFFFF' : 'F8FAFC';
            
            $sheet->setCellValue('A' . $row, $rowData[0]);
            $sheet->setCellValue('B' . $row, $rowData[1]);
            $sheet->setCellValue('C' . $row, $rowData[2] . '%');
            $sheet->setCellValue('D' . $row, $rowData[3]);
            $sheet->setCellValue('E' . $row, $rowData[4]);
            $sheet->setCellValue('F' . $row, $rowData[5]);

            $sheet->getStyle('A' . $row . ':F' . $row)->applyFromArray([
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $bgColor]],
                'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
            ]);

            $sheet->getStyle('A' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('C' . $row . ':F' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

            $sheet->getStyle('D' . $row)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_00);
            $sheet->getStyle('E' . $row)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_00);

            $sheet->getRowDimension($row)->setRowHeight(24);
            $row++;
        }

        // Summary Row
        $sheet->mergeCells('A' . $row . ':D' . $row);
        $sheet->setCellValue('A' . $row, 'NILAI AKHIR KINERJA 360 DERAJAT');
        $sheet->setCellValue('E' . $row, $result->final_score ?? 0);
        $sheet->setCellValue('F' . $row, $catLabel);

        $sheet->getStyle('A' . $row . ':F' . $row)->applyFromArray([
            'font' => ['bold' => true],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'EFF6FF']],
            'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
        ]);
        $sheet->getStyle('A' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $sheet->getStyle('E' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('E' . $row)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_00);
        self::applyPredicateStyle($sheet, 'F' . $row, $catLabel);
        $sheet->getRowDimension($row)->setRowHeight(26);

        // Apply borders
        $sheet->getStyle('A14:F' . $row)->applyFromArray([
            'borders' => [
                'allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'E2E8F0']],
                'outline' => ['borderStyle' => Border::BORDER_MEDIUM, 'color' => ['rgb' => '2563EB']],
            ]
        ]);

        // 5. Score Scale Clarification Footnote
        $noteRow = $row + 1;
        $sheet->mergeCells('A' . $noteRow . ':F' . $noteRow);
        $sheet->setCellValue('A' . $noteRow, '* Catatan: Skor Atasan, Sejawat, dan Bawahan menggunakan skala 1–10. Nilai Akhir merupakan hasil konversi bobot ke skala 100.');
        $sheet->getStyle('A' . $noteRow)->applyFromArray([
            'font' => ['size' => 9, 'italic' => true, 'color' => ['rgb' => '64748B']],
            'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
        ]);
        $sheet->getRowDimension($noteRow)->setRowHeight(18);

        // 6. Executive Footer Card (Row + 3)
        $footerStart = $noteRow + 3;
        $footerEnd = $footerStart + 4;

        $sheet->mergeCells('A' . $footerStart . ':F' . $footerEnd);
        $sheet->setCellValue('A' . $footerStart, 
            "DOKUMEN RESMI PELAPORAN PENILAIAN KINERJA ASN 360° - BKPSDM KABUPATEN PEMALANG\n" .
            "Diekspor Oleh : " . (Auth::user()->name ?? 'Administrator') . "  |  Tanggal Export : " . date('d F Y') . "  |  Jam Export : " . date('H.i') . " WIB\n" .
            "Sistem Informasi SIKINERJA 360°  |  Versi System : v1.0  |  Status Operasional : Validated & Authenticated"
        );

        $sheet->getStyle('A' . $footerStart . ':F' . $footerEnd)->applyFromArray([
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

        foreach (range('A', 'F') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $sheet->getPageSetup()->setOrientation(PageSetup::ORIENTATION_LANDSCAPE);
        $sheet->getPageSetup()->setPaperSize(PageSetup::PAPERSIZE_A4);
        $sheet->getPageSetup()->setFitToWidth(1);
        $sheet->getPageSetup()->setFitToHeight(0);
        $sheet->getPageSetup()->setRowsToRepeatAtTopByStartAndEnd(14, 14);
    }

    protected function buildSheetDetailPenilaian(Worksheet $sheet): void
    {
        $result = $this->result;
        
        $sheet->mergeCells('A1:G1');
        $sheet->setCellValue('A1', 'DETAIL PENILAIAN BERAKHLAK / INDIKATOR');
        $sheet->getStyle('A1')->applyFromArray([
            'font' => ['bold' => true, 'size' => 14, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '2563EB']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
        ]);
        $sheet->getRowDimension(1)->setRowHeight(34);

        $categories = AssessmentCategory::where('is_active', true)->orderBy('display_order')->get();
        $assessments = Assessment::with(['scores.indicator'])
            ->where('employee_id', $result->employee_id)
            ->where('period_id', $result->period_id)
            ->where('status', 'SUBMITTED')
            ->get();

        $headers = ['Kategori Assessment', 'Indikator Penilaian', 'Nilai Atasan (1-10)', 'Nilai Rekan (1-10)', 'Nilai Bawahan (1-10)', 'Nilai Rata-Rata', 'Keterangan Evaluasi'];
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

        // Removed freezePane so user can scroll normally

        $row = 4;
        $counter = 0;
        foreach ($categories as $cat) {
            $indicators = AssessmentIndicator::where('category_id', $cat->id)->where('is_active', true)->orderBy('display_order')->get();
            $catStartRow = $row;

            foreach ($indicators as $ind) {
                $counter++;
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
                $catEvalLabel = $overallAvg >= 8.5 ? 'Sangat Baik' : ($overallAvg >= 7.5 ? 'Baik' : ($overallAvg >= 6.0 ? 'Cukup' : 'Perlu Pembinaan'));

                $bgColor = ($counter % 2 == 0) ? 'FFFFFF' : 'F8FAFC';

                $sheet->setCellValue('A' . $row, $cat->name);
                $sheet->setCellValue('B' . $row, $ind->indicator);
                $sheet->setCellValue('C' . $row, $avgAtasan);
                $sheet->setCellValue('D' . $row, $avgRekan);
                $sheet->setCellValue('E' . $row, $avgBawahan);
                $sheet->setCellValue('F' . $row, $overallAvg);
                $sheet->setCellValue('G' . $row, $catEvalLabel);

                $sheet->getStyle('A' . $row . ':G' . $row)->applyFromArray([
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $bgColor]],
                    'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
                ]);

                $sheet->getStyle('C' . $row . ':G' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                $sheet->getStyle('C' . $row)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_00);
                $sheet->getStyle('D' . $row)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_00);
                $sheet->getStyle('E' . $row)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_00);
                $sheet->getStyle('F' . $row)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_00);

                $sheet->getRowDimension($row)->setRowHeight(24);
                $row++;
            }

            // Merge category column if multiple indicators
            if ($row - 1 > $catStartRow) {
                $sheet->mergeCells('A' . $catStartRow . ':A' . ($row - 1));
                $sheet->getStyle('A' . $catStartRow)->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
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
        $result = $this->result;

        // Title
        $sheet->mergeCells('A1:H1');
        $sheet->setCellValue('A1', 'DASHBOARD & GRAFIK VISUALISASI HASIL PENILAIAN 360°');
        $sheet->getStyle('A1')->applyFromArray([
            'font' => ['bold' => true, 'size' => 14, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '2563EB']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
        ]);
        $sheet->getRowDimension(1)->setRowHeight(34);

        // Top Summary KPI Widgets
        $catEnum = $result->category instanceof \App\Enums\ResultCategory ? $result->category : \App\Enums\ResultCategory::tryFrom($result->category);
        $catLabel = $catEnum ? $catEnum->label() : strtoupper((string)$result->category);

        $sheet->mergeCells('A3:C3'); $sheet->setCellValue('A3', 'NILAI AKHIR KINERJA 360°');
        $sheet->mergeCells('A4:C4'); $sheet->setCellValue('A4', number_format($result->final_score ?? 0, 2));

        $sheet->mergeCells('D3:F3'); $sheet->setCellValue('D3', 'KATEGORI PREDIKAT');
        $sheet->mergeCells('D4:F4'); $sheet->setCellValue('D4', $catLabel);

        $sheet->mergeCells('G3:H3'); $sheet->setCellValue('G3', 'STATUS REKAP');
        $sheet->mergeCells('G4:H4'); $sheet->setCellValue('G4', strtoupper($result->status?->value ?? $result->status ?? 'SELESAI'));

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
        self::applyPredicateStyle($sheet, 'D4', $catLabel);

        $sheet->getStyle('A3:H4')->applyFromArray([
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'BFDBFE']]],
        ]);
        $sheet->getRowDimension(3)->setRowHeight(18);
        $sheet->getRowDimension(4)->setRowHeight(32);

        // Chart Data Source (Hidden Data Block on Rows 26-28)
        $sheet->setCellValue('A26', 'Tipe Penilai');
        $sheet->setCellValue('B26', 'Skor Rata-Rata (1-10)');
        $sheet->setCellValue('A27', 'Atasan');
        $sheet->setCellValue('B27', $result->subordinate_average ?? 0);
        $sheet->setCellValue('A28', 'Sejawat');
        $sheet->setCellValue('B28', $result->peer_average ?? 0);
        $sheet->setCellValue('A29', 'Bawahan');
        $sheet->setCellValue('B29', $result->superior_average ?? 0);

        // Build Native PhpSpreadsheet Column Chart
        try {
            $dataSeriesLabels = [
                new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, '\'Grafik Penilaian\'!$B$26', null, 1),
            ];
            $xAxisTickValues = [
                new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, '\'Grafik Penilaian\'!$A$27:$A$29', null, 3),
            ];
            $dataSeriesValues = [
                new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER, '\'Grafik Penilaian\'!$B$27:$B$29', null, 3),
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
            $title = new Title('Perbandingan Skor Rata-Rata Berdasarkan Tipe Penilai (Atasan, Sejawat, Bawahan)');

            $chart = new Chart('chart_result_360', $title, $legend, $plotArea);
            $chart->setTopLeftPosition('A6');
            $chart->setBottomRightPosition('H24');

            $sheet->addChart($chart);
        } catch (\Exception $e) {
            // Fallback if environment chart driver differs
        }

        foreach (range('A', 'H') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $sheet->getPageSetup()->setOrientation(PageSetup::ORIENTATION_LANDSCAPE);
        $sheet->getPageSetup()->setPaperSize(PageSetup::PAPERSIZE_A4);
        $sheet->getPageSetup()->setFitToWidth(1);
    }

    protected function buildSheetRiwayatPenilai(Worksheet $sheet): void
    {
        $result = $this->result;

        $sheet->mergeCells('A1:F1');
        $sheet->setCellValue('A1', 'RIWAYAT DAN DAFTAR PENILAI 360°');
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
            ->where('employee_id', $result->employee_id)
            ->where('period_id', $result->period_id)
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

            // Status badge styling
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

    public static function applyPredicateStyle(Worksheet $sheet, string $cell, ?string $label): void
    {
        $labelUpper = strtoupper((string)$label);

        $style = [
            'font' => ['bold' => true],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'allBorders' => ['borderStyle' => Border::BORDER_THIN],
            ],
        ];

        if (str_contains($labelUpper, 'SANGAT BAIK')) {
            $style['fill'] = ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'DCFCE7']];
            $style['font']['color'] = ['rgb' => '16A34A'];
            $style['borders']['allBorders']['color'] = ['rgb' => '86EFAC'];
        } elseif (str_contains($labelUpper, 'BAIK')) {
            $style['fill'] = ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F0FDF4']];
            $style['font']['color'] = ['rgb' => '166534'];
            $style['borders']['allBorders']['color'] = ['rgb' => 'BBF7D0'];
        } elseif (str_contains($labelUpper, 'CUKUP')) {
            $style['fill'] = ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'FEF9C3']];
            $style['font']['color'] = ['rgb' => 'F59E0B'];
            $style['borders']['allBorders']['color'] = ['rgb' => 'FDE047'];
        } elseif (str_contains($labelUpper, 'KURANG')) {
            $style['fill'] = ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'FFEDD5']];
            $style['font']['color'] = ['rgb' => 'C2410C'];
            $style['borders']['allBorders']['color'] = ['rgb' => 'FDBA74'];
        } elseif (str_contains($labelUpper, 'PEMBINAAN') || str_contains($labelUpper, 'SANGAT KURANG')) {
            $style['fill'] = ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'FEE2E2']];
            $style['font']['color'] = ['rgb' => 'DC2626'];
            $style['borders']['allBorders']['color'] = ['rgb' => 'FCA5A5'];
        } else {
            $style['fill'] = ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F8FAFC']];
            $style['font']['color'] = ['rgb' => '0F172A'];
            $style['borders']['allBorders']['color'] = ['rgb' => 'E2E8F0'];
        }

        $sheet->getStyle($cell)->applyFromArray($style);
    }
}
