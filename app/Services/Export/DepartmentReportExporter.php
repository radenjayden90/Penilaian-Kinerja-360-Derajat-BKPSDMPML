<?php

namespace App\Services\Export;

use App\Models\Period;
use App\Models\Department;
use Illuminate\Support\Collection;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;
use Illuminate\Support\Facades\Auth;

class DepartmentReportExporter
{
    protected Collection $departmentStats;
    protected ?Period $period;
    protected ?Department $department;

    public function __construct(Collection $departmentStats, ?Period $period = null, ?Department $department = null)
    {
        $this->departmentStats = $departmentStats;
        $this->period = $period;
        $this->department = $department;
    }

    public function export(): Spreadsheet
    {
        $spreadsheet = new Spreadsheet();

        $spreadsheet->getProperties()
            ->setCreator('BKPSDM Kabupaten Pemalang')
            ->setLastModifiedBy(Auth::user()->name ?? 'Administrator')
            ->setTitle('Rekapitulasi Penilaian Kinerja Per Bidang')
            ->setSubject('Laporan Penilaian ASN Per Bidang')
            ->setDescription('Laporan hasil penilaian ASN per unit kerja / bidang.')
            ->setCompany('BKPSDM Kabupaten Pemalang');

        $spreadsheet->getDefaultStyle()->getFont()->setName('Calibri')->setSize(11);

        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Laporan Per Bidang');

        // Header Banner
        $sheet->mergeCells('A1:F1');
        $sheet->setCellValue('A1', "    REKAPITULASI PENILAIAN KINERJA PER BIDANG\n    Badan Kepegawaian dan Pengembangan Sumber Daya Manusia - Kabupaten Pemalang");
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

        // Logo
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

        // Header Info
        $sheet->mergeCells('A2:F2');
        $sheet->setCellValue('A2', 'PERIODE: ' . strtoupper($this->period->name ?? 'SEMUA PERIODE') . ' | UNIT KERJA: ' . strtoupper($this->department->name ?? 'SEMUA OPD') . ' | TANGGAL CETAK: ' . date('d/m/Y') . ' | JAM EXPORT: ' . date('H.i') . ' WIB');
        $sheet->getStyle('A2')->applyFromArray([
            'font' => ['bold' => true, 'size' => 9, 'color' => ['rgb' => '1E40AF']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT, 'vertical' => Alignment::VERTICAL_CENTER],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'DBEAFE']],
        ]);
        $sheet->getRowDimension(2)->setRowHeight(20);
        $sheet->getRowDimension(3)->setRowHeight(10);

        // Table Headers
        $headers = ['No', 'Unit Kerja / Bidang', 'Jumlah Pegawai', 'Rata-rata Nilai Akhir', 'Nilai Tertinggi', 'Distribusi Predikat'];
        foreach ($headers as $colIdx => $h) {
            $colLetter = chr(65 + $colIdx);
            $sheet->setCellValue($colLetter . '4', $h);
        }

        $sheet->getStyle('A4:F4')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '2563EB']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
        ]);
        $sheet->getRowDimension(4)->setRowHeight(26);
        $sheet->freezePane('A5');

        // Table Data
        $row = 5;
        foreach ($this->departmentStats as $index => $stat) {
            $bgColor = ($index % 2 == 0) ? 'FFFFFF' : 'F8FAFC';

            $sheet->setCellValue('A' . $row, $index + 1);
            $sheet->setCellValue('B' . $row, $stat['department']->name);
            $sheet->setCellValue('C' . $row, $stat['total_evaluated'] . ' Orang');
            $sheet->setCellValue('D' . $row, number_format($stat['average_score'], 2));
            $sheet->setCellValue('E' . $row, number_format($stat['highest_score'], 2));
            
            $dist = "SB: {$stat['very_good']}, B: {$stat['good']}, C: {$stat['fair']}, PB: {$stat['needs_improvement']}";
            $sheet->setCellValue('F' . $row, $dist);

            $sheet->getStyle('A' . $row . ':F' . $row)->applyFromArray([
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $bgColor]],
                'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
            ]);

            $sheet->getStyle('A' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('C' . $row . ':F' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

            $sheet->getRowDimension($row)->setRowHeight(24);
            $row++;
        }

        $sheet->getStyle('A4:F' . ($row - 1))->applyFromArray([
            'borders' => [
                'allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'E2E8F0']],
                'outline' => ['borderStyle' => Border::BORDER_MEDIUM, 'color' => ['rgb' => '2563EB']],
            ]
        ]);

        // Footer
        $footerStart = $row + 2;
        $footerEnd = $footerStart + 2;

        $sheet->mergeCells('A' . $footerStart . ':F' . $footerEnd);
        $sheet->setCellValue('A' . $footerStart, 
            "DOKUMEN RESMI PELAPORAN PENILAIAN KINERJA ASN 360° - BKPSDM KABUPATEN PEMALANG\n" .
            "Diekspor Oleh : " . (Auth::user()->name ?? 'Administrator') . "  |  Tanggal Export : " . date('d F Y') . "  |  Jam Export : " . date('H.i') . " WIB"
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

        // Auto Size columns
        $sheet->getColumnDimension('A')->setAutoSize(true);
        $sheet->getColumnDimension('B')->setWidth(40);
        foreach (range('C', 'F') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $sheet->getPageSetup()->setOrientation(PageSetup::ORIENTATION_LANDSCAPE);
        $sheet->getPageSetup()->setPaperSize(PageSetup::PAPERSIZE_A4);
        $sheet->getPageSetup()->setFitToWidth(1);

        return $spreadsheet;
    }
}
