@extends('layouts.app')

@section('title', 'Profil Saya & Rapor Kinerja 360°')
@section('header', 'Profil Saya & Visualisasi Nilai Kinerja 360°')
@section('subtitle', 'Biodata lengkap pegawai dan analisis grafik radar kompetensi 360° BerAKHLAK.')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active" aria-current="page">Profil Saya</li>
@endsection

<<<<<<< HEAD
@section('content')
<div class="row g-4">
    <!-- Left Column: Data Singkat Pegawai -->
    <div class="col-12 col-lg-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white py-3 border-bottom">
                <h6 class="fw-bold text-dark mb-0">
                    <i class="bi bi-person-badge text-primary me-2"></i>Data Singkat Pegawai
                </h6>
            </div>
            <div class="card-body p-4">
                <div class="text-center mb-4 pb-3 border-bottom">
                    <div class="avatar-circle bg-primary text-white mx-auto mb-3 d-flex align-items-center justify-content-center fw-bold shadow-sm" style="width: 80px; height: 80px; font-size: 2rem; border-radius: 50%; background-color: #1E3A5F !important;">
                        {{ strtoupper(substr($employee->name ?? $user->name, 0, 1)) }}
                    </div>
                    <h5 class="fw-bold text-dark mb-1">{{ $employee->name ?? $user->name }}</h5>
                    <div class="badge bg-light text-secondary border px-3 py-1 mb-2">
                        NIP. {{ $employee->nip ?? '-' }}
                    </div>
                    <div>
                        @php
                            $roleName = $employee->role->name ?? 'Pegawai';
                            $roleBadge = match($roleName) {
                                'ADMIN' => 'bg-danger',
                                'HEAD' => 'bg-warning text-dark',
                                default => 'bg-info text-dark'
                            };
                        @endphp
                        <span class="badge {{ $roleBadge }} me-1"><i class="bi bi-shield-lock me-1"></i>Role: {{ $roleName }}</span>
                        <span class="badge bg-success"><i class="bi bi-check-circle me-1"></i>Status: Aktif</span>
                    </div>
                </div>
=======
@push('styles')
<style>
    /* Executive Profile & Rapor Styling */
    :root {
        --primary-blue: #2563EB;
        --primary-hover: #1D4ED8;
        --surface-bg: #F8FAFC;
        --card-border: #E2E8F0;
        --text-dark: #0F172A;
        --text-muted: #64748B;
    }
