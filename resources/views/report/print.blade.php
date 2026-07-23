<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Hasil Penilaian Kinerja 360° ASN - BKPSDM Pemalang</title>
    <style>
        @page {
            size: A4 landscape;
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

        /* Kop Surat Header Government Style */
        .kop-container {
            width: 100%;
            margin-bottom: 4px;
        }

        .kop-table {
            width: 100%;
            border-collapse: collapse;
            margin: 0 auto;
        }

        .kop-logo-cell {
            width: 15%;
            vertical-align: middle;
            text-align: center;
            padding-right: 10px;
        }

        .kop-logo {
            height: 2.3cm;
            width: auto;
        }

        .kop-text-cell {
            width: 85%;
            vertical-align: middle;
            text-align: center;
        }

        .kop-header-bold {
            font-family: 'Calibri', Arial, sans-serif;
            font-size: 12pt;
            font-weight: bold;
            text-transform: uppercase;
            line-height: 1.25;
            margin: 0;
        }

        .kop-header-sub {
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
            margin-top: 5px;
            margin-bottom: 18px;
        }

        /* Document Title */
        .title-block {
            text-align: center;
            margin-bottom: 18px;
        }

        .doc-title {
            font-family: 'Calibri', Arial, sans-serif;
            font-size: 12pt;
            font-weight: bold;
            text-transform: uppercase;
            margin: 0;
            line-height: 1.3;
        }

        .doc-subtitle {
            font-family: 'Calibri', Arial, sans-serif;
            font-size: 12pt;
            font-weight: bold;
            margin-top: 4px;
            text-transform: uppercase;
        }

        /* Print Table - Fixed width strictly within margins */
        .table-print {
            width: 100%;
            max-width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
            margin-bottom: 15px;
            table-layout: fixed;
            word-wrap: break-word;
            word-break: break-word;
            box-sizing: border-box;
        }

        .table-print th, .table-print td {
            border: 1px solid #000000;
            padding: 4px 3px;
            font-family: 'Calibri', Arial, sans-serif;
            font-size: 9pt;
            line-height: 1.2;
            vertical-align: middle;
            word-wrap: break-word;
            word-break: break-word;
        }

        .table-print th {
            background-color: #F2F2F2;
            text-align: center;
            font-weight: bold;
            font-size: 9pt;
        }

        .text-center { text-align: center; }
        .text-bold { font-weight: bold; }

        /* Signature Container */
        .ttd-container {
            margin-top: 35px;
            width: 100%;
            page-break-inside: avoid;
        }

        .ttd-box {
            text-align: center;
            float: right;
            width: 260px;
            font-family: 'Calibri', Arial, sans-serif;
            font-size: 10pt;
            line-height: 1.25;
        }

        /* Footer */
        .footer-line {
            position: fixed;
            bottom: -0.4cm;
            left: 0;
            right: 0;
            height: 20px;
            border-top: 0.8px solid #666666;
            padding-top: 4px;
            font-size: 8pt;
            color: #555555;
            font-family: 'Calibri', Arial, sans-serif;
        }
    </style>
</head>
<body>
    @php
        $logoPath = public_path('images/logo-pemalang.png');
        $logoBase64 = file_exists($logoPath) ? 'data:image/png;base64,' . base64_encode(file_get_contents($logoPath)) : '';
    @endphp

    <!-- Kop Surat Header Government Style -->
    <div class="kop-container">
        <table class="kop-table">
            <tr>
                @if(!empty($logoBase64))
                    <td class="kop-logo-cell">
                        <img src="{{ $logoBase64 }}" alt="Logo Pemalang" class="kop-logo">
                    </td>
                @endif
                <td class="kop-text-cell">
                    <div class="kop-header-bold">PEMERINTAH KABUPATEN PEMALANG</div>
                    <div class="kop-header-bold">BADAN KEPEGAWAIAN DAN PENGEMBANGAN SUMBER DAYA MANUSIA</div>
                    <div class="kop-header-sub">Jalan Surohadikusumo Nomor 1, Pemalang, Jawa Tengah 52312</div>
                    <div class="kop-header-sub">Telepon (0284) 321376 Fax (0284) 321502 Website: https://bkpsdm.pemalangkab.go.id</div>
                </td>
            </tr>
        </table>
        <div class="kop-line"></div>
    </div>

    <div class="title-block">
        <div class="doc-title">LAPORAN REKAPITULASI HASIL PENILAIAN KINERJA 360 DERAJAT ASN</div>
        <div class="doc-subtitle">PERIODE: {{ strtoupper($period->name ?? 'SEMUA PERIODE') }}</div>
        @if($department)
            <div class="doc-subtitle" style="margin-top: 2px;">UNIT KERJA: {{ strtoupper($department->name) }}</div>
        @endif
    </div>

    <!-- Print Table -->
    <table class="table-print">
        <colgroup>
            <col style="width: 2.2%;">
            <col style="width: 13.8%;">
            <col style="width: 18%;">
            <col style="width: 18%;">
            <col style="width: 17%;">
            <col style="width: 6%;">
            <col style="width: 6%;">
            <col style="width: 6%;">
            <col style="width: 5%;">
            <col style="width: 5%;">
            <col style="width: 7%;">
        </colgroup>
        <thead>
            <tr>
                <th>NO</th>
                <th>NIP</th>
                <th>NAMA PEGAWAI</th>
                <th>UNIT KERJA / BIDANG</th>
                <th>JABATAN</th>
                <th>ATASAN (40%)</th>
                <th>SEJAWAT (30%)</th>
                <th>BAWAHAN (20%)</th>
                <th>DIRI (10%)</th>
                <th>NILAI AKHIR</th>
                <th>PREDIKAT</th>
            </tr>
        </thead>
        <tbody>
            @forelse($results as $index => $res)
                @php
                    $catLabel = \App\Enums\ResultCategory::formatLabel($res->category);
                @endphp
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ $res->employee->nip ?? '-' }}</td>
                    <td><strong>{{ $res->employee->name ?? '-' }}</strong></td>
                    <td>{{ $res->employee->department->name ?? '-' }}</td>
                    <td>{{ $res->employee->position->name ?? '-' }}</td>
                    <td class="text-center">{{ number_format($res->superior_score ?? 0, 2) }}</td>
                    <td class="text-center">{{ number_format($res->peer_score ?? 0, 2) }}</td>
                    <td class="text-center">{{ number_format($res->subordinate_score ?? 0, 2) }}</td>
                    <td class="text-center">{{ number_format($res->self_score ?? 0, 2) }}</td>
                    <td class="text-center text-bold"><strong>{{ number_format($res->final_score ?? 0, 2) }}</strong></td>
                    <td class="text-center"><strong>{{ $catLabel }}</strong></td>
                </tr>
            @empty
                <tr>
                    <td colspan="11" class="text-center" style="padding: 15px;">Tidak ada data hasil penilaian kinerja untuk periode ini.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Signature Container -->
    <div class="ttd-container">
        <div class="ttd-box">
            <p style="margin-bottom: 3px;">Pemalang, {{ \Carbon\Carbon::now('Asia/Jakarta')->isoFormat('D MMMM Y') }}</p>
            <p style="margin-bottom: 75px;">Kepala BKPSDM Kabupaten Pemalang,</p>
            <p style="font-weight: bold; margin-bottom: 1px;"><u>Khaeron, S.H., M.M.</u></p>
            <p style="margin-top: 0px; font-size: 9.5pt;">NIP. 196803231990031012</p>
        </div>
    </div>

    <!-- Footer -->
    <div class="footer-line">
        <div style="float: left;">360° Kinerja ASN - BKPSDM Kabupaten Pemalang</div>
        <div style="float: right;">Dicetak pada {{ \Carbon\Carbon::now('Asia/Jakarta')->format('d/m/Y H:i') }} WIB</div>
        <div style="clear: both;"></div>
    </div>
</body>
</html>
