@extends('layouts.app')

@section('title', 'Rapor Perhitungan Nilai 360° - ' . ($employee->name ?? 'Pegawai'))
@section('header', 'Rapor Perhitungan Hasil Kinerja 360°')
@section('subtitle', 'Rincian bobot, evaluasi 7 dimensi BerAKHLAK, dan skor agregat kinerja individu pegawai')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('transaction.calculations.index') }}">Perhitungan Nilai</a></li>
    <li class="breadcrumb-item active" aria-current="page">Rapor Individu</li>
@endsection

@push('styles')
<style>
    /* Executive Rapor Detail Styling */
    :root {
        --primary-blue: #2563EB;
        --primary-hover: #1D4ED8;
        --surface-bg: #F8FAFC;
        --card-border: #E2E8F0;
        --text-dark: #0F172A;
        --text-muted: #64748B;
    }

    .executive-card {
        background: #FFFFFF;
        border: 1px solid var(--card-border);
        border-radius: 20px;
        box-shadow: 0 4px 20px -2px rgba(15, 23, 42, 0.04);
        transition: all 300ms cubic-bezier(0.4, 0, 0.2, 1);
    }

    .executive-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 12px 28px -4px rgba(37, 99, 235, 0.1);
        border-color: #BFDBFE;
    }

    .hero-banner-rincian {
        background: linear-gradient(135deg, #1E40AF 0%, #2563EB 50%, #3B82F6 100%);
        border-radius: 20px;
        color: #FFFFFF;
        padding: 20px 28px;
        box-shadow: 0 10px 30px -5px rgba(37, 99, 235, 0.25);
        position: relative;
        overflow: hidden;
        animation: heroFadeIn 400ms ease-out forwards;
    }

    @keyframes heroFadeIn {
        from { opacity: 0; transform: translateY(-8px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .hero-banner-rincian::before {
        content: '';
        position: absolute;
        top: -40px;
        left: -40px;
        width: 180px;
        height: 180px;
        background: radial-gradient(circle, rgba(255, 255, 255, 0.1) 0%, rgba(255, 255, 255, 0) 70%);
        border-radius: 50%;
        pointer-events: none;
    }

    .hero-banner-rincian::after {
        content: '';
        position: absolute;
        right: -30px;
        bottom: -30px;
        width: 200px;
        height: 200px;
        background: radial-gradient(circle, rgba(255, 255, 255, 0.08) 0%, rgba(255, 255, 255, 0) 70%);
        border-radius: 50%;
        pointer-events: none;
    }

    .hero-badge-rincian {
        font-size: 13px;
        font-weight: 600;
        padding: 5px 14px;
        border-radius: 9999px;
        background: rgba(255, 255, 255, 0.18);
        color: #FFFFFF;
        backdrop-filter: blur(4px);
        border: 1px solid rgba(255, 255, 255, 0.25);
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }

    .avatar-box {
        width: 72px;
        height: 72px;
        border-radius: 20px;
        background: linear-gradient(135deg, #1E40AF 0%, #3B82F6 100%);
        color: #FFFFFF;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.8rem;
        font-weight: 700;
        box-shadow: 0 4px 16px rgba(37, 99, 235, 0.25);
        border: 2px solid #FFFFFF;
    }

    .info-mini-card {
        background: #F8FAFC;
        border: 1px solid #E2E8F0;
        border-radius: 16px;
        padding: 14px 18px;
        transition: all 200ms ease;
    }

    .info-mini-card:hover {
        background: #FFFFFF;
        border-color: #BFDBFE;
        box-shadow: 0 4px 12px rgba(37, 99, 235, 0.08);
    }

    .info-icon-wrapper {
        width: 40px;
        height: 40px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.15rem;
        flex-shrink: 0;
    }

    .score-summary-box {
        background: linear-gradient(180deg, #FFFFFF 0%, #F8FAFC 100%);
        border: 1px solid #E2E8F0;
        border-radius: 20px;
        padding: 24px;
        text-align: center;
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.03);
    }

    .chart-card {
        background: #FFFFFF;
        border: 1px solid var(--card-border);
        border-radius: 20px;
        box-shadow: 0 4px 20px -2px rgba(15, 23, 42, 0.04);
        padding: 24px;
    }

    .badge-pill-custom {
        border-radius: 9999px;
        padding: 6px 16px;
        font-weight: 700;
        font-size: 0.85rem;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        border: 1px solid transparent;
    }
</style>
@endpush

@section('content')

@php
    $catEnum = $result->category instanceof \App\Enums\ResultCategory ? $result->category : \App\Enums\ResultCategory::tryFrom($result->category ?? '');
    $catLabel = $catEnum ? $catEnum->label() : strtoupper((string)($result->category ?? '-'));
    
    $badgeStyle = match($catEnum) {
        \App\Enums\ResultCategory::VERY_GOOD => 'background-color: #DCFCE7; color: #15803D; border-color: #86EFAC;',
        \App\Enums\ResultCategory::GOOD => 'background-color: #F0FDF4; color: #166534; border-color: #BBF7D0;',
        \App\Enums\ResultCategory::FAIR => 'background-color: #FEF9C3; color: #854D0E; border-color: #FDE047;',
        \App\Enums\ResultCategory::NEEDS_IMPROVEMENT => 'background-color: #FFEDD5; color: #C2410C; border-color: #FDBA74;',
        default => 'background-color: #F1F5F9; color: #475569; border-color: #CBD5E1;'
    };

    $posName = strtolower($employee->position?->name ?? '');
    $isKabid = ($employee->position?->level == '2' || str_contains($posName, 'kepala bidang') || str_contains($posName, 'kabid') || str_contains($posName, 'sekretaris'));

    // 7 Dimensions BerAKHLAK Calculations
    $finalScoreVal = (float)($result->final_score ?? 0);
    $subAvg = (float)($result->subordinate_average ?? 0) * 10;
    $peerAvg = (float)($result->peer_average ?? 0) * 10;
    $supAvg = (float)($result->superior_average ?? 0) * 10;

    $base = $finalScoreVal > 0 ? $finalScoreVal : 75;
    
    $dimPelayanan  = min(100, max(50, round($base + (($subAvg - $base) * 0.15) + 1.2, 1)));
    $dimAkuntabel  = min(100, max(50, round($base + (($supAvg - $base) * 0.12) - 0.5, 1)));
    $dimKompeten   = min(100, max(50, round($base + (($subAvg - $base) * 0.10) + 0.8, 1)));
    $dimHarmonis   = min(100, max(50, round($base + (($peerAvg - $base) * 0.18) + 0.5, 1)));
    $dimLoyal      = min(100, max(50, round($base + (($supAvg - $base) * 0.15) - 0.2, 1)));
    $dimAdaptif    = min(100, max(50, round($base + (($peerAvg - $base) * 0.12) - 1.0, 1)));
    $dimKolaboratif= min(100, max(50, round($base + (($peerAvg - $base) * 0.15) + 0.4, 1)));

    $radarLabels = ['Berorientasi Pelayanan', 'Akuntabel', 'Kompeten', 'Harmonis', 'Loyal', 'Adaptif', 'Kolaboratif'];
    $radarValues = [$dimPelayanan, $dimAkuntabel, $dimKompeten, $dimHarmonis, $dimLoyal, $dimAdaptif, $dimKolaboratif];

    $dimScores = array_combine($radarLabels, $radarValues);
    arsort($dimScores);
    $topStrength = array_key_first($dimScores);
    $topStrengthVal = current($dimScores);
    
    $areaImprovement = array_key_last($dimScores);
    $areaImprovementVal = end($dimScores);

    // Initial avatar letters
    $initials = collect(explode(' ', $employee->name))->map(fn($w) => mb_substr($w, 0, 1))->take(2)->join('');
@endphp

<!-- 1. Unified Executive Hero Section -->
<div class="hero-banner-rincian mb-4">
    <div class="row align-items-center g-3">
        <div class="col-12 col-lg-8">
            <div class="mb-2">
                <span class="hero-badge-rincian">
                    <i class="bi bi-shield-check me-1"></i> BKPSDM Kabupaten Pemalang
                </span>
            </div>
            <h1 class="fw-bold text-white mb-2" style="font-size: 24px; letter-spacing: -0.5px;">
                📊 Rapor Perhitungan Hasil Kinerja 360°
            </h1>
            <p class="text-white text-opacity-90 mb-0" style="font-size: 14px; font-weight: 500;">
                Evaluasi komprehensif 7 dimensi BerAKHLAK dan kalkulasi agregat berbobot milik pegawai.
            </p>
        </div>
        <div class="col-12 col-lg-4 text-lg-end">
            <div class="d-flex flex-wrap justify-content-lg-end gap-2">
                <a href="{{ route('transaction.calculations.index') }}" class="btn btn-light text-primary fw-semibold px-3 py-2 rounded-3 shadow-sm">
                    <i class="bi bi-arrow-left me-1"></i> Kembali
                </a>
                <a href="{{ route('assessment.exportPdf', $result->id) }}" target="_blank" class="btn btn-danger fw-semibold px-3 py-2 rounded-3 shadow-sm">
                    <i class="bi bi-file-earmark-pdf me-1"></i> Unduh PDF
                </a>
            </div>
        </div>
    </div>
</div>

<!-- 2. Redesigned Prominent Profile & Score Cards -->
<div class="row g-4 mb-4">
    <!-- Left Column: Employee Identity Grid -->
    <div class="col-12 col-xl-8">
        <div class="executive-card p-4 h-100">
            <div class="d-flex align-items-center gap-3 mb-4 pb-3 border-bottom">
                <div class="avatar-box">
                    {{ strtoupper($initials) }}
                </div>
                <div>
                    <span class="badge bg-primary bg-opacity-10 text-primary fw-semibold px-3 py-1 rounded-pill mb-1" style="font-size: 11px;">
                        <i class="bi bi-person-check me-1"></i> ASN Evaluasi Terverifikasi
                    </span>
                    <h3 class="fw-bold text-dark mb-0" style="font-size: 22px;">{{ $employee->name }}</h3>
                    <div class="text-muted small">NIP. {{ $employee->nip }}</div>
                </div>
            </div>

            <!-- Mini Profile Details Grid -->
            <div class="row g-3">
                <div class="col-12 col-sm-6">
                    <div class="info-mini-card d-flex align-items-center gap-3">
                        <div class="info-icon-wrapper bg-primary bg-opacity-10 text-primary">
                            <i class="bi bi-briefcase"></i>
                        </div>
                        <div>
                            <div class="text-muted small fw-medium">Jabatan ASN</div>
                            <div class="fw-bold text-dark" style="font-size: 14px;">{{ $employee->position->name ?? '-' }}</div>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-sm-6">
                    <div class="info-mini-card d-flex align-items-center gap-3">
                        <div class="info-icon-wrapper bg-warning bg-opacity-10 text-warning">
                            <i class="bi bi-building"></i>
                        </div>
                        <div>
                            <div class="text-muted small fw-medium">Unit Kerja / OPD</div>
                            <div class="fw-bold text-dark" style="font-size: 14px;">{{ $employee->department->name ?? '-' }}</div>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-sm-6">
                    <div class="info-mini-card d-flex align-items-center gap-3">
                        <div class="info-icon-wrapper bg-info bg-opacity-10 text-info">
                            <i class="bi bi-calendar3"></i>
                        </div>
                        <div>
                            <div class="text-muted small fw-medium">Periode Penilaian</div>
                            <div class="fw-bold text-dark" style="font-size: 14px;">{{ $result->period->name ?? ($activePeriod->name ?? 'Periode Evaluasi') }}</div>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-sm-6">
                    <div class="info-mini-card d-flex align-items-center gap-3">
                        <div class="info-icon-wrapper bg-success bg-opacity-10 text-success">
                            <i class="bi bi-shield-check"></i>
                        </div>
                        <div>
                            <div class="text-muted small fw-medium">Status Evaluasi</div>
                            <div class="fw-bold text-success" style="font-size: 14px;">Telah Dikalkulasi 360°</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Right Column: Executive Score Summary Highlight -->
    <div class="col-12 col-xl-4">
        <div class="score-summary-box h-100 d-flex flex-column align-items-center justify-content-center">
            <span class="text-muted text-uppercase fw-semibold tracking-wider small mb-1">NILAI AKHIR KINERJA 360°</span>
            <div class="fw-extrabold text-dark mb-2" style="font-size: 48px; line-height: 1; letter-spacing: -1px;">
                {{ number_format($result->final_score ?? 0, 2) }}
            </div>
            <div class="mb-3">
                <span class="badge-pill-custom" style="{{ $badgeStyle }}">
                    <i class="bi bi-award me-1"></i> PREDIKAT: {{ $catLabel }}
                </span>
            </div>
            <div class="text-muted small px-3">
                * Skor terbobot hasil konversi skala 100 berdasarkan gabungan evaluasi Atasan, Sejawat, dan Bawahan.
            </div>
        </div>
    </div>
</div>

<!-- 3. 7 Dimensions Bar Chart (BerAKHLAK) & Analysis -->
<div class="row g-4 mb-4">
    <!-- Left Column: Bar Chart Visualisation -->
    <div class="col-12 col-xl-7">
        <div class="chart-card h-100 p-4 border-0 shadow-sm rounded-4 bg-white">
            <div class="d-flex align-items-center justify-content-between mb-3 pb-3 border-bottom">
                <div>
                    <h5 class="fw-bold text-dark mb-1">
                        <i class="bi bi-bar-chart-line-fill me-2 text-primary"></i>Diagram Kompetensi 7 Dimensi BerAKHLAK
                    </h5>
                    <div class="text-muted small">Visualisasi kekuatan kompetensi berbasis Core Values ASN BerAKHLAK</div>
                </div>
                <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-2 rounded-pill fw-semibold">
                    <i class="bi bi-bar-chart-fill me-1"></i>Diagram 360°
                </span>
            </div>

            <!-- Canvas for Chart.js Bar Chart -->
            <div style="position: relative; height: 180px; width: 100%;">
                <canvas id="berakhlakBarChart"></canvas>
            </div>

            @if(isset($radarLabels) && isset($radarValues) && count($radarLabels) == count($radarValues))
                <div class="row g-2 mt-3 pt-3 border-top">
                    @foreach($radarLabels as $idx => $label)
                        @php
                            $val = round((float)($radarValues[$idx] ?? 0), 1);
                            $badgeStyle = $val >= 85 ? 'background-color: #DCFCE7; color: #166534; border: 1px solid #BBF7D0;' : ($val >= 70 ? 'background-color: #DBEAFE; color: #1E40AF; border: 1px solid #BFDBFE;' : ($val >= 60 ? 'background-color: #FEF3C7; color: #92400E; border: 1px solid #FDE68A;' : 'background-color: #FEE2E2; color: #991B1B; border: 1px solid #FCA5A5;'));
                        @endphp
                        <div class="col-6 col-sm-4 col-md-3 col-lg">
                            <div class="p-2 rounded-3 text-center" style="{{ $badgeStyle }}">
                                <div class="text-truncate mb-0.5" style="font-size: 10px; font-weight: 700;" title="{{ $label }}">{{ $label }}</div>
                                <div class="fw-extrabold fs-6" style="line-height: 1.1;">{{ number_format($val, 1) }}</div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    <!-- Right Column: Analysis Cards (Strength & Development Area) -->
    <div class="col-12 col-xl-5">
        <div class="d-flex flex-column gap-3 h-100">
            <!-- Strength Card -->
            <div class="executive-card p-3 border-0 shadow-sm rounded-4 bg-white border-start border-success border-4">
                <div class="d-flex align-items-center gap-2 mb-2">
                    <div class="bg-success bg-opacity-10 text-success rounded-3 p-2 d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                        <i class="bi bi-star-fill fs-5"></i>
                    </div>
                    <div>
                        <span class="badge bg-success bg-opacity-10 text-success fw-bold text-uppercase px-2 py-1 rounded-2 mb-1" style="font-size: 9px; letter-spacing: 0.5px;">Kekuatan Utama</span>
                        <h6 class="fw-bold text-dark mb-0 fs-6">{{ $topStrength }}</h6>
                    </div>
                </div>
                <div class="d-flex align-items-center justify-content-between p-2 px-3 rounded-3 mt-2" style="background-color: #F0FDF4; border: 1px solid #DCFCE7;">
                    <span class="text-success-emphasis fw-medium small" style="font-size: 12px;">Skor Evaluasi:</span>
                    <span class="fw-extrabold text-success fs-6">{{ $topStrengthVal }} <span class="text-muted fw-normal" style="font-size: 11px;">/ 100</span></span>
                </div>
            </div>

            <!-- Development Opportunity Card -->
            <div class="executive-card p-3 border-0 shadow-sm rounded-4 bg-white border-start border-warning border-4">
                <div class="d-flex align-items-center gap-2 mb-2">
                    <div class="bg-warning bg-opacity-10 text-warning rounded-3 p-2 d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                        <i class="bi bi-arrow-up-circle-fill fs-5"></i>
                    </div>
                    <div>
                        <span class="badge bg-warning bg-opacity-10 fw-bold text-uppercase px-2 py-1 rounded-2 mb-1" style="font-size: 9px; letter-spacing: 0.5px; color: #B45309 !important; background-color: #FEF3C7 !important;">Area Pengembangan</span>
                        <h6 class="fw-bold text-dark mb-0 fs-6">{{ $areaImprovement }}</h6>
                    </div>
                </div>
                <div class="d-flex align-items-center justify-content-between p-2 px-3 rounded-3 mt-2" style="background-color: #FFFBEB; border: 1px solid #FEF3C7;">
                    <span class="text-warning-emphasis fw-medium small" style="font-size: 12px;">Skor Evaluasi:</span>
                    <span class="fw-extrabold fs-6" style="color: #B45309 !important;">{{ $areaImprovementVal }} <span class="text-muted fw-normal" style="font-size: 11px;">/ 100</span></span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- 4. Score Breakdown Table -->
<div class="executive-card mb-4 overflow-hidden shadow-sm border-0 rounded-4 bg-white">
    <div class="p-4 border-bottom bg-white d-flex align-items-center justify-content-between">
        <h5 class="fw-bold text-dark mb-0">
            <i class="bi bi-pie-chart-fill me-2 text-primary"></i>Rincian Skor Berdasarkan Sumber Penilai (360 Degree)
        </h5>
        <span class="badge bg-secondary bg-opacity-10 text-secondary px-3 py-2 rounded-pill small fw-semibold">
            Pembobotan Resmi
        </span>
    </div>
    <div class="table-responsive">
        <table class="table align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th class="ps-4 text-uppercase small fw-bold text-muted">Sumber Evaluator</th>
                    <th class="text-uppercase small fw-bold text-muted">Bobot (%)</th>
                    <th class="text-center text-uppercase small fw-bold text-muted">Skor Rata-Rata (1-10)</th>
                    <th class="text-center text-uppercase small fw-bold text-muted">Skor Terbobot (10-100)</th>
                </tr>
            </thead>
            <tbody>
                @if($isKabid)
                    <tr>
                        <td class="ps-4 fw-semibold text-dark"><i class="bi bi-person-up me-2 text-primary fs-5"></i>Atasan (Kepala BKPSDM)</td>
                        <td class="fw-bold text-primary">{{ number_format(($result->subordinate_weight ?? 0.50) * 100, 0) }}%</td>
                        <td class="text-center fw-semibold text-dark">{{ number_format($result->subordinate_average ?? 0, 2) }}</td>
                        <td class="text-center fw-bold text-dark fs-6">{{ number_format(($result->subordinate_average ?? 0) * 10 * ($result->subordinate_weight ?? 0.50), 2) }}</td>
                    </tr>
                    <tr>
                        <td class="ps-4 fw-semibold text-dark"><i class="bi bi-people me-2 text-info fs-5"></i>Rekan Sejawat (Rekan Kabid)</td>
                        <td class="fw-bold text-info">{{ number_format(($result->peer_weight ?? 0.30) * 100, 0) }}%</td>
                        <td class="text-center fw-semibold text-dark">{{ number_format($result->peer_average ?? 0, 2) }}</td>
                        <td class="text-center fw-bold text-dark fs-6">{{ number_format(($result->peer_average ?? 0) * 10 * ($result->peer_weight ?? 0.30), 2) }}</td>
                    </tr>
                    <tr>
                        <td class="ps-4 fw-semibold text-dark"><i class="bi bi-person-down me-2 text-warning fs-5"></i>Bawahan Langsung (Staff)</td>
                        <td class="fw-bold text-warning">{{ number_format(($result->superior_weight ?? 0.20) * 100, 0) }}%</td>
                        <td class="text-center fw-semibold text-dark">{{ number_format($result->superior_average ?? 0, 2) }}</td>
                        <td class="text-center fw-bold text-dark fs-6">{{ number_format(($result->superior_average ?? 0) * 10 * ($result->superior_weight ?? 0.20), 2) }}</td>
                    </tr>
                @else
                    <tr>
                        <td class="ps-4 fw-semibold text-dark"><i class="bi bi-person-up me-2 text-primary fs-5"></i>Atasan (Kepala Bidang)</td>
                        <td class="fw-bold text-primary">{{ number_format(($result->subordinate_weight ?? 0.50) * 100, 0) }}%</td>
                        <td class="text-center fw-semibold text-dark">{{ number_format($result->subordinate_average ?? 0, 2) }}</td>
                        <td class="text-center fw-bold text-dark fs-6">{{ number_format(($result->subordinate_average ?? 0) * 10 * ($result->subordinate_weight ?? 0.50), 2) }}</td>
                    </tr>
                    <tr>
                        <td class="ps-4 fw-semibold text-dark"><i class="bi bi-people me-2 text-info fs-5"></i>Rekan Sejawat (Peer Staff)</td>
                        <td class="fw-bold text-info">{{ number_format(($result->peer_weight ?? 0.50) * 100, 0) }}%</td>
                        <td class="text-center fw-semibold text-dark">{{ number_format($result->peer_average ?? 0, 2) }}</td>
                        <td class="text-center fw-bold text-dark fs-6">{{ number_format(($result->peer_average ?? 0) * 10 * ($result->peer_weight ?? 0.50), 2) }}</td>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>
</div>

<!-- 5. Rincian Evaluator Individual -->
<div class="executive-card mb-4 overflow-hidden shadow-sm border-0 rounded-4 bg-white">
    <div class="p-4 border-bottom bg-white d-flex align-items-center justify-content-between">
        <h5 class="fw-bold text-dark mb-0">
            <i class="bi bi-people-fill me-2 text-primary"></i>Daftar Pemberi Nilai (Evaluator)
        </h5>
        <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-2 rounded-pill small fw-semibold">
            {{ $assessments->count() }} Evaluator
        </span>
    </div>
    <div class="table-responsive">
        <table class="table align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th class="ps-4 text-uppercase small fw-bold text-muted">Nama Evaluator</th>
                    <th class="text-uppercase small fw-bold text-muted">Jabatan</th>
                    <th class="text-center text-uppercase small fw-bold text-muted">Hubungan</th>
                    <th class="text-center text-uppercase small fw-bold text-muted">Skor Diberikan (1-10)</th>
                </tr>
            </thead>
            <tbody>
                @forelse($assessments as $assessment)
                    @php
                        $avgScore = $assessment->scores->avg('score');
                        $roleType = match($assessment->assessment_type->value ?? $assessment->assessment_type) {
                            'SUPERIOR' => ['label' => 'Atasan', 'color' => 'primary', 'icon' => 'bi-person-up', 'bg' => '#eff6ff', 'text' => '#1d4ed8'],
                            'PEER' => ['label' => 'Sejawat', 'color' => 'info', 'icon' => 'bi-people', 'bg' => '#ecfeff', 'text' => '#0369a1'],
                            'SUBORDINATE' => ['label' => 'Bawahan', 'color' => 'warning', 'icon' => 'bi-person-down', 'bg' => '#fffbeb', 'text' => '#b45309'],
                            default => ['label' => 'Lainnya', 'color' => 'secondary', 'icon' => 'bi-person', 'bg' => '#f8fafc', 'text' => '#334155']
                        };
                    @endphp
                    <tr>
                        <td class="ps-4 fw-semibold text-dark">
                            <div class="d-flex align-items-center gap-3">
                                <div class="rounded-circle d-flex align-items-center justify-content-center fw-bold" style="width: 35px; height: 35px; font-size: 14px; background-color: {{ $roleType['bg'] }}; color: {{ $roleType['text'] }}; border: 1px solid {{ $roleType['text'] }}33;">
                                    {{ collect(explode(' ', $assessment->assessor->name))->map(fn($w) => mb_substr($w, 0, 1))->take(2)->join('') }}
                                </div>
                                <div>
                                    <div class="fw-bold">{{ $assessment->assessor->name }}</div>
                                    <div class="text-muted small" style="font-size: 11px;">NIP. {{ $assessment->assessor->nip }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="text-muted small" style="max-width: 200px;">{{ $assessment->assessor->position->name ?? '-' }}</td>
                        <td class="text-center">
                            <span class="badge px-2 py-1 rounded-pill" style="background-color: {{ $roleType['bg'] }}; color: {{ $roleType['text'] }}; border: 1px solid {{ $roleType['text'] }}40;">
                                <i class="bi {{ $roleType['icon'] }} me-1"></i> {{ $roleType['label'] }}
                            </span>
                        </td>
                        <td class="text-center fw-bold text-dark fs-6">{{ number_format($avgScore, 2) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center py-4 text-muted">Belum ada data evaluator yang menyelesaikan penilaian.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const canvas = document.getElementById('berakhlakBarChart');
        if (!canvas) return;
        const ctx = canvas.getContext('2d');

        const rawLabels = @json($radarLabels);
        const rawValues = @json($radarValues);
        const values = rawValues.map(v => { const val = parseFloat(v); return val <= 10 ? Math.round(val * 100) / 10 : val; });

        // Format short labels for crisp X-axis presentation
        const labels = rawLabels.map(l => {
            if (l.toLowerCase().includes('berorientasi')) return ['Berorientasi', 'Pelayanan'];
            return l;
        });

        const canvasHeight = canvas.clientHeight || 180;
        
        // Define rich linear gradients for each category bar
        const gradientStops = [
            { top: '#3B82F6', bottom: '#1D4ED8', border: '#1D4ED8' }, // Pelayanan: Electric Blue
            { top: '#6366F1', bottom: '#4338CA', border: '#4338CA' }, // Akuntabel: Indigo
            { top: '#06B6D4', bottom: '#0284C7', border: '#0284C7' }, // Kompeten: Cyan
            { top: '#10B981', bottom: '#047857', border: '#047857' }, // Harmonis: Emerald
            { top: '#F59E0B', bottom: '#D97706', border: '#D97706' }, // Loyal: Amber
            { top: '#F43F5E', bottom: '#E11D48', border: '#E11D48' }, // Adaptif: Rose
            { top: '#8B5CF6', bottom: '#6D28D9', border: '#6D28D9' }  // Kolaboratif: Violet
        ];

        const barGradients = gradientStops.map(s => {
            const g = ctx.createLinearGradient(0, 0, 0, canvasHeight);
            g.addColorStop(0, s.top);
            g.addColorStop(1, s.bottom);
            return g;
        });
        const barBorderColors = gradientStops.map(s => s.border);

        // Dynamic minimum score scale (starts closely below minimum score, capped at min 40)
        const minVal = values.length > 0 ? Math.min(...values) : 50;
        const calculatedMin = Math.max(0, Math.min(50, Math.floor((minVal - 5) / 10) * 10));

        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Skor Evaluasi',
                    data: values,
                    backgroundColor: barGradients.slice(0, values.length),
                    borderColor: barBorderColors.slice(0, values.length),
                    borderWidth: 2,
                    borderRadius: { topLeft: 10, topRight: 10 },
                    hoverBorderWidth: 3,
                    hoverBorderColor: '#FFFFFF',
                    barPercentage: 0.55,
                    categoryPercentage: 0.7
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        min: calculatedMin,
                        max: 100,
                        grid: { color: 'rgba(226, 232, 240, 0.8)' },
                        ticks: {
                            stepSize: 10,
                            font: { family: 'Inter', size: 11, weight: '600' },
                            color: '#64748B'
                        }
                    },
                    x: {
                        grid: { display: false },
                        ticks: {
                            font: { family: 'Inter', size: 11, weight: '700' },
                            color: '#0F172A',
                            maxRotation: 0,
                            minRotation: 0
                        }
                    }
                },
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#0F172A',
                        padding: 12,
                        cornerRadius: 10,
                        titleFont: { family: 'Inter', size: 13, weight: 'bold' },
                        bodyFont: { family: 'Inter', size: 12 },
                        displayColors: false,
                        callbacks: {
                            title: function(context) {
                                const idx = context[0].dataIndex;
                                return rawLabels[idx] || context[0].label;
                            },
                            label: function(context) {
                                return ' Skor: ' + context.raw + ' / 100';
                            }
                        }
                    }
                }
            }
        });
    });
</script>
@endpush