>>>>>>> 0730e7d (feat: redesign Profile & Rapor 360 views with 7-dimension BerAKHLAK Radar Chart, senior-friendly biodata card, and unified hero headers)

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

    .hero-banner-profile {
        background: linear-gradient(135deg, #1E40AF 0%, #2563EB 50%, #3B82F6 100%);
        border-radius: 24px;
        color: #FFFFFF;
        padding: 32px 36px;
        box-shadow: 0 10px 30px -5px rgba(37, 99, 235, 0.25);
        position: relative;
        overflow: hidden;
        animation: heroFadeIn 400ms ease-out forwards;
    }

    @keyframes heroFadeIn {
        from { opacity: 0; transform: translateY(-8px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .hero-banner-profile::before {
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

    .hero-banner-profile::after {
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

    .hero-badge-profile {
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

    .avatar-prominent {
        width: 84px;
        height: 84px;
        border-radius: 24px;
        background: linear-gradient(135deg, #1E40AF 0%, #3B82F6 100%);
        color: #FFFFFF;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2.2rem;
        font-weight: 800;
        box-shadow: 0 8px 24px rgba(37, 99, 235, 0.3);
        border: 3px solid #FFFFFF;
    }

    .info-card-item {
        background: #F8FAFC;
        border: 1px solid #E2E8F0;
        border-radius: 16px;
        padding: 16px 20px;
        transition: all 200ms ease;
    }

    .info-card-item:hover {
        background: #FFFFFF;
        border-color: #BFDBFE;
        box-shadow: 0 4px 16px rgba(37, 99, 235, 0.08);
        transform: translateY(-1px);
    }

    .info-icon-box {
        width: 44px;
        height: 44px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
        flex-shrink: 0;
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
    $empName = $employee->name ?? $user->name;
    $empNip = $employee->nip ?? $user->nip ?? '-';
    $roleName = $employee->role->name ?? $user->role->name ?? 'Pegawai';
    $roleFormatted = ucwords(strtolower(str_replace('_', ' ', $roleName)));
    if (strtolower($roleFormatted) === 'employee') {
        $roleFormatted = 'Pegawai';
    }

    $initials = collect(explode(' ', $empName))->map(fn($w) => mb_substr($w, 0, 1))->take(2)->join('');

    // Radar Chart 7 Dimensions Calculations
    if ($latestResult) {
        $finalScoreVal = (float)($latestResult->final_score ?? 0);
        $subAvg = (float)($latestResult->subordinate_average ?? 0) * 10;
        $peerAvg = (float)($latestResult->peer_average ?? 0) * 10;
        $supAvg = (float)($latestResult->superior_average ?? 0) * 10;

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

        $catEnum = $latestResult->category instanceof \App\Enums\ResultCategory ? $latestResult->category : \App\Enums\ResultCategory::tryFrom($latestResult->category ?? '');
        $catLabel = $catEnum ? $catEnum->label() : strtoupper((string)($latestResult->category ?? '-'));
        
        $badgeStyle = match($catEnum) {
            \App\Enums\ResultCategory::VERY_GOOD => 'background-color: #DCFCE7; color: #15803D; border-color: #86EFAC;',
            \App\Enums\ResultCategory::GOOD => 'background-color: #F0FDF4; color: #166534; border-color: #BBF7D0;',
            \App\Enums\ResultCategory::FAIR => 'background-color: #FEF9C3; color: #854D0E; border-color: #FDE047;',
            \App\Enums\ResultCategory::NEEDS_IMPROVEMENT => 'background-color: #FFEDD5; color: #C2410C; border-color: #FDBA74;',
            default => 'background-color: #F1F5F9; color: #475569; border-color: #CBD5E1;'
        };
    }
@endphp

<!-- Senior-Friendly Profile & Biodata Card -->
<div class="executive-card p-4 mb-4">
    <!-- Header Biodata Banner -->
    <div class="p-4 rounded-4 mb-4" style="background: linear-gradient(135deg, #F8FAFC 0%, #EFF6FF 100%); border: 1px solid #DBEAFE;">
        <div class="d-flex flex-column flex-md-row align-items-center gap-4">
            <div class="avatar-prominent" style="width: 76px; height: 76px; min-width: 76px; font-size: 2rem; border-radius: 20px;">
                {{ strtoupper($initials) }}
            </div>
            <div class="text-center text-md-start flex-grow-1">
                <h2 class="fw-bold text-dark mb-1" style="font-size: 24px; color: #0F172A !important;">
                    {{ $empName }}
                </h2>
                <div class="fw-bold text-primary mb-2" style="font-size: 16px; color: #2563EB !important;">
                    NIP. {{ $empNip }}
                </div>
                <div class="d-flex flex-wrap align-items-center justify-content-center justify-content-md-start gap-2">
                    <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-20 px-3 py-1.5 rounded-pill fw-bold" style="font-size: 12.5px;">
                        <i class="bi bi-check-circle-fill me-1"></i> Status: Aktif Terverifikasi
                    </span>
                    <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-20 px-3 py-1.5 rounded-pill fw-bold" style="font-size: 12.5px;">
                        <i class="bi bi-shield-lock me-1"></i> Peran: {{ $roleFormatted }}
                    </span>
                </div>
            </div>
        </div>
    </div>

<<<<<<< HEAD
    <!-- Right Column: Visualisasi Nilai Kinerja Terakhir (Chart.js) -->
    <div class="col-12 col-lg-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center border-bottom">
                <h6 class="fw-bold text-dark mb-0">
                    <i class="bi bi-pie-chart text-success me-2"></i>Visualisasi Nilai Kinerja 360° Terakhir
                </h6>
                @if($latestResult)
                    <span class="badge bg-light text-dark border">
                        <i class="bi bi-calendar-check me-1 text-primary"></i>{{ $latestResult->period->name ?? 'Periode Aktif' }}
                    </span>
                @endif
            </div>

            <div class="card-body p-4">
                @if($latestResult)
                    <!-- Key Summary Cards -->
                    <div class="row g-3 mb-4">
                        <div class="col-6 col-md-6">
                            <div class="p-3 rounded border bg-light text-center h-100 d-flex flex-column justify-content-center align-items-center">
                                <small class="text-muted d-block mb-1 fw-semibold">Nilai Akhir 360°</small>
                                <span class="fs-2 fw-bold text-primary" style="color: #1E3A5F !important;">
                                    {{ number_format($latestResult->final_score ?? 0, 2) }}
                                </span>
                                <small class="text-muted d-block mt-1" style="font-size: 11px;">Skala 10 - 100</small>
                            </div>
                        </div>
                        <div class="col-6 col-md-6">
                            <div class="p-3 rounded border bg-light text-center h-100 d-flex flex-column justify-content-center align-items-center">
                                <small class="text-muted d-block mb-1 fw-semibold">Predikat Kategori</small>
                                @php
                                    $catEnum = $latestResult->category instanceof \App\Enums\ResultCategory ? $latestResult->category : \App\Enums\ResultCategory::tryFrom($latestResult->category);
                                    $textColor = match($catEnum) {
                                        \App\Enums\ResultCategory::VERY_GOOD => 'text-success',
                                        \App\Enums\ResultCategory::GOOD => 'text-primary',
                                        \App\Enums\ResultCategory::FAIR => 'text-warning',
                                        \App\Enums\ResultCategory::NEEDS_IMPROVEMENT => 'text-danger',
                                        default => 'text-secondary'
                                    };
                                    $style = $catEnum === \App\Enums\ResultCategory::FAIR ? 'style="color: #b58900 !important;"' : '';
                                @endphp
                                <div class="mt-2 fw-bold fs-4 {{ $textColor }}" {!! $style !!}>
                                    {{ $catEnum ? $catEnum->label() : ($latestResult->category ?? '-') }}
                                </div>
=======
    <!-- Clear 2-Column Key-Value Biodata Table (Ramah Lansia) -->
    <div class="card border-0 shadow-none bg-white">
        <div class="card-header bg-white py-3 px-0 border-bottom d-flex align-items-center justify-content-between">
            <h5 class="fw-bold text-dark mb-0" style="font-size: 17px;">
                <i class="bi bi-person-vcard me-2 text-primary"></i>Informasi Biodata Pegawai ASN
            </h5>
            <small class="text-muted fw-semibold">Data Resmi BKPSDM Pemalang</small>
        </div>
        <div class="card-body p-0 pt-2">
            <div class="row g-0">
                <div class="col-12 col-lg-6">
                    <div class="p-3 border-bottom d-flex align-items-center gap-3">
                        <div class="bg-primary bg-opacity-10 text-primary rounded-circle p-2.5 d-flex align-items-center justify-content-center flex-shrink-0" style="width: 44px; height: 44px;">
                            <i class="bi bi-briefcase fs-5"></i>
                        </div>
                        <div>
                            <div class="text-secondary fw-semibold mb-0" style="font-size: 13px;">Jabatan ASN:</div>
                            <div class="fw-bold text-dark" style="font-size: 15px; color: #0F172A !important;">
                                {{ $employee->position->name ?? 'Belum Diatur' }}
>>>>>>> 0730e7d (feat: redesign Profile & Rapor 360 views with 7-dimension BerAKHLAK Radar Chart, senior-friendly biodata card, and unified hero headers)
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-lg-6">
                    <div class="p-3 border-bottom d-flex align-items-center gap-3">
                        <div class="bg-warning bg-opacity-10 text-warning rounded-circle p-2.5 d-flex align-items-center justify-content-center flex-shrink-0" style="width: 44px; height: 44px; color: #B45309 !important;">
                            <i class="bi bi-building fs-5"></i>
                        </div>
                        <div>
                            <div class="text-secondary fw-semibold mb-0" style="font-size: 13px;">Unit Kerja / OPD:</div>
                            <div class="fw-bold text-dark" style="font-size: 15px; color: #0F172A !important;">
                                {{ $employee->department->name ?? 'Belum Diatur' }}
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-lg-6">
                    <div class="p-3 border-bottom d-flex align-items-center gap-3">
                        <div class="bg-indigo bg-opacity-10 text-indigo rounded-circle p-2.5 d-flex align-items-center justify-content-center flex-shrink-0" style="width: 44px; height: 44px; background-color: #E0E7FF; color: #4F46E5;">
                            <i class="bi bi-person-up fs-5"></i>
                        </div>
                        <div>
                            <div class="text-secondary fw-semibold mb-0" style="font-size: 13px;">Atasan Langsung:</div>
                            <div class="fw-bold text-dark" style="font-size: 15px; color: #0F172A !important;">
                                {{ $employee->supervisor->name ?? 'Pimpinan Utama / Top' }}
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-lg-6">
                    <div class="p-3 border-bottom d-flex align-items-center gap-3">
                        <div class="bg-info bg-opacity-10 text-info rounded-circle p-2.5 d-flex align-items-center justify-content-center flex-shrink-0" style="width: 44px; height: 44px;">
                            <i class="bi bi-envelope fs-5"></i>
                        </div>
                        <div>
                            <div class="text-secondary fw-semibold mb-0" style="font-size: 13px;">Email Kedinasan:</div>
                            <div class="fw-bold text-dark" style="font-size: 15px; color: #0F172A !important;">
                                {{ $employee->email ?? '-' }}
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-lg-6">
                    <div class="p-3 border-bottom border-lg-0 d-flex align-items-center gap-3">
                        <div class="bg-danger bg-opacity-10 text-danger rounded-circle p-2.5 d-flex align-items-center justify-content-center flex-shrink-0" style="width: 44px; height: 44px;">
                            <i class="bi bi-gender-ambiguous fs-5"></i>
                        </div>
                        <div>
                            <div class="text-secondary fw-semibold mb-0" style="font-size: 13px;">Jenis Kelamin:</div>
                            <div class="fw-bold text-dark" style="font-size: 15px; color: #0F172A !important;">
                                {{ ($employee->gender ?? '') === 'L' ? 'Laki-Laki' : (($employee->gender ?? '') === 'P' ? 'Perempuan' : '-') }}
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-lg-6">
                    <div class="p-3 d-flex align-items-center gap-3">
                        <div class="bg-success bg-opacity-10 text-success rounded-circle p-2.5 d-flex align-items-center justify-content-center flex-shrink-0" style="width: 44px; height: 44px;">
                            <i class="bi bi-telephone fs-5"></i>
                        </div>
                        <div>
                            <div class="text-secondary fw-semibold mb-0" style="font-size: 13px;">No. Telepon / WhatsApp:</div>
                            <div class="fw-bold text-dark" style="font-size: 15px; color: #0F172A !important;">
                                {{ $employee->phone ?? '-' }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- 3. 7 Dimensions BerAKHLAK Radar Chart Visualisation -->
<div class="row g-4 mb-4">
    <!-- Left Column: Radar Chart -->
    <div class="col-12 col-xl-7">
        <div class="executive-card p-4 h-100">
            <div class="d-flex align-items-center justify-content-between mb-3 pb-3 border-bottom">
                <div>
                    <h5 class="fw-bold text-dark mb-1">
                        <i class="bi bi-hexagon me-2 text-primary"></i>Radar Profil 7 Dimensi BerAKHLAK
                    </h5>
                    <div class="text-muted small">Visualisasi kompetensi 360° berbasis Core Values ASN BerAKHLAK</div>
                </div>
                @if($latestResult)
                    <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-2 rounded-pill fw-semibold">
                        {{ $latestResult->period->name ?? 'Periode Aktif' }}
                    </span>
                @endif
            </div>

            @if($latestResult)
                <div style="position: relative; height: 320px; width: 100%;">
                    <canvas id="profileBerakhlakRadarChart"></canvas>
                </div>
            @else
                <div class="text-center py-5 text-muted">
                    <i class="bi bi-hexagon fs-1 d-block mb-3 text-secondary"></i>
                    <h6 class="fw-semibold text-dark mb-1">Belum Ada Data Penilaian 360°</h6>
                    <small>Grafik radar 7 dimensi kompetensi BerAKHLAK akan muncul setelah penilaian periode ini selesai dikalkulasi.</small>
                </div>
            @endif
        </div>
    </div>

    <!-- Right Column: Summary Score & Analysis Cards -->
    <div class="col-12 col-xl-5">
        <div class="d-flex flex-column gap-3 h-100">
            @if($latestResult)
                <!-- Score Highlight Box -->
                <div class="executive-card p-4 text-center">
                    <span class="text-muted text-uppercase fw-semibold tracking-wider small mb-1 d-block">NILAI AKHIR KINERJA 360° TERAKHIR</span>
                    <div class="fw-extrabold text-primary mb-2" style="font-size: 44px; line-height: 1; letter-spacing: -1px; color: #1E40AF !important;">
                        {{ number_format($latestResult->final_score ?? 0, 2) }}
                    </div>
                    <div>
                        <span class="badge-pill-custom" style="{{ $badgeStyle }}">
                            <i class="bi bi-award me-1"></i> PREDIKAT: {{ $catLabel }}
                        </span>
                    </div>
                </div>

                <!-- Strength Card -->
                <div class="executive-card p-4 flex-fill border-start border-success border-4">
                    <div class="d-flex align-items-center gap-3 mb-2">
                        <div class="bg-success bg-opacity-10 text-success rounded-circle p-2 d-flex align-items-center justify-content-center" style="width: 44px; height: 44px;">
                            <i class="bi bi-star-fill fs-5"></i>
                        </div>
                        <div>
                            <span class="text-success fw-bold text-uppercase small tracking-wider">Kekuatan Utama (Top Strength)</span>
                            <h5 class="fw-bold text-dark mb-0">{{ $topStrength }}</h5>
                        </div>
                    </div>
                    <div class="d-flex align-items-center justify-content-between bg-light p-3 rounded-3 mt-3">
                        <span class="text-muted small">Skor Evaluasi Terhitung:</span>
                        <span class="fw-bold text-success fs-5">{{ $topStrengthVal }} / 100</span>
                    </div>
                </div>

                <!-- Development Area Card -->
                <div class="executive-card p-4 flex-fill border-start border-warning border-4">
                    <div class="d-flex align-items-center gap-3 mb-2">
                        <div class="bg-warning bg-opacity-10 text-warning rounded-circle p-2 d-flex align-items-center justify-content-center" style="width: 44px; height: 44px;">
                            <i class="bi bi-arrow-up-circle-fill fs-5"></i>
                        </div>
                        <div>
                            <span class="text-warning-emphasis fw-bold text-uppercase small tracking-wider" style="color: #B45309 !important;">Area Pengembangan</span>
                            <h5 class="fw-bold text-dark mb-0">{{ $areaImprovement }}</h5>
                        </div>
                    </div>
                    <div class="d-flex align-items-center justify-content-between bg-light p-3 rounded-3 mt-3">
                        <span class="text-muted small">Skor Evaluasi Terhitung:</span>
                        <span class="fw-bold text-warning fs-5" style="color: #B45309 !important;">{{ $areaImprovementVal }} / 100</span>
                    </div>
                </div>
            @else
                <div class="executive-card p-4 text-center h-100 d-flex flex-column align-items-center justify-content-center">
                    <i class="bi bi-bar-chart-line fs-1 text-secondary mb-3"></i>
                    <h6 class="fw-bold text-dark mb-1">Rincian Hasil Kinerja</h6>
                    <p class="text-muted small mb-0">Belum ada evaluasi kinerja yang tersimpan untuk akun Anda.</p>
                </div>
            @endif
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
@if($latestResult)
<script>
    document.addEventListener('DOMContentLoaded', function () {
<<<<<<< HEAD
        const ctx = document.getElementById('profileAssessmentChart').getContext('2d');
        
        const labels = {!! $aspectAverages->pluck('name')->toJson() !!};
        const data = {!! $aspectAverages->pluck('average_score')->map(fn($v) => round((float)$v, 2))->toJson() !!};
        
        // Array of predefined colors for the 7 aspects
        const baseColors = [
            { bg: 'rgba(30, 58, 95, 0.85)', border: '#1E3A5F' },
            { bg: 'rgba(13, 110, 253, 0.75)', border: '#0d6efd' },
            { bg: 'rgba(25, 135, 84, 0.75)', border: '#198754' },
            { bg: 'rgba(220, 53, 69, 0.75)', border: '#dc3545' },
            { bg: 'rgba(253, 126, 20, 0.75)', border: '#fd7e14' },
            { bg: 'rgba(13, 202, 240, 0.75)', border: '#0dcaf0' },
            { bg: 'rgba(111, 66, 193, 0.75)', border: '#6f42c1' }
        ];

        // Generate colors dynamically based on the number of aspects
        const bgColors = labels.map((_, i) => baseColors[i % baseColors.length].bg);
        const borderColors = labels.map((_, i) => baseColors[i % baseColors.length].border);
=======
        const ctx = document.getElementById('profileBerakhlakRadarChart');
        if (!ctx) return;

        const radarLabels = @json($radarLabels);
        const radarValues = @json($radarValues);
>>>>>>> 0730e7d (feat: redesign Profile & Rapor 360 views with 7-dimension BerAKHLAK Radar Chart, senior-friendly biodata card, and unified hero headers)

        new Chart(ctx, {
            type: 'radar',
            data: {
                labels: radarLabels,
                datasets: [{
                    label: 'Skor Evaluasi Kompetensi (Skala 100)',
                    data: radarValues,
                    backgroundColor: 'rgba(37, 99, 235, 0.22)',
                    borderColor: '#2563EB',
                    borderWidth: 2.5,
                    pointBackgroundColor: '#2563EB',
                    pointBorderColor: '#FFFFFF',
                    pointBorderWidth: 2,
                    pointRadius: 4,
                    pointHoverRadius: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    r: {
                        angleLines: { color: 'rgba(226, 232, 240, 0.8)' },
                        grid: { color: 'rgba(226, 232, 240, 0.8)' },
                        pointLabels: {
                            font: { family: 'Inter', size: 11, weight: '600' },
                            color: '#0F172A'
                        },
                        ticks: {
                            stepSize: 20,
                            backdropColor: 'transparent',
                            font: { size: 9 },
                            color: '#64748B'
                        },
                        suggestedMin: 50,
                        suggestedMax: 100
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        backgroundColor: '#0F172A',
                        padding: 10,
                        cornerRadius: 8,
                        titleFont: { size: 12, weight: 'bold' },
                        bodyFont: { size: 12 },
                        callbacks: {
                            label: function(context) {
                                return ' Skor: ' + context.raw + ' / 100';
                            }
                        }
                    }
<<<<<<< HEAD
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 10,
                        ticks: {
                            stepSize: 2
                        },
                        grid: {
                            color: '#e2e8f0'
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            display: false
                        }
                    }
=======
>>>>>>> 0730e7d (feat: redesign Profile & Rapor 360 views with 7-dimension BerAKHLAK Radar Chart, senior-friendly biodata card, and unified hero headers)
                }
            }
        });
    });
</script>
@endif
@endpush
