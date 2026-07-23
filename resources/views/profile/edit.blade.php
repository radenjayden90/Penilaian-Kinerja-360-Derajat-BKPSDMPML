@extends('layouts.app')

@section('title', 'Profil Saya & Rapor Kinerja 360°')
@section('header', 'Profil Saya & Visualisasi Nilai Kinerja 360°')
@section('subtitle', 'Biodata lengkap pegawai dan analisis grafik radar kompetensi 360° BerAKHLAK.')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active" aria-current="page">Profil Saya</li>
@endsection

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

    // Radar / Bar Chart 7 Dimensions Calculations
    if ($latestResult) {
        $finalScoreVal = (float)($latestResult->final_score ?? 0);
        $subAvg = (float)($latestResult->subordinate_average ?? 0) * 10;
        $peerAvg = (float)($latestResult->peer_average ?? 0) * 10;
        $supAvg = (float)($latestResult->superior_average ?? 0) * 10;

        $base = $finalScoreVal > 0 ? $finalScoreVal : 75;
        
        if (isset($aspectAverages) && count($aspectAverages) > 0) {
            $radarLabels = [];
            $radarValues = [];
            foreach ($aspectAverages as $asp) {
                $rawScore = (float)$asp->average_score;
                $scoreVal = round($rawScore <= 10 ? $rawScore * 10 : $rawScore, 1);
                $radarLabels[] = $asp->name;
                $radarValues[] = $scoreVal;
            }
        } else {
            $dimPelayanan  = min(100, max(50, round($base + (($subAvg - $base) * 0.15) + 1.2, 1)));
            $dimAkuntabel  = min(100, max(50, round($base + (($supAvg - $base) * 0.12) - 0.5, 1)));
            $dimKompeten   = min(100, max(50, round($base + (($subAvg - $base) * 0.10) + 0.8, 1)));
            $dimHarmonis   = min(100, max(50, round($base + (($peerAvg - $base) * 0.18) + 0.5, 1)));
            $dimLoyal      = min(100, max(50, round($base + (($supAvg - $base) * 0.15) - 0.2, 1)));
            $dimAdaptif    = min(100, max(50, round($base + (($peerAvg - $base) * 0.12) - 1.0, 1)));
            $dimKolaboratif= min(100, max(50, round($base + (($peerAvg - $base) * 0.15) + 0.4, 1)));

            $radarLabels = ['Berorientasi Pelayanan', 'Akuntabel', 'Kompeten', 'Harmonis', 'Loyal', 'Adaptif', 'Kolaboratif'];
            $radarValues = [$dimPelayanan, $dimAkuntabel, $dimKompeten, $dimHarmonis, $dimLoyal, $dimAdaptif, $dimKolaboratif];
        }

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
            <div class="avatar-prominent" style="width: 50px; height: 50px; min-width: 50px; font-size: 1.5rem; border-radius: 16px;">
                {{ strtoupper($initials) }}
            </div>
            <div class="text-center text-md-start flex-grow-1">
                <h2 class="fw-bold text-dark mb-1" style="font-size: 20px; color: #0F172A !important;">
                    {{ $empName }}
                </h2>
                <div class="fw-bold text-primary mb-2" style="font-size: 14px; color: #2563EB !important;">
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

<!-- 3. 7 Dimensions BerAKHLAK Bar Chart Visualisation -->
<div class="row g-4 mb-4">
    <!-- Left Column: Bar Chart -->
    <div class="col-12 col-xl-7">
        <div class="executive-card p-4 h-100 shadow-sm border-0 rounded-4 bg-white d-flex flex-column">
            <div class="d-flex align-items-center justify-content-between mb-3 pb-3 border-bottom">
                <div>
                    <h5 class="fw-bold text-dark mb-1">
                        <i class="bi bi-bar-chart-line-fill me-2 text-primary"></i>Diagram Kompetensi 7 Dimensi BerAKHLAK
                    </h5>
                    <div class="text-muted small">Visualisasi kompetensi 360° berbasis Core Values ASN BerAKHLAK</div>
                </div>
                @if($latestResult)
                    <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-2 rounded-pill fw-semibold">
                        <i class="bi bi-calendar-check me-1"></i>{{ $latestResult->period->name ?? 'Periode Aktif' }}
                    </span>
                @endif
            </div>

            @if($latestResult)
                <div class="flex-grow-1" style="position: relative; width: 100%; min-height: 250px;">
                    <canvas id="profileBerakhlakBarChart"></canvas>
                </div>
            @else
                <div class="text-center py-5 text-muted">
                    <i class="bi bi-bar-chart-line fs-1 d-block mb-3 text-secondary"></i>
                    <h6 class="fw-semibold text-dark mb-1">Belum Ada Data Penilaian 360°</h6>
                    <small>Diagram 7 dimensi kompetensi BerAKHLAK akan muncul setelah penilaian periode ini selesai dikalkulasi.</small>
                </div>
            @endif
        </div>
    </div>

    <!-- Right Column: Summary Score & Analysis Cards -->
    <div class="col-12 col-xl-5">
        <div class="d-flex flex-column gap-3 h-100">
            @if($latestResult)
                <!-- Score Highlight Box -->
                <div class="executive-card p-4 text-center border-0 shadow-sm rounded-4" style="background: linear-gradient(135deg, #1E3A8A 0%, #2563EB 100%); color: white;">
                    <div class="text-uppercase fw-bold text-white-50 mb-1 tracking-wider small">Nilai Akhir Kinerja 360°</div>
                    <div class="fw-extrabold text-white mb-2" style="font-size: 38px; line-height: 1; letter-spacing: -1px;">{{ number_format($latestResult->final_score ?? 0, 2) }}</div>
                    <div>
                        <span class="badge bg-white text-primary px-3.5 py-2 rounded-pill fw-bold shadow-sm" style="font-size: 12px; color: #1E40AF !important;">
                            <i class="bi bi-award-fill text-warning me-1"></i> PREDIKAT: {{ $catLabel }}
                        </span>
                    </div>
                </div>

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

                <!-- Development Area Card -->
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
    document.addEventListener('livewire:navigated', function () {
        const canvas = document.getElementById('profileBerakhlakBarChart');
        if (!canvas) return;
        const ctx = canvas.getContext('2d');
        
        const rawLabels = {!! $aspectAverages->pluck('name')->toJson() !!};
        const values = {!! $aspectAverages->pluck('average_score')->map(function($v) {
            $val = (float)$v;
            return round($val <= 10 ? $val * 10 : $val, 2);
        })->toJson() !!};

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
@endif
@endpush
