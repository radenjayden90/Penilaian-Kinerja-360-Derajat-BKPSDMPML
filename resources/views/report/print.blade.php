<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Hasil Penilaian Kinerja 360° ASN - BKPSDM Pemalang</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Calibri', Arial, Helvetica, sans-serif;
            font-size: 12px;
            color: #000;
            background-color: #fff;
            padding: 20px;
        }
        .header-kop {
            text-align: center;
            border-bottom: 3px double #000;
            padding-bottom: 12px;
            margin-bottom: 20px;
        }
        .header-kop h3 {
            font-size: 18px;
            font-weight: 700;
            margin: 0;
            text-transform: uppercase;
        }
        .header-kop h4 {
            font-size: 15px;
            font-weight: 600;
            margin: 2px 0;
        }
        .header-kop p {
            font-size: 11px;
            margin: 0;
            color: #333;
        }
        .table-print {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        .table-print th, .table-print td {
            border: 1px solid #000;
            padding: 6px 8px;
            font-size: 11px;
        }
        .table-print th {
            background-color: #f2f2f2 !important;
            text-align: center;
            font-weight: 600;
        }
        .ttd-container {
            margin-top: 40px;
            display: flex;
            justify-content: flex-end;
        }
        .ttd-box {
            text-align: center;
            width: 250px;
        }
        @media print {
            .no-print { display: none !important; }
            body { padding: 0; }
        }
    </style>
</head>
<body>
    <!-- Floating Action Toolbar for Screen View -->
    <div class="no-print mb-4 d-flex justify-content-between align-items-center bg-light p-3 rounded border">
        <div>
            <strong>Mode Cetak Laporan Penilaian 360°</strong>
            <div class="text-muted small">Tekan tombol cetak di sebelah kanan untuk menyimpan sebagai PDF atau mencetak dokumen secara fisik.</div>
        </div>
        <div>
            <button onclick="window.print()" class="btn btn-primary me-2">
                <i class="bi bi-printer"></i> Cetak Dokumen
            </button>
            <button onclick="window.close()" class="btn btn-outline-secondary">
                Tutup Window
            </button>
        </div>
    </div>

    <!-- Kop Surat Header Government Style -->
    <div class="header-kop">
        <h3>PEMERINTAH KABUPATEN PEMALANG</h3>
        <h4>BADAN KEPEGAWAIAN DAN PENGEMBANGAN SUMBER DAYA MANUSIA</h4>
        <p>Jalan Surohadikusumo No. 1 Pemalang, Jawa Tengah 52312 | Telp/Fax: (0284) 321010</p>
        <p>Website: bkpsdm.pemalangkab.go.id | Email: bkpsdm@pemalangkab.go.id</p>
    </div>

    <div class="text-center mb-3">
        <h5 class="fw-bold mb-1">LAPORAN REKAPITULASI HASIL PENILAIAN KINERJA 360 DEGRAE ASN</h5>
        <div class="small fw-semibold">
            PERIODE: {{ strtoupper($period->name ?? 'SEMUA PERIODE') }}
            @if($department)
                | UNIT KERJA: {{ strtoupper($department->name) }}
            @endif
        </div>
    </div>

    <!-- Print Table -->
    <table class="table-print">
        <thead>
            <tr>
                <th style="width: 35px;">NO</th>
                <th>NIP</th>
                <th>NAMA PEGAWAI</th>
                <th>UNIT KERJA / BIDANG</th>
                <th>JABATAN</th>
                <th style="width: 70px;">NILAI ATASAN</th>
                <th style="width: 70px;">NILAI REKAN</th>
                <th style="width: 70px;">NILAI BAWAHAN</th>
                <th style="width: 70px;">NILAI AKHIR</th>
                <th style="width: 90px;">PREDIKAT</th>
            </tr>
        </thead>
        <tbody>
            @forelse($results as $index => $res)
                @php
                    $catVal = is_object($res->category) ? $res->category->value : $res->category;
                @endphp
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ $res->employee->nip ?? '-' }}</td>
                    <td><strong>{{ $res->employee->name ?? '-' }}</strong></td>
                    <td>{{ $res->employee->department->name ?? '-' }}</td>
                    @php
                        $posName = $res->employee->position->name ?? '-';
                        if (stripos($posName, 'kepala bidang') !== false) {
                            $posName = 'Kepala Bidang';
                        }
                    @endphp
                    <td>{{ $posName }}</td>
                    <td class="text-center">
                        {{ number_format($res->subordinate_average ?? 0, 2) }}
                        <br><span style="font-size: 9px; color: #666;">({{ ($res->subordinate_weight ?? 0) * 100 }}%)</span>
                    </td>
                    <td class="text-center">
                        {{ number_format($res->peer_average ?? 0, 2) }}
                        <br><span style="font-size: 9px; color: #666;">({{ ($res->peer_weight ?? 0) * 100 }}%)</span>
                    </td>
                    <td class="text-center">
                        @if(($res->superior_weight ?? 0) > 0)
                            {{ number_format($res->superior_average ?? 0, 2) }}
                            <br><span style="font-size: 9px; color: #666;">({{ ($res->superior_weight ?? 0) * 100 }}%)</span>
                        @else
                            -
                        @endif
                    </td>
                    <td class="text-center font-weight-bold"><strong>{{ number_format($res->final_score ?? 0, 2) }}</strong></td>
                    <td class="text-center"><strong>{{ str_replace('_', ' ', $catVal ?? '-') }}</strong></td>
                </tr>
            @empty
                <tr>
                    <td colspan="10" class="text-center py-3">Tidak ada data hasil penilaian kinerja untuk periode ini.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Signature Container -->
    <div class="ttd-container">
        <div class="ttd-box">
            <p class="mb-1">Pemalang, {{ \Carbon\Carbon::now()->isoFormat('D MMMM Y') }}</p>
            <p class="fw-semibold mb-5">Kepala BKPSDM Kabupaten Pemalang,</p>
            <br><br>
            <p class="fw-bold mb-0"><u>________________________</u></p>
            <p class="mb-0">NIP. 197501011998031002</p>
        </div>
    </div>
</body>
</html>
