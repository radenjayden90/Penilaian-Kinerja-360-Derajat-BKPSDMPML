@extends('layouts.app')

@section('title', 'Riwayat Hasil Penilaian Saya')
@section('header', 'Riwayat Penilaian Saya')
@section('subtitle', 'Histori rapor evaluasi kinerja 360° milik Anda dari seluruh periode penilaian.')

@section('breadcrumb')
    <li class="breadcrumb-item active" aria-current="page">Riwayat Penilaian</li>
@endsection

@push('styles')
<style>
    /* Custom Executive Dashboard Styling */
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
        transform: translateY(-4px);
        box-shadow: 0 12px 28px -4px rgba(37, 99, 235, 0.12);
        border-color: #BFDBFE;
    }

    .kpi-icon-wrapper {
        width: 52px;
        height: 52px;
        border-radius: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.35rem;
    }

    .hero-banner {
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

    .hero-banner::before {
        content: '';
        position: absolute;
        top: -40px;
        left: -40px;
        width: 150px;
        height: 150px;
        background: radial-gradient(circle, rgba(255, 255, 255, 0.08) 0%, rgba(255, 255, 255, 0) 70%);
        border-radius: 50%;
        pointer-events: none;
    }

    .hero-banner::after {
        content: '';
        position: absolute;
        right: -30px;
        bottom: -30px;
        width: 170px;
        height: 170px;
        background: radial-gradient(circle, rgba(255, 255, 255, 0.06) 0%, rgba(255, 255, 255, 0) 70%);
        border-radius: 50%;
        pointer-events: none;
    }

    .hero-icon-box {
        width: 36px;
        height: 36px;
        border-radius: 10px;
        background: rgba(255, 255, 255, 0.15);
        backdrop-filter: blur(4px);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 1rem;
    }

    .hero-badge {
        font-size: 13px;
        font-weight: 600;
        padding: 5px 12px;
        border-radius: 9999px;
        background: rgba(255, 255, 255, 0.18);
        color: #FFFFFF;
        backdrop-filter: blur(4px);
        border: 1px solid rgba(255, 255, 255, 0.25);
        display: inline-flex;
        align-items: center;
        gap: 6px;
        transition: transform 300ms ease;
    }

    .hero-badge:hover {
        transform: scale(1.03);
    }

    .hero-title {
        font-size: 24px;
        font-weight: 700;
        letter-spacing: -0.5px;
        line-height: 1.2;
    }

    .hero-desc {
        font-size: 14px;
        font-weight: 500;
        line-height: 1.5;
        max-width: 650px;
        color: rgba(255, 255, 255, 0.9);
    }

    .hero-card-updated {
        display: inline-flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        text-align: center;
        min-width: 195px;
        padding: 14px 20px;
        background: rgba(255, 255, 255, 0.14);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.25);
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        border-radius: 16px;
        animation: slideInRight 500ms ease-out forwards;
    }

    @keyframes slideInRight {
        from { opacity: 0; transform: translateX(16px); }
        to { opacity: 1; transform: translateX(0); }
    }

    .kpi-pill-item {
        height: 44px;
        background: rgba(255, 255, 255, 0.12);
        border: 1px solid rgba(255, 255, 255, 0.2);
        border-radius: 9999px;
        padding: 10px 18px;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        backdrop-filter: blur(4px);
        transition: transform 300ms ease;
    }

    .kpi-pill-item:hover {
        transform: scale(1.03);
    }

    .kpi-pill-val {
        font-size: 18px;
        font-weight: 700;
        color: #FFFFFF;
    }

    .kpi-pill-lbl {
        font-size: 14px;
        font-weight: 500;
        color: rgba(255, 255, 255, 0.85);
    }

    .toolbar-container {
        background: #FFFFFF;
        border: 1px solid var(--card-border);
        border-radius: 16px;
        padding: 16px 20px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.02);
    }

    .executive-table-container {
        background: #FFFFFF;
        border: 1px solid var(--card-border);
        border-radius: 20px;
        overflow: hidden;
        box-shadow: 0 4px 20px -2px rgba(15, 23, 42, 0.04);
    }

    .executive-table {
        margin-bottom: 0;
    }

    .executive-table th {
        background-color: var(--primary-blue) !important;
        color: #FFFFFF !important;
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.78rem;
        letter-spacing: 0.05em;
        padding: 16px 20px;
        border: none;
        vertical-align: middle;
    }

    .executive-table td {
        padding: 18px 20px;
        vertical-align: middle;
        border-bottom: 1px solid #F1F5F9;
        transition: background-color 200ms ease;
    }

    .executive-table tbody tr:hover td {
        background-color: #F8FAFC !important;
    }

    .badge-pill {
        border-radius: 9999px;
        padding: 6px 16px;
        font-weight: 600;
        font-size: 0.78rem;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        border: 1px solid transparent;
    }

    .badge-sangat-baik {
        background-color: #DCFCE7;
        color: #15803D;
        border-color: #86EFAC;
    }

    .badge-baik {
        background-color: #F0FDF4;
        color: #166534;
        border-color: #BBF7D0;
    }

    .badge-cukup {
        background-color: #FEF9C3;
        color: #854D0E;
        border-color: #FDE047;
    }

    .badge-kurang {
        background-color: #FFEDD5;
        color: #C2410C;
        border-color: #FDBA74;
    }

    .badge-pembinaan {
        background-color: #FEE2E2;
        color: #B91C1C;
        border-color: #FCA5A5;
    }

    .btn-action-soft {
        width: 38px;
        height: 38px;
        border-radius: 12px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        transition: all 200ms ease;
        border: none;
        font-size: 1.1rem;
    }

    .btn-action-pdf {
        background-color: #FEE2E2;
        color: #DC2626;
    }
    .btn-action-pdf:hover {
        background-color: #DC2626;
        color: #FFFFFF;
        transform: scale(1.05);
    }

    .btn-action-excel {
        background-color: #DCFCE7;
        color: #16A34A;
    }
    .btn-action-excel:hover {
        background-color: #16A34A;
        color: #FFFFFF;
        transform: scale(1.05);
    }

    .btn-action-detail {
        background-color: #DBEAFE;
        color: #2563EB;
    }
    .btn-action-detail:hover {
        background-color: #2563EB;
        color: #FFFFFF;
        transform: scale(1.05);
    }

    .detail-drawer {
        background-color: #F8FAFC;
        border-left: 4px solid var(--primary-blue);
        padding: 20px;
        border-radius: 0 0 16px 16px;
    }

    /* Skeleton Loader Styling */
    .skeleton {
        background: linear-gradient(90deg, #E2E8F0 25%, #F1F5F9 50%, #E2E8F0 75%);
        background-size: 200% 100%;
        animation: skeleton-loading 1.5s infinite;
        border-radius: 8px;
    }
    @keyframes skeleton-loading {
        0% { background-position: 200% 0; }
        100% { background-position: -200% 0; }
    }

    /* Hover effect for dropdowns */
    .dropdown:hover .dropdown-menu {
        display: block;
        margin-top: 0; 
    }
</style>
@endpush

@section('content')

@php
    $posName = strtolower($employee->position?->name ?? '');
    $isKabid = ($employee->position?->level == '2' || str_contains($posName, 'kepala bidang') || str_contains($posName, 'kabid') || str_contains($posName, 'sekretaris'));
    $latestResult = $myResults->first();
    $latestScore = $latestResult?->final_score !== null ? number_format($latestResult->final_score, 2) : '-';
    $avgScore = $myResults->count() > 0 ? number_format($myResults->avg('final_score'), 2) : '-';
    $totalCount = $myResults->total();

    $catEnum = $latestResult?->category instanceof \App\Enums\ResultCategory ? $latestResult->category : \App\Enums\ResultCategory::tryFrom($latestResult?->category ?? '');
    $latestPredikatLabel = $catEnum ? $catEnum->label() : strtoupper((string)($latestResult?->category ?? '-'));
@endphp

<!-- 1. Hero Header Section -->
<div class="hero-banner mb-4">
    <div class="row align-items-center g-3">
        <div class="col-12 col-lg-8">
            <div style="margin-bottom: 16px;">
                <span class="hero-badge">
                    <i class="bi bi-shield-check me-1"></i> BKPSDM Kabupaten Pemalang
                </span>
            </div>
            <div class="d-flex align-items-center gap-3" style="margin-bottom: 12px;">
                <div class="hero-icon-box text-white">
                    📊
                </div>
                <h1 class="hero-title text-white mb-0">Riwayat Hasil Penilaian Saya</h1>
            </div>
            <p class="hero-desc" style="margin-bottom: 0;">
                Lihat riwayat hasil penilaian kinerja ASN berbasis 360 Degree Feedback pada setiap periode penilaian, lengkap dengan ringkasan nilai serta laporan yang dapat diunduh.
            </p>
        </div>

        <div class="col-12 col-lg-4 text-center text-lg-end">
            <div class="hero-card-updated">
                <span class="text-white text-opacity-85 d-block mb-1" style="font-size: 13px; font-weight: 500; white-space: nowrap;">
                    <i class="bi bi-clock-history me-1"></i> Terakhir Diperbarui
                </span>
                <span class="fw-bold text-white d-block" style="font-size: 20px; line-height: 1.2; white-space: nowrap;">
                    {{ \Carbon\Carbon::parse($latestResult?->updated_at ?? now())->locale('id')->translatedFormat('d F Y') }}
                </span>
                <span class="fw-semibold text-white text-opacity-90 d-block mt-1" style="font-size: 13px; white-space: nowrap;">
                    {{ \Carbon\Carbon::parse($latestResult?->updated_at ?? now())->format('H.i') }} WIB
                </span>
            </div>
        </div>
    </div>
</div>

<!-- 2. Summary KPI Cards (4 Cards Grid) -->
<div class="row g-3 mb-4">
    <!-- Card 1: Nilai Terakhir -->
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="executive-card p-4">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <span class="text-muted fw-semibold small text-uppercase tracking-wider">Nilai Terakhir</span>
                <div class="kpi-icon-wrapper" style="background-color: #FEF3C7; color: #D97706;">
                    <i class="bi bi-trophy-fill"></i>
                </div>
            </div>
            <div class="d-flex align-items-baseline gap-2">
                <span class="fw-bold text-dark fs-2 mb-0" style="color: var(--primary-blue) !important;">{{ $latestScore }}</span>
                <span class="text-muted small">/ 100</span>
            </div>
            <div class="mt-2 text-muted small">Skor evaluasi periode terbaru</div>
        </div>
    </div>

    <!-- Card 2: Rata-Rata Nilai -->
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="executive-card p-4">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <span class="text-muted fw-semibold small text-uppercase tracking-wider">Rata-Rata Nilai</span>
                <div class="kpi-icon-wrapper" style="background-color: #DBEAFE; color: #2563EB;">
                    <i class="bi bi-graph-up-arrow"></i>
                </div>
            </div>
            <div class="d-flex align-items-baseline gap-2">
                <span class="fw-bold text-dark fs-2 mb-0">{{ $avgScore }}</span>
                <span class="text-muted small">/ 100</span>
            </div>
            <div class="mt-2 text-muted small">Rata-rata akumulasi histori</div>
        </div>
    </div>

    <!-- Card 3: Jumlah Periode -->
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="executive-card p-4">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <span class="text-muted fw-semibold small text-uppercase tracking-wider">Jumlah Periode</span>
                <div class="kpi-icon-wrapper" style="background-color: #F3E8FF; color: #9333EA;">
                    <i class="bi bi-calendar-check-fill"></i>
                </div>
            </div>
            <div class="d-flex align-items-baseline gap-2">
                <span class="fw-bold text-dark fs-2 mb-0">{{ $totalCount }}</span>
                <span class="text-muted small">Periode</span>
            </div>
            <div class="mt-2 text-muted small">Total evaluasi selesai</div>
        </div>
    </div>

    <!-- Card 4: Predikat Terakhir -->
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="executive-card p-4">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <span class="text-muted fw-semibold small text-uppercase tracking-wider">Predikat Terakhir</span>
                <div class="kpi-icon-wrapper" style="background-color: #DCFCE7; color: #16A34A;">
                    <i class="bi bi-award-fill"></i>
                </div>
            </div>
            <div class="mt-1">
                @if($latestResult?->category)
                    @php
                        $badgeClass = match($catEnum) {
                            \App\Enums\ResultCategory::VERY_GOOD => 'badge-sangat-baik',
                            \App\Enums\ResultCategory::GOOD => 'badge-baik',
                            \App\Enums\ResultCategory::FAIR => 'badge-cukup',
                            \App\Enums\ResultCategory::NEEDS_IMPROVEMENT => 'badge-pembinaan',
                            default => 'bg-light text-dark'
                        };
                    @endphp
                    <span class="badge-pill {{ $badgeClass }} fs-6">
                        <i class="bi bi-patch-check-fill"></i> {{ $latestPredikatLabel }}
                    </span>
                @else
                    <span class="text-muted fw-semibold fs-5">-</span>
                @endif
            </div>
            <div class="mt-2 text-muted small">Kategori penilaian terbaru</div>
        </div>
    </div>
</div>

<!-- 3. Toolbar & Filter Section -->
<div class="toolbar-container mb-4">
    <div class="row align-items-center g-3">
        <div class="col-12 col-lg-6">
            <form method="GET" action="{{ route('assessment.index') }}" class="d-flex align-items-center gap-2 flex-wrap" x-data @submit.prevent="Livewire.navigate($el.action + '?' + new URLSearchParams(new FormData($el)).toString())">
                <div class="input-group input-group-merge" style="max-width: 320px;">
                    <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-funnel"></i></span>
                    <select name="period_id" class="form-select bg-light border-start-0 ps-0 fw-medium" onchange="this.form.requestSubmit()">
                        <option value="">-- Semua Periode Penilaian --</option>
                        @foreach($periods as $p)
                            <option value="{{ $p->id }}" {{ request('period_id') == $p->id ? 'selected' : '' }}>
                                {{ $p->name }} ({{ $p->year }})
                            </option>
                        @endforeach
                    </select>
                </div>
                @if(request('period_id'))
                    <a href="{{ route('assessment.index') }}" class="btn btn-light border text-muted fw-medium rounded-3" title="Reset Filter" wire:navigate>
                        <i class="bi bi-x-circle me-1"></i> Reset
                    </a>
                @endif
            </form>
        </div>
        <div class="col-12 col-lg-6 text-lg-end">
            <div class="d-flex align-items-center justify-content-lg-end gap-2 flex-wrap">
                @php $availableYears = $periods->pluck('year')->unique(); @endphp
                <!-- Dropdown Ekspor PDF -->
                <div class="dropdown d-inline-block">
                    <button class="btn btn-primary fw-semibold px-3 rounded-3 dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-file-pdf me-1"></i> Ekspor Rekap PDF
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0" style="min-width: 220px;">
                        <li><h6 class="dropdown-header">Pilih Rentang Waktu</h6></li>
                        <li><a wire:navigate class="dropdown-item py-2" href="{{ route('assessment.exportAllPdf', ['year' => 'all']) }}" target="_blank"><i class="bi bi-journal-text me-2 text-primary"></i>Seluruh Hasil Penilaian</a></li>
                        @if($availableYears->isNotEmpty())
                            <li><hr class="dropdown-divider"></li>
                            @foreach($availableYears as $yr)
                                <li><a wire:navigate class="dropdown-item py-2" href="{{ route('assessment.exportAllPdf', ['year' => $yr]) }}" target="_blank"><i class="bi bi-calendar me-2 text-muted"></i>Tahun {{ $yr }}</a></li>
                            @endforeach
                        @endif
                    </ul>
                </div>

                <!-- Dropdown Ekspor Excel -->
                <div class="dropdown d-inline-block">
                    <button class="btn btn-outline-primary fw-semibold px-3 rounded-3 dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-file-earmark-excel me-1"></i> Ekspor Rekap Excel
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0" style="min-width: 220px;">
                        <li><h6 class="dropdown-header">Pilih Rentang Waktu</h6></li>
                        <li><a wire:navigate class="dropdown-item py-2" href="{{ route('assessment.exportAllExcel', ['year' => 'all']) }}"><i class="bi bi-journal-text me-2 text-success"></i>Seluruh Hasil Penilaian</a></li>
                        @if($availableYears->isNotEmpty())
                            <li><hr class="dropdown-divider"></li>
                            @foreach($availableYears as $yr)
                                <li><a wire:navigate class="dropdown-item py-2" href="{{ route('assessment.exportAllExcel', ['year' => $yr]) }}"><i class="bi bi-calendar me-2 text-muted"></i>Tahun {{ $yr }}</a></li>
                            @endforeach
                        @endif
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- 4. Score Progress & Gauge Visual Summary -->
@if($latestResult && $latestResult->final_score !== null)
<div class="executive-card p-4 mb-4">
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-2">
        <div class="d-flex align-items-center gap-2">
            <i class="bi bi-speedometer2 text-primary fs-5"></i>
            <span class="fw-bold text-dark fs-6">Progress Capaian Kinerja Periode Terakhir ({{ $latestResult->period?->name }})</span>
        </div>
        <span class="fw-bold text-primary fs-6">{{ number_format($latestResult->final_score, 2) }} / 100</span>
    </div>
    <div class="progress rounded-pill bg-light" style="height: 12px;">
        <div class="progress-bar rounded-pill" role="progressbar" 
             style="width: {{ min(100, max(0, $latestResult->final_score)) }}%; background: linear-gradient(90deg, #2563EB 0%, #16A34A 100%);" 
             aria-valuenow="{{ $latestResult->final_score }}" aria-valuemin="0" aria-valuemax="100">
        </div>
    </div>
</div>
@endif

<!-- 5. History Content Table & Mobile Cards -->
@if($myResults->isEmpty())
    <!-- Empty State Component -->
    <div class="executive-card p-5 text-center my-4">
        <div class="kpi-icon-wrapper mx-auto mb-3" style="width: 72px; height: 72px; background-color: #EFF6FF; color: #2563EB; font-size: 2rem;">
            <i class="bi bi-inbox-fill"></i>
        </div>
        <h4 class="fw-bold text-dark mb-2">Belum Ada Riwayat Penilaian</h4>
        <p class="text-muted mb-4 mx-auto" style="max-width: 480px;">
            Anda belum memiliki hasil penilaian pada periode mana pun. Silakan cek kembali saat periode penilaian berjalan telah dihitung oleh atasan.
        </p>
        <a wire:navigate href="{{ route('dashboard') }}" class="btn btn-primary fw-semibold px-4 rounded-3">
            <i class="bi bi-house me-1"></i> Kembali ke Dashboard
        </a>
    </div>
@else
    <!-- Desktop Executive Table (Shown on MD and larger) -->
    <div class="executive-table-container d-none d-md-block mb-4">
        <div class="table-responsive">
            <table class="table executive-table align-middle">
                <thead>
                    <tr>
                        <th class="text-center" style="width: 60px;">No</th>
                        <th>Periode Penilaian</th>
                        @if($isKabid)
                            <th class="text-center">Skor Atasan (50%)</th>
                            <th class="text-center">Skor Sejawat (30%)</th>
                            <th class="text-center">Skor Bawahan (20%)</th>
                        @else
                            <th class="text-center">Skor Atasan (50%)</th>
                            <th class="text-center">Skor Sejawat (50%)</th>
                        @endif
                        <th class="text-center">Skor Akhir 360°</th>
                        <th class="text-center">Kategori Predikat</th>
                        <th class="text-center" style="width: 170px;">Aksi Rapor</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($myResults as $index => $res)
                        @php
                            $catEnum = $res->category instanceof \App\Enums\ResultCategory ? $res->category : \App\Enums\ResultCategory::tryFrom($res->category);
                            $catLabel = $catEnum ? $catEnum->label() : strtoupper((string)($res->category ?? '-'));
                            $badgeClass = match($catEnum) {
                                \App\Enums\ResultCategory::VERY_GOOD => 'badge-sangat-baik',
                                \App\Enums\ResultCategory::GOOD => 'badge-baik',
                                \App\Enums\ResultCategory::FAIR => 'badge-cukup',
                                \App\Enums\ResultCategory::NEEDS_IMPROVEMENT => 'badge-pembinaan',
                                default => 'bg-light text-dark'
                            };
                        @endphp
                        <tr>
                            <td class="text-center fw-bold text-muted">{{ $myResults->firstItem() + $index }}</td>
                            <td>
                                <div class="fw-bold text-dark fs-6 mb-0">{{ $res->period->name ?? '-' }}</div>
                                <div class="text-muted small">Tahun {{ $res->period->year ?? '-' }}</div>
                            </td>
                            @if($isKabid)
                                <td class="text-center font-monospace fw-semibold fs-6 text-dark">
                                    {{ number_format($res->subordinate_average ?? 0, 2) }}
                                </td>
                                <td class="text-center font-monospace fw-semibold fs-6 text-dark">
                                    {{ number_format($res->peer_average ?? 0, 2) }}
                                </td>
                                <td class="text-center font-monospace fw-semibold fs-6 text-dark">
                                    {{ number_format($res->superior_average ?? 0, 2) }}
                                </td>
                            @else
                                <td class="text-center font-monospace fw-semibold fs-6 text-dark">
                                    {{ number_format($res->subordinate_average ?? 0, 2) }}
                                </td>
                                <td class="text-center font-monospace fw-semibold fs-6 text-dark">
                                    {{ number_format($res->peer_average ?? 0, 2) }}
                                </td>
                            @endif
                            <td class="text-center">
                                @if($res->final_score !== null)
                                    <div>
                                        <span class="fw-bold fs-5" style="color: var(--primary-blue);">
                                            {{ number_format($res->final_score, 2) }}
                                        </span>
                                    </div>
                                    <small class="text-muted d-block" style="font-size: 0.72rem;">Skala 100</small>
                                @else
                                    <span class="text-muted small">-</span>
                                @endif
                            </td>
                            <td class="text-center">
                                @if($res->category)
                                    <span class="badge-pill {{ $badgeClass }}">
                                        <i class="bi bi-patch-check-fill"></i> {{ $catLabel }}
                                    </span>
                                @else
                                    <span class="badge bg-light text-muted fw-normal px-3 py-2 rounded-pill">Proses Evaluasi</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <div class="d-flex align-items-center justify-content-center gap-2">
                                    <button class="btn-action-soft btn-action-detail" type="button" 
                                            onclick="document.getElementById('drawer-{{ $res->id }}').classList.toggle('d-none')" 
                                            title="Buka Detail Component Rapor">
                                        <i class="bi bi-chevron-down"></i>
                                    </button>
                                    <a href="{{ route('assessment.exportPdf', $res->id) }}" target="_blank" class="btn-action-soft btn-action-pdf" title="Ekspor PDF">
                                        <i class="bi bi-file-pdf"></i>
                                    </a>
                                    <a wire:navigate href="{{ route('assessment.exportExcel', $res->id) }}" class="btn-action-soft btn-action-excel" title="Ekspor Excel">
                                        <i class="bi bi-file-earmark-excel"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <!-- Inline Expandable Detail Drawer -->
                        <tr id="drawer-{{ $res->id }}" class="d-none">
                            <td colspan="{{ $isKabid ? 8 : 7 }}" class="p-0 border-0">
                                <div class="detail-drawer">
                                        <div class="d-flex align-items-center justify-content-between mb-3">
                                        <h6 class="fw-bold text-dark mb-0">
                                            <i class="bi bi-list-check me-2 text-primary"></i>Rincian Komponen Evaluasi: {{ $res->period->name }}
                                        </h6>
                                        <span class="badge bg-white text-muted border px-3 py-2 rounded-pill small">
                                            Dihitung: {{ $res->calculated_at ? $res->calculated_at->format('d/m/Y H:i') : '-' }}
                                        </span>
                                    </div>
                                    @if(isset($res->aspectAverages) && $res->aspectAverages->count() > 0)
                                        <div class="row g-2">
                                            @foreach($res->aspectAverages as $aspect)
                                                <div class="col-12 col-sm-6 col-md-4 col-xl-3">
                                                    <div class="bg-white p-2 px-3 rounded-3 border d-flex justify-content-between align-items-center" style="box-shadow: 0 1px 3px rgba(0,0,0,0.02);">
                                                        <small class="text-muted fw-semibold" style="font-size: 0.75rem;">{{ $aspect->name }}</small>
                                                        <span class="fw-bold text-dark" style="font-size: 0.9rem;">{{ number_format($aspect->average_score, 2) }}</span>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    @else
                                        <div class="text-muted small">Detail komponen belum tersedia.</div>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @if($myResults->hasPages())
            <div class="p-3 border-top">
                {{ $myResults->withQueryString()->links() }}
            </div>
        @endif
    </div>

    <!-- Mobile Cards View (Shown on Mobile screens <768px) -->
    <div class="d-block d-md-none mb-4">
        @foreach($myResults as $index => $res)
            @php
                $catEnum = $res->category instanceof \App\Enums\ResultCategory ? $res->category : \App\Enums\ResultCategory::tryFrom($res->category);
                $catLabel = $catEnum ? $catEnum->label() : strtoupper((string)($res->category ?? '-'));
                $badgeClass = match($catEnum) {
                    \App\Enums\ResultCategory::VERY_GOOD => 'badge-sangat-baik',
                    \App\Enums\ResultCategory::GOOD => 'badge-baik',
                    \App\Enums\ResultCategory::FAIR => 'badge-cukup',
                    \App\Enums\ResultCategory::NEEDS_IMPROVEMENT => 'badge-pembinaan',
                    default => 'bg-light text-dark'
                };
            @endphp
            <div class="executive-card p-4 mb-3">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div>
                        <h6 class="fw-bold text-dark mb-1">{{ $res->period->name ?? '-' }}</h6>
                        <small class="text-muted">Tahun {{ $res->period->year ?? '-' }}</small>
                    </div>
                    @if($res->category)
                        <span class="badge-pill {{ $badgeClass }}">
                            {{ $catLabel }}
                        </span>
                    @endif
                </div>

                <div class="bg-light p-3 rounded-3 mb-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="text-muted small font-medium">Nilai Akhir 360°</span>
                        <span class="fw-bold fs-4 text-primary">{{ number_format($res->final_score ?? 0, 2) }} <small class="fs-7 text-muted">/ 100</small></span>
                    </div>
                </div>

                <div class="row g-2 mb-3 text-center">
                    <div class="col-6">
                        <div class="border p-2 rounded-2 bg-white">
                            <small class="text-muted d-block" style="font-size: 0.72rem;">Atasan</small>
                            <span class="fw-bold text-dark">{{ number_format($res->subordinate_average ?? 0, 2) }}</span>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="border p-2 rounded-2 bg-white">
                            <small class="text-muted d-block" style="font-size: 0.72rem;">Sejawat</small>
                            <span class="fw-bold text-dark">{{ number_format($res->peer_average ?? 0, 2) }}</span>
                        </div>
                    </div>
                </div>

                <div class="d-flex gap-2">
                    <a href="{{ route('assessment.exportPdf', $res->id) }}" target="_blank" class="btn btn-sm btn-outline-danger w-50 fw-semibold rounded-3 py-2">
                        <i class="bi bi-file-pdf me-1"></i> PDF
                    </a>
                    <a wire:navigate href="{{ route('assessment.exportExcel', $res->id) }}" class="btn btn-sm btn-outline-primary w-50 fw-semibold rounded-3 py-2">
                        <i class="bi bi-file-earmark-excel me-1"></i> Excel
                    </a>
                </div>
            </div>
        @endforeach

        @if($myResults->hasPages())
            <div class="d-flex justify-content-center mt-3">
                {{ $myResults->withQueryString()->links() }}
            </div>
        @endif
    </div>
@endif

@endsection
