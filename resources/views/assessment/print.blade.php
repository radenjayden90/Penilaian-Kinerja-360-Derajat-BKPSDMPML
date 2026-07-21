<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rapor Penilaian Kinerja 360° Individu - {{ $result->employee->name ?? '-' }}</title>
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
            <strong>Ekspor PDF - Rapor Penilaian Kinerja 360°</strong>
            <div class="text-muted small">Tekan tombol cetak di sebelah kanan untuk menyimpan rapor sebagai PDF atau mencetak dokumen ini.</div>
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
        <h5 class="fw-bold mb-1">RAPOR INDIVIDU HASIL PENILAIAN KINERJA 360 DERAJAT</h5>
        <div class="small fw-semibold">PERIODE: {{ strtoupper($result->period->name ?? '-') }}</div>
    </div>

    <!-- Employee Profile Info -->
    <div class="row mb-4">
        <div class="col-8">
            <table class="info-table w-100">
                <tr>
                    <td style="width: 130px;">Nama Pegawai</td>
                    <td style="width: 15px;">:</td>
                    <td><strong>{{ $result->employee->name ?? '-' }}</strong></td>
                </tr>
                <tr>
                    <td>NIP</td>
                    <td>:</td>
                    <td>{{ $result->employee->nip ?? '-' }}</td>
                </tr>
                <tr>
                    <td>Jabatan</td>
                    <td>:</td>
                    <td>{{ $result->employee->position->name ?? '-' }}</td>
                </tr>
                <tr>
                    <td>Unit Kerja / Bidang</td>
                    <td>:</td>
                    <td>{{ $result->employee->department->name ?? '-' }}</td>
                </tr>
            </table>
        </div>
    </div>

    <!-- Score Table -->
    @php
        $posName = strtolower($result->employee->position?->name ?? '');
        $isKabid = ($result->employee->position?->level == '2' || str_contains($posName, 'kepala bidang') || str_contains($posName, 'kabid') || str_contains($posName, 'sekretaris'));
        
        $catEnum = $result->category instanceof \App\Enums\ResultCategory ? $result->category : \App\Enums\ResultCategory::tryFrom($result->category);
    @endphp

    <table class="table-print">
        <thead>
            <tr>
                <th style="width: 50px;">NO</th>
                <th>KOMPONEN PENILAIAN</th>
                <th style="width: 120px;">BOBOT</th>
                <th style="width: 150px;">SKOR RATA-RATA (1-10)</th>
                <th style="width: 150px;">SKOR TERBOBOT (10-100)</th>
            </tr>
        </thead>
        <tbody>
            @if($isKabid)
                <tr>
                    <td class="text-center">1</td>
                    <td>Penilaian Atasan (Kepala BKPSDM)</td>
                    <td class="text-center">50%</td>
                    <td class="text-center">{{ number_format($result->subordinate_average ?? 0, 2) }}</td>
                    <td class="text-center">{{ number_format(($result->subordinate_average ?? 0) * 10 * 0.50, 2) }}</td>
                </tr>
                <tr>
                    <td class="text-center">2</td>
                    <td>Penilaian Sejawat (Rekan Kepala Bidang)</td>
                    <td class="text-center">30%</td>
                    <td class="text-center">{{ number_format($result->peer_average ?? 0, 2) }}</td>
                    <td class="text-center">{{ number_format(($result->peer_average ?? 0) * 10 * 0.30, 2) }}</td>
                </tr>
                <tr>
                    <td class="text-center">3</td>
                    <td>Penilaian Bawahan (Staf Divisi)</td>
                    <td class="text-center">20%</td>
                    <td class="text-center">{{ number_format($result->superior_average ?? 0, 2) }}</td>
                    <td class="text-center">{{ number_format(($result->superior_average ?? 0) * 10 * 0.20, 2) }}</td>
                </tr>
            @else
                <tr>
                    <td class="text-center">1</td>
                    <td>Penilaian Atasan (Kepala Bidang)</td>
                    <td class="text-center">50%</td>
                    <td class="text-center">{{ number_format($result->subordinate_average ?? 0, 2) }}</td>
                    <td class="text-center">{{ number_format(($result->subordinate_average ?? 0) * 10 * 0.50, 2) }}</td>
                </tr>
                <tr>
                    <td class="text-center">2</td>
                    <td>Penilaian Sejawat (Rekan Staff)</td>
                    <td class="text-center">50%</td>
                    <td class="text-center">{{ number_format($result->peer_average ?? 0, 2) }}</td>
                    <td class="text-center">{{ number_format(($result->peer_average ?? 0) * 10 * 0.50, 2) }}</td>
                </tr>
            @endif
            <tr style="background-color: #f9f9f9;">
                <td colspan="4" class="text-end"><strong>NILAI AKHIR KINERJA 360°</strong></td>
                <td class="text-center"><strong>{{ number_format($result->final_score ?? 0, 2) }}</strong></td>
            </tr>
            <tr style="background-color: #f9f9f9;">
                <td colspan="4" class="text-end"><strong>PREDIKAT KATEGORI</strong></td>
                <td class="text-center"><strong>{{ $catEnum ? strtoupper($catEnum->label()) : strtoupper($result->category) }}</strong></td>
            </tr>
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
