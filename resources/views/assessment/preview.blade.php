<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Review Rapor Penilaian 360° - {{ $result->employee->name ?? '-' }}</title>
    
    <!-- Bootstrap 5 CSS & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        body {
            background-color: #0F172A;
            font-family: 'Inter', sans-serif;
            color: #F8FAFC;
            margin: 0;
            padding-top: 70px;
        }

        /* Sticky Control Header */
        .preview-header {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            height: 64px;
            background: rgba(15, 23, 42, 0.95);
            backdrop-filter: blur(12px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            z-index: 9999;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 24px;
        }

        .paper-container {
            display: flex;
            justify-content: center;
            padding: 30px 15px 60px 15px;
        }

        /* A4 Paper Simulation */
        .paper-a4 {
            background: #FFFFFF;
            color: #000000;
            width: 210mm;
            min-height: 297mm;
            padding: 1.2cm 2.5cm 2.5cm 2.5cm;
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.5);
            border-radius: 4px;
            font-family: 'Times New Roman', Times, serif;
            font-size: 11pt;
            line-height: 1.3;
            box-sizing: border-box;
        }

        /* Styles embedded from print.blade.php */
        .kop-container {
            width: 100%;
            margin-bottom: 4px;
        }
        .kop-table {
            margin: 0 auto;
            border-collapse: collapse;
        }
        .kop-logo-cell {
            vertical-align: middle;
            padding-right: 15px;
            text-align: right;
        }
        .kop-logo-cell img {
            height: 2.6cm;
            width: auto;
        }
        .kop-text-cell {
            vertical-align: middle;
            text-align: center;
        }
        .kop-header-1 {
            font-size: 14pt;
            font-weight: bold;
            text-transform: uppercase;
            line-height: 1.2;
            margin: 0;
        }
        .kop-header-2 {
            font-size: 10pt;
            font-weight: normal;
            line-height: 1.2;
            margin-top: 2px;
        }
        .kop-line {
            border-top: 2.5px solid #000000;
            border-bottom: 0.8px solid #000000;
            height: 2px;
            margin-top: 4px;
            margin-bottom: 18px;
        }
        .title-block {
            text-align: center;
            margin-bottom: 18px;
        }
        .doc-title {
            font-size: 12pt;
            font-weight: bold;
            text-transform: uppercase;
            margin: 0;
            letter-spacing: 0.5px;
        }
        .doc-subtitle {
            font-size: 10pt;
            font-weight: bold;
            margin-top: 4px;
        }
        .info-table {
            width: 100%;
            max-width: 100%;
            border-collapse: collapse;
            margin-bottom: 18px;
            box-sizing: border-box;
        }
        .info-table td {
            padding: 4px 6px;
            font-size: 10pt;
            vertical-align: top;
        }
        .info-table td.label {
            font-weight: bold;
            width: 1%;
            white-space: nowrap;
        }
        .info-table td.sep {
            width: 10px;
            text-align: center;
        }
        .report-table {
            width: 100%;
            max-width: 100%;
            border-collapse: collapse;
            margin-bottom: 12px;
            box-sizing: border-box;
        }
        .report-table th {
            background-color: #DBEAFE;
            color: #000000;
            font-size: 9.5pt;
            font-weight: bold;
            padding: 7px 6px;
            border: 1px solid #000000;
            text-align: center;
            vertical-align: middle;
        }
        .report-table td {
            padding: 6px;
            font-size: 9.5pt;
            border: 1px solid #000000;
            vertical-align: middle;
        }
        .text-center { text-align: center; }
        .note-text {
            font-size: 9pt;
            font-style: italic;
            color: #333333;
            margin-bottom: 15px;
        }
        .ttd-table {
            width: 100%;
            max-width: 100%;
            border-collapse: collapse;
            margin-top: 50px;
            margin-bottom: 10px;
            box-sizing: border-box;
        }
        .ttd-cell {
            width: 45%;
            text-align: center;
            vertical-align: top;
            font-size: 10pt;
        }
        .footer-line {
            margin-top: 20px;
            border-top: 0.8px solid #666666;
            padding-top: 6px;
            font-size: 8pt;
            color: #555555;
            width: 100%;
        }

        @media print {
            .preview-header {
                display: none !important;
            }
            body {
                background: #FFFFFF !important;
                padding-top: 0 !important;
            }
            .paper-container {
                padding: 0 !important;
            }
            .paper-a4 {
                width: 100% !important;
                min-height: auto !important;
                box-shadow: none !important;
                border-radius: 0 !important;
                padding: 0 !important;
            }
        }
    </style>
