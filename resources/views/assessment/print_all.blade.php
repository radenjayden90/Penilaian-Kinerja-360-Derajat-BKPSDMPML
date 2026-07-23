<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Rekap Histori Penilaian Kinerja 360° - {{ $employee->name ?? '-' }}</title>
    <style>
        @page {
            size: A4 portrait;
            margin-top: 1.2cm;
            margin-bottom: 1.2cm;
            margin-left: 1.5cm;
            margin-right: 1.5cm;
        }

        * {
            box-sizing: border-box;
        }

        body {
            font-family: 'Calibri', Arial, Helvetica, sans-serif;
            font-size: 12pt;
            line-height: 1.3;
            color: #000000;
            background-color: #ffffff;
            margin: 0;
            padding: 0;
        }

        /* Centered Kop Surat with Logo beside text */
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
            font-family: 'Calibri', Arial, sans-serif;
            font-size: 12pt;
            font-weight: bold;
            text-transform: uppercase;
            line-height: 1.25;
            margin: 0;
        }

        .kop-header-2 {
            font-family: 'Calibri', Arial, sans-serif;
            font-size: 9pt;
            font-weight: normal;
            line-height: 1.3;
            margin-top: 2px;
        }

        /* Garis Pembatas Kop Surat Ganda */
        .kop-line {
            border-top: 2.5px solid #000000;
            border-bottom: 0.8px solid #000000;
            height: 2px;
            margin-top: 4px;
            margin-bottom: 18px;
        }

        /* Document Title */
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

        /* Info Table */
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

        /* Main Data Table */
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
            font-size: 9pt;
            font-weight: bold;
            padding: 6px 5px;
            border: 1px solid #000000;
            text-align: center;
            vertical-align: middle;
            word-wrap: break-word;
        }

        .report-table td {
            padding: 5px;
            font-size: 9pt;
            border: 1px solid #000000;
            vertical-align: middle;
            word-wrap: break-word;
        }

        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .text-bold { font-weight: bold; }

        /* Notes Footnote */
        .note-text {
            font-size: 9pt;
            font-style: italic;
            color: #333333;
            margin-bottom: 15px;
        }

        /* Signature Table Pushed to Bottom Right */
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

        /* Footer */
        .footer-line {
            position: fixed;
            bottom: -0.4cm;
            left: 0;
            right: 0;
            height: 25px;
            border-top: 0.8px solid #666666;
            padding-top: 6px;
            font-size: 8pt;
            color: #555555;
        }
    </style>
</head>
<body>

    @php
        $logoPath = public_path('images/logo-pemalang.png');
        $logoBase64 = file_exists($logoPath) ? 'data:image/png;base64,' . base64_encode(file_get_contents($logoPath)) : asset('images/logo-pemalang.png');

        $posName = strtolower($employee->position?->name ?? '');
        $isKabid = ($employee->position?->level == '2' || str_contains($posName, 'kepala bidang') || str_contains($posName, 'kabid') || str_contains($posName, 'sekretaris'));
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
                    <div class="kop-header-1">BADAN KEPEGAWAIAN DAN PENGEMBANGAN SUMBER DAYA MANUSIA</div>
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
        <h1 class="doc-title">REKAPITULASI HISTORI HASIL PENILAIAN KINERJA ASN 360 DERAJAT</h1>
        <div class="doc-subtitle">SELURUH PERIODE PENILAIAN TEREVALUASI</div>
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

    <!-- Assessment History Table -->
    <table class="report-table">
        <thead>
            <tr>
                <th>No</th>
                <th>Periode Penilaian</th>
                @if($isKabid)
                    <th>Atasan (50%)</th>
                    <th>Sejawat (30%)</th>
                    <th>Bawahan (20%)</th>
                @else
                    <th>Atasan (50%)</th>
                    <th>Sejawat (50%)</th>
                @endif
                <th>Nilai Akhir</th>
                <th>Predikat</th>
            </tr>
        </thead>
        <tbody>
            @forelse($results as $index => $res)
                @php
                    $resCatLabel = \App\Enums\ResultCategory::formatLabel($res->category);
                    
                    $mNum = (int)($res->period->month ?? date('m'));
                    $mName = \Carbon\Carbon::create()->month($mNum)->isoFormat('MMMM');
                    $yVal = $res->period->year ?? date('Y');
                    $pText = "Penilaian Kinerja Bulan " . $mName . " Tahun " . $yVal;
                @endphp
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>
                        {{ $pText }}
                    </td>
                    @if($isKabid)
                        <td class="text-center">{{ number_format($res->subordinate_average ?? 0, 2) }}</td>
                        <td class="text-center">{{ number_format($res->peer_average ?? 0, 2) }}</td>
                        <td class="text-center">{{ number_format($res->superior_average ?? 0, 2) }}</td>
                    @else
                        <td class="text-center">{{ number_format($res->subordinate_average ?? 0, 2) }}</td>
                        <td class="text-center">{{ number_format($res->peer_average ?? 0, 2) }}</td>
                    @endif
                    <td class="text-center">{{ number_format($res->final_score ?? 0, 2) }}</td>
                    <td class="text-center">{{ $resCatLabel }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="{{ $isKabid ? 7 : 6 }}" class="text-center" style="padding: 15px; color: #555555;">Belum ada data riwayat hasil penilaian.</td>
                </tr>
            @endforelse
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
            <td class="ttd-cell" style="font-size: 10pt; line-height: 1.25;">
                Pemalang, {{ \Carbon\Carbon::now('Asia/Jakarta')->isoFormat('D MMMM Y') }}<br>
                Kepala BKPSDM Kabupaten Pemalang,
                <br><br><br><br><br><br>
                <strong><u>Khaeron, S.H., M.M.</u></strong><br>
                <span style="font-size: 9.5pt;">NIP. 196803231990031012</span>
            </td>
        </tr>
    </table>

    <!-- Footer -->
    <div class="footer-line">
        <div style="float: left;">360° Kinerja ASN - BKPSDM Kabupaten Pemalang</div>
        <div style="float: right;">Dicetak pada {{ \Carbon\Carbon::now('Asia/Jakarta')->format('d/m/Y H:i') }} WIB</div>
        <div style="clear: both;"></div>
    </div>

</body>
</html>
