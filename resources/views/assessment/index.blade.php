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
        border-radius: 24px;
        color: #FFFFFF;
        padding: 32px;
        box-shadow: 0 10px 30px -5px rgba(37, 99, 235, 0.25);
        position: relative;
        overflow: hidden;
    }

    .hero-banner::after {
        content: '';
        position: absolute;
        right: -40px;
        bottom: -40px;
        width: 200px;
        height: 200px;
        background: rgba(255, 255, 255, 0.08);
        border-radius: 50%;
        pointer-events: none;
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
            <div class="d-flex align-items-center gap-2 mb-2">
                <span class="badge bg-white text-primary fw-semibold px-3 py-2 rounded-pill fs-7">
                    <i class="bi bi-shield-check me-1"></i> Dashboard ASN Pemalang
                </span>
                <span class="badge bg-white bg-opacity-20 text-white fw-medium px-3 py-2 rounded-pill fs-7">
                    SIKINERJA 360°
                </span>
            </div>
            <h2 class="fw-bold text-white mb-2 fs-3">📊 Riwayat Hasil Penilaian Saya</h2>
            <p class="text-white text-opacity-80 mb-0 fs-6">
                Pantau histori evaluasi kinerja ASN berbasis 360 Degree Feedback secara transparan, akuntabel, dan siap diunduh.
            </p>
        </div>
        <div class="col-12 col-lg-4 text-lg-end">
            <div class="d-inline-flex flex-column align-items-lg-end gap-1 bg-white bg-opacity-10 p-3 rounded-4 backdrop-blur">
                <span class="text-white text-opacity-75 small">Terakhir Diperbarui</span>
                <span class="fw-bold text-white fs-6">
                    <i class="bi bi-clock-history me-1"></i>
                    {{ $latestResult?->updated_at ? $latestResult->updated_at->diffForHumans() : date('d M Y') }}
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
            <form method="GET" action="{{ route('assessment.index') }}" class="d-flex align-items-center gap-2 flex-wrap">
                <div class="input-group input-group-merge" style="max-width: 320px;">
                    <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-funnel"></i></span>
                    <select name="period_id" class="form-select bg-light border-start-0 ps-0 fw-medium" onchange="this.form.submit()">
                        <option value="">-- Semua Periode Penilaian --</option>
                        @foreach($periods as $p)
                            <option value="{{ $p->id }}" {{ request('period_id') == $p->id ? 'selected' : '' }}>
                                {{ $p->name }} ({{ $p->year }})
                            </option>
                        @endforeach
                    </select>
                </div>
                @if(request('period_id'))
                    <a href="{{ route('assessment.index') }}" class="btn btn-light border text-muted fw-medium rounded-3" title="Reset Filter">
                        <i class="bi bi-x-circle me-1"></i> Reset
                    </a>
                @endif
            </form>
        </div>
        <div class="col-12 col-lg-6 text-lg-end">
            <div class="d-flex align-items-center justify-content-lg-end gap-2 flex-wrap">
                <a href="{{ route('assessment.exportAllPdf') }}" target="_blank" class="btn btn-primary fw-semibold px-3 rounded-3">
                    <i class="bi bi-file-pdf me-1"></i> Ekspor Rekap PDF
                </a>
                <a href="{{ route('assessment.exportAllExcel') }}" class="btn btn-outline-primary fw-semibold px-3 rounded-3">
                    <i class="bi bi-file-earmark-excel me-1"></i> Ekspor Rekap Excel
                </a>
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
        <a href="{{ route('dashboard') }}" class="btn btn-primary fw-semibold px-4 rounded-3">
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
                                            data-bs-toggle="collapse" 
                                            data-bs-target="#drawer-{{ $res->id }}" 
                                            aria-expanded="false" 
                                            title="Buka Detail Component Rapor">
                                        <i class="bi bi-chevron-down"></i>
                                    </button>
                                    <a href="{{ route('assessment.exportPdf', $res->id) }}" target="_blank" class="btn-action-soft btn-action-pdf" title="Ekspor PDF">
                                        <i class="bi bi-file-pdf"></i>
                                    </a>
                                    <a href="{{ route('assessment.exportExcel', $res->id) }}" class="btn-action-soft btn-action-excel" title="Ekspor Excel">
                                        <i class="bi bi-file-earmark-excel"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <!-- Inline Expandable Detail Drawer -->
                        <tr class="collapse" id="drawer-{{ $res->id }}">
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
                                    <div class="row g-2">
                                        <div class="col-12 col-md-3">
                                            <div class="bg-white p-3 rounded-3 border">
                                                <small class="text-muted d-block fw-semibold">SKOR ATASAN</small>
                                                <span class="fs-5 fw-bold text-dark">{{ number_format($res->subordinate_average ?? 0, 2) }}</span>
                                                <span class="text-muted small"> (1-10)</span>
                                            </div>
                                        </div>
                                        <div class="col-12 col-md-3">
                                            <div class="bg-white p-3 rounded-3 border">
                                                <small class="text-muted d-block fw-semibold">SKOR SEJAWAT</small>
                                                <span class="fs-5 fw-bold text-dark">{{ number_format($res->peer_average ?? 0, 2) }}</span>
                                                <span class="text-muted small"> (1-10)</span>
                                            </div>
                                        </div>
                                        @if($isKabid)
                                            <div class="col-12 col-md-3">
                                                <div class="bg-white p-3 rounded-3 border">
                                                    <small class="text-muted d-block fw-semibold">SKOR BAWAHAN</small>
                                                    <span class="fs-5 fw-bold text-dark">{{ number_format($res->superior_average ?? 0, 2) }}</span>
                                                    <span class="text-muted small"> (1-10)</span>
                                                </div>
                                            </div>
                                        @endif
                                        <div class="col-12 col-md-3">
                                            <div class="bg-white p-3 rounded-3 border border-primary border-opacity-25">
                                                <small class="text-primary d-block fw-bold">NILAI AKHIR 360°</small>
                                                <span class="fs-4 fw-bold text-primary">{{ number_format($res->final_score ?? 0, 2) }}</span>
                                                <span class="text-muted small"> / 100</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @if($myResults->hasPages())
            <div class="p-3 border-top d-flex justify-content-between align-items-center flex-wrap gap-2">
                <small class="text-muted">
                    Menampilkan {{ $myResults->firstItem() }} - {{ $myResults->lastItem() }} dari {{ $myResults->total() }} histori
                </small>
                <div>
                    {{ $myResults->withQueryString()->links() }}
                </div>
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
                    <a href="{{ route('assessment.exportExcel', $res->id) }}" class="btn btn-sm btn-outline-primary w-50 fw-semibold rounded-3 py-2">
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