</head>
<body>

    <!-- Sticky Control Header -->
    <header class="preview-header">
        <div class="d-flex align-items-center gap-3">
            <a href="javascript:history.back()" class="btn btn-outline-light btn-sm rounded-pill px-3">
                <i class="bi bi-arrow-left me-1"></i> Kembali
            </a>
            <div>
                <h6 class="mb-0 fw-bold text-white">Review Rapor Penilaian 360°</h6>
                <small class="text-slate-400" style="font-size: 11px;">Pegawai: {{ $result->employee->name ?? '-' }} (NIP. {{ $result->employee->nip ?? '-' }})</small>
            </div>
        </div>

        <div class="d-flex align-items-center gap-2">
            <button onclick="window.print()" class="btn btn-outline-light btn-sm rounded-pill px-3">
                <i class="bi bi-printer me-1"></i> Cetak / Simpan via Browser
            </button>
            <a href="{{ route('assessment.exportPdf', $result->id) }}?download=1" class="btn btn-danger btn-sm rounded-pill px-3 fw-semibold shadow-sm">
                <i class="bi bi-file-earmark-pdf-fill me-1"></i> Download File PDF
            </a>
        </div>
    </header>

    <!-- Document Paper Preview Container -->
    <div class="paper-container">
        <div class="paper-a4">
            @php
                $logoPath = public_path('images/logo-pemalang.png');
                $logoBase64 = file_exists($logoPath) ? 'data:image/png;base64,' . base64_encode(file_get_contents($logoPath)) : asset('images/logo-pemalang.png');

                $employee = $result->employee;
                $period = $result->period;
                $posName = strtolower($employee->position?->name ?? '');
                $isKabid = ($employee->position?->level == '2' || str_contains($posName, 'kepala bidang') || str_contains($posName, 'kabid') || str_contains($posName, 'sekretaris'));

                $catEnum = $result->category instanceof \App\Enums\ResultCategory ? $result->category : \App\Enums\ResultCategory::tryFrom($result->category ?? '');
                $catLabel = $catEnum ? $catEnum->label() : ucwords(strtolower((string)($result->category ?? '-')));

                $monthNum = (int)($period->month ?? date('m'));
                $monthName = \Carbon\Carbon::create()->month($monthNum)->isoFormat('MMMM');
                $yearVal = $period->year ?? date('Y');
                $periodeFormatted = "Penilaian Kinerja Bulan " . $monthName . " Tahun " . $yearVal;
            @endphp

            <!-- Centered Kop Surat with Logo Beside Text -->
            <div class="kop-container">
                <table class="kop-table">
                    <tr>
                        <td class="kop-logo-cell">
                            <img src="{{ $logoBase64 }}" alt="Logo Pemalang">
                        </td>
                        <td class="kop-text-cell">
                            <div class="kop-header-1">PEMERINTAH KABUPATEN PEMALANG</div>
                            <div class="kop-header-1">BADAN KEPEGAWAIAN DAN PENGEMBANGAN</div>
                            <div class="kop-header-1">SUMBER DAYA MANUSIA</div>
                            <div class="kop-header-2">Jalan Surohadikusumo Nomor 1, Pemalang, Jawa Tengah 52312</div>
                            <div class="kop-header-2">Telepon (0284) 321376 Fax (0284) 321502 Website: https://bkpsdm.pemalangkab.go.id</div>
                        </td>
                    </tr>
                </table>
            </div>

            <!-- Double Line Separator -->
            <div class="kop-line"></div>

            <!-- Title Block -->
            <div class="title-block">
                <h1 class="doc-title">RAPOR HASIL PENILAIAN KINERJA ASN 360 DERAJAT</h1>
                <div class="doc-subtitle">{{ $periodeFormatted }}</div>
            </div>

            <!-- Employee Information -->
            <table class="info-table">
                <tr>
                    <td class="label">Nama Pegawai</td>
                    <td class="sep">:</td>
                    <td style="width: 60%;"><strong>{{ $employee->name ?? '-' }}</strong></td>
                    <td class="label" style="padding-left: 10px;">NIP</td>
                    <td class="sep">:</td>
                    <td>{{ $employee->nip ?? '-' }}</td>
                </tr>
                <tr>
                    <td class="label">Jabatan</td>
                    <td class="sep">:</td>
                    <td>{{ $employee->position->name ?? '-' }}</td>
                    <td class="label" style="padding-left: 10px;">Tanggal Cetak</td>
                    <td class="sep">:</td>
                    <td>{{ \Carbon\Carbon::now()->isoFormat('D MMMM Y') }}</td>
                </tr>
                <tr>
                    <td class="label">Unit Kerja / OPD</td>
                    <td class="sep">:</td>
                    <td colspan="4">{{ $employee->department->name ?? '-' }}</td>
                </tr>
            </table>

            <!-- Assessment Data Table -->
            <table class="report-table">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Periode Penilaian / Komponen 360°</th>
                        <th>Atasan (50%)</th>
                        <th>Sejawat (50%)</th>
                        <th>Nilai Akhir</th>
                        <th>Predikat</th>
                    </tr>
                </thead>
                <tbody>
                    @if($isKabid)
                        <tr>
                            <td class="text-center">1</td>
                            <td>Penilaian Atasan (Kepala BKPSDM)</td>
                            <td class="text-center">{{ number_format($result->subordinate_average ?? 0, 2) }}</td>
                            <td class="text-center">{{ number_format($result->peer_average ?? 0, 2) }}</td>
                            <td class="text-center">{{ number_format(($result->subordinate_average ?? 0) * 10 * 0.50 + ($result->peer_average ?? 0) * 10 * 0.30 + ($result->superior_average ?? 0) * 10 * 0.20, 2) }}</td>
                            <td class="text-center">-</td>
                        </tr>
                        <tr>
                            <td class="text-center">2</td>
                            <td>Penilaian Sejawat (Rekan Kepala Bidang)</td>
                            <td class="text-center">-</td>
                            <td class="text-center">{{ number_format($result->peer_average ?? 0, 2) }}</td>
                            <td class="text-center">-</td>
                            <td class="text-center">-</td>
                        </tr>
                        <tr>
                            <td class="text-center">3</td>
                            <td>Penilaian Bawahan (Staf Divisi)</td>
                            <td class="text-center">-</td>
                            <td class="text-center">-</td>
                            <td class="text-center">-</td>
                            <td class="text-center">-</td>
                        </tr>
                    @else
                        <tr>
                            <td class="text-center">1</td>
                            <td>{{ $periodeFormatted }}</td>
                            <td class="text-center">{{ number_format($result->subordinate_average ?? 0, 2) }}</td>
                            <td class="text-center">{{ number_format($result->peer_average ?? 0, 2) }}</td>
                            <td class="text-center">{{ number_format($result->final_score ?? 0, 2) }}</td>
                            <td class="text-center">{{ $catLabel }}</td>
                        </tr>
                    @endif
                    <tr>
                        <td colspan="4" class="text-center">NILAI AKHIR KINERJA 360°</td>
                        <td class="text-center" colspan="2">{{ number_format($result->final_score ?? 0, 2) }} ({{ $catLabel }})</td>
                    </tr>
                </tbody>
            </table>

            <!-- Footnote -->
            <div class="note-text">
                * Catatan: Skor Atasan, Sejawat, dan Bawahan menggunakan skala 1–10. Nilai Akhir merupakan hasil konversi berbobot ke skala 100.
            </div>

            <!-- Official Signature Block Pushed to Bottom Right -->
            <table class="ttd-table">
                <tr>
                    <td style="width: 55%;"></td>
                    <td class="ttd-cell">
                        Pemalang, {{ \Carbon\Carbon::now()->isoFormat('D MMMM Y') }}<br>
                        Kepala BKPSDM Kabupaten Pemalang,
                        <br><br><br><br><br>
                        <strong><u>Khaeron, S.H., M.M.</u></strong><br>
                        NIP. 196803231990031012
                    </td>
                </tr>
            </table>

            <!-- Footer -->
            <div class="footer-line">
                <div style="float: left;">360° Kinerja ASN - BKPSDM Kabupaten Pemalang</div>
                <div style="float: right;">Dicetak pada {{ date('d/m/Y H:i') }} WIB</div>
                <div style="clear: both;"></div>
            </div>
        </div>
    </div>

</body>
</html>
