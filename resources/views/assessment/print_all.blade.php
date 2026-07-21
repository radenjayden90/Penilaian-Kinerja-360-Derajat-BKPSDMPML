<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rekap Histori Rapor Penilaian Kinerja 360° - {{ $employee->name ?? '-' }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', Arial, sans-serif;
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
            padding: 8px 10px;
            font-size: 11px;
        }
        .table-print th {
            background-color: #f2f2f2 !important;
            text-align: center;
            font-weight: 600;
        }
        .info-table td {
            border: none;
            padding: 4px 0;
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
            <strong>Ekspor PDF - Rekapitulasi Histori Penilaian Kinerja 360°</strong>
            <div class="text-muted small">Tekan tombol cetak untuk menyimpan semua histori laporan sebagai satu dokumen PDF atau mencetaknya.</div>
        </div>
        <div>
            <button onclick="window.print()" class="btn btn-primary me-2">
                Cetak / Simpan PDF
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

    <div class="text-center mb-4">
        <h5 class="fw-bold mb-1">REKAPITULASI HISTORI HASIL PENILAIAN KINERJA 360 DERAJAT</h5>
        <div class="small fw-semibold">SEALURUH PERIODE PENILAIAN</div>
    </div>

    <!-- Employee Profile Info -->
    <div class="row mb-4">
        <div class="col-8">
            <table class="info-table w-100">
                <tr>
                    <td style="width: 130px;">Nama Pegawai</td>
                    <td style="width: 15px;">:</td>
                    <td><strong>{{ $employee->name ?? '-' }}</strong></td>
                </tr>
                <tr>
                    <td>NIP</td>
                    <td>:</td>
                    <td>{{ $employee->nip ?? '-' }}</td>
                </tr>
                <tr>
                    <td>Jabatan</td>
                    <td>:</td>
                    <td>{{ $employee->position->name ?? '-' }}</td>
                </tr>
                <tr>
                    <td>Unit Kerja / Bidang</td>
                    <td>:</td>
                    <td>{{ $employee->department->name ?? '-' }}</td>
                </tr>
            </table>
        </div>
    </div>

    <!-- Score Table -->
    @php
        $posName = strtolower($employee->position?->name ?? '');
        $isKabid = ($employee->position?->level == '2' || str_contains($posName, 'kepala bidang') || str_contains($posName, 'kabid') || str_contains($posName, 'sekretaris'));
    @endphp

    <table class="table-print">
        <thead>
            <tr>
                <th style="width: 40px;">NO</th>
                <th>PERIODE</th>
                @if($isKabid)
                    <th style="width: 100px;">ATASAN (50%)</th>
                    <th style="width: 100px;">SEJAWAT (30%)</th>
                    <th style="width: 100px;">BAWAHAN (20%)</th>
                @else
                    <th style="width: 120px;">ATASAN (50%)</th>
                    <th style="width: 120px;">SEJAWAT (50%)</th>
                @endif
                <th style="width: 110px;">NILAI AKHIR</th>
                <th style="width: 130px;">PREDIKAT</th>
            </tr>
        </thead>
        <tbody>
            @forelse($results as $index => $res)
                @php
                    $catEnum = $res->category instanceof \App\Enums\ResultCategory ? $res->category : \App\Enums\ResultCategory::tryFrom($res->category);
                @endphp
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>
                        {{ $res->period->name ?? '-' }}
                        <small class="text-muted d-block">Tahun {{ $res->period->year ?? '-' }}</small>
                    </td>
                    @if($isKabid)
                        <td class="text-center">{{ number_format($res->subordinate_average ?? 0, 2) }}</td>
                        <td class="text-center">{{ number_format($res->peer_average ?? 0, 2) }}</td>
                        <td class="text-center">{{ number_format($res->superior_average ?? 0, 2) }}</td>
                    @else
                        <td class="text-center">{{ number_format($res->subordinate_average ?? 0, 2) }}</td>
                        <td class="text-center">{{ number_format($res->peer_average ?? 0, 2) }}</td>
                    @endif
                    <td class="text-center font-weight-bold"><strong>{{ number_format($res->final_score ?? 0, 2) }}</strong></td>
                    <td class="text-center"><strong>{{ $catEnum ? $catEnum->label() : ($res->category ?? '-') }}</strong></td>
                </tr>
            @empty
                <tr>
                    <td colspan="{{ $isKabid ? 7 : 6 }}" class="text-center py-3">Belum ada data riwayat hasil penilaian.</td>
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
