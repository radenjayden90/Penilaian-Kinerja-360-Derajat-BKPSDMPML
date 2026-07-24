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
        <div class="doc-title">LAPORAN REKAPITULASI PENILAIAN KINERJA PER BIDANG</div>
        <div class="doc-subtitle">PERIODE: {{ strtoupper($period->name ?? 'SEMUA PERIODE') }}</div>
        @if($department)
            <div class="doc-subtitle" style="margin-top: 2px;">UNIT KERJA: {{ strtoupper($department->name) }}</div>
        @endif
    </div>

    <table class="table-print">
        <colgroup>
            <col style="width: 5%;">
            <col style="width: 35%;">
            <col style="width: 12%;">
            <col style="width: 12%;">
            <col style="width: 12%;">
            <col style="width: 24%;">
        </colgroup>
        <thead>
            <tr>
                <th>NO</th>
                <th>UNIT KERJA / BIDANG</th>
                <th>JUMLAH PEGAWAI</th>
                <th>RATA-RATA NILAI</th>
                <th>NILAI TERTINGGI</th>
                <th>DISTRIBUSI PREDIKAT</th>
            </tr>
        </thead>
        <tbody>
            @forelse($departmentStats as $index => $stat)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td><strong>{{ $stat['department']->name }}</strong></td>
                    <td class="text-center">{{ $stat['total_evaluated'] }} Orang</td>
                    <td class="text-center text-bold">{{ number_format($stat['average_score'], 2) }}</td>
                    <td class="text-center">{{ number_format($stat['highest_score'], 2) }}</td>
                    <td class="text-center">
                        <span style="font-size: 8.5pt;">
                            SB: <strong>{{ $stat['very_good'] }}</strong>, 
                            B: <strong>{{ $stat['good'] }}</strong>, 
                            C: <strong>{{ $stat['fair'] }}</strong>, 
                            PB: <strong>{{ $stat['needs_improvement'] }}</strong>
                        </span>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center" style="padding: 15px;">Tidak ada data bidang untuk periode ini.</td>
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
