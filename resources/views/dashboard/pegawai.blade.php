@extends('layouts.app')

@section('title', 'Dashboard Pegawai')
@section('header', 'Dashboard Pegawai')
@section('subtitle', 'Portal Penilaian Kinerja 360 Derajat ASN Kabupaten Pemalang')

@push('styles')
<style>
    /* Executive Dashboard Styling for Pegawai */
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

    .hero-banner-pegawai {
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

    .hero-banner-pegawai::before {
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

    .hero-banner-pegawai::after {
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

    .hero-badge-pegawai {
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

    .badge-info-pill {
        background: #FFFFFF;
        color: #0F172A;
        font-size: 13px;
        font-weight: 600;
        padding: 6px 16px;
        border-radius: 9999px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }

    .btn-hero-cta {
        background: #FFFFFF;
        color: #1E40AF !important;
        font-weight: 700;
        font-size: 14px;
        padding: 12px 24px;
        border-radius: 14px;
        border: none;
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.12);
        transition: all 250ms ease;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }

    .btn-hero-cta:hover {
        background: #F8FAFC;
        color: #1D4ED8 !important;
        transform: translateY(-2px);
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.18);
    }

    .glass-alert {
        border-radius: 16px;
        border: 1px solid rgba(37, 99, 235, 0.15);
        background: #F0F6FF;
        padding: 16px 20px;
    }

    .glass-alert-success {
        border-radius: 16px;
        border: 1px solid rgba(22, 163, 74, 0.2);
        background: #F0FDF4;
        padding: 16px 20px;
    }

    .glass-alert-empty {
        border-radius: 16px;
        border: 1px solid #E2E8F0;
        background: #F8FAFC;
        padding: 16px 20px;
    }

    .stat-number {
        font-size: 30px;
        font-weight: 800;
        line-height: 1.1;
        letter-spacing: -0.5px;
    }
</style>
@endpush

@section('content')

<!-- 1. Executive Welcome Hero Banner -->
<div class="hero-banner-pegawai mb-4">
    <div class="row align-items-center g-3">
        <div class="col-12 col-lg-8">
            <div class="mb-3">
                <span class="hero-badge-pegawai">
                    <i class="bi bi-shield-check me-1"></i> BKPSDM Kabupaten Pemalang
                </span>
            </div>
            <h2 class="fw-bold text-white mb-2" style="font-size: 24px; letter-spacing: -0.5px;">
                Selamat Datang, {{ $user->name ?? 'Pegawai' }}! 👋
            </h2>
            <p class="text-white text-opacity-90 mb-3" style="font-size: 14px; font-weight: 500;">
                NIP. {{ $user->nip ?? '-' }} &bull; {{ $user->position->name ?? 'Jabatan Belum Diatur' }}
            </p>
            @php
                $rawDept = $user->department->name ?? 'Unit Kerja Belum Diatur';
                $deptName = str_ireplace('bkpsdm', 'BKPSDM', ucwords(strtolower($rawDept)));
                $roleRaw = $user->role->name ?? 'Pegawai';
                $roleFormatted = ucwords(strtolower(str_replace('_', ' ', $roleRaw)));
                if (strtolower($roleFormatted) === 'employee') {
                    $roleFormatted = 'Pegawai';
                }
            @endphp
            <div class="d-flex flex-wrap align-items-center gap-2">
                <span class="badge-info-pill">
                    <i class="bi bi-building text-warning me-1"></i> {{ $deptName }}
                </span>
                <span class="badge-info-pill">
                    <i class="bi bi-person-badge text-info me-1"></i> {{ $roleFormatted }}
                </span>
            </div>
        </div>

        <div class="col-12 col-lg-4 text-lg-end">
            <a href="{{ route('transaction.assessments.index') }}" class="btn-hero-cta">
                <i class="bi bi-pencil-square fs-5"></i> Mulai Penilaian
            </a>
        </div>
    </div>
</div>

<!-- 2. Active Period Alert Banner -->
@if($activePeriod)
    <div class="glass-alert shadow-sm d-flex flex-column flex-sm-row align-items-sm-center justify-content-between gap-3 mb-4">
        <div class="d-flex align-items-center gap-3">
            <div class="bg-primary bg-opacity-10 text-primary rounded-circle p-2 d-flex align-items-center justify-content-center flex-shrink-0" style="width: 46px; height: 46px;">
                <i class="bi bi-calendar-event fs-4"></i>
            </div>
            <div>
                <div class="fw-bold text-dark fs-6" style="color: #0F172A !important;">
                    Periode Penilaian Aktif: {{ $activePeriod->name }}
                </div>
                <div class="text-muted small mt-1">
                    <i class="bi bi-clock me-1"></i> Jangka Waktu: 
                    <strong>{{ \Carbon\Carbon::parse($activePeriod->start_date)->locale('id')->isoFormat('D MMMM Y') }}</strong> s/d 
                    <strong>{{ \Carbon\Carbon::parse($activePeriod->end_date)->locale('id')->isoFormat('D MMMM Y') }}</strong>
                </div>
            </div>
        </div>
        <div>
            <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-20 px-3 py-2 rounded-pill fw-semibold" style="font-size: 13px;">
                <i class="bi bi-check-circle-fill me-1"></i> Periode Aktif
            </span>
        </div>
    </div>
@else
    <div class="glass-alert-empty shadow-sm d-flex align-items-center gap-3 p-3 mb-4">
        <div class="bg-secondary bg-opacity-10 text-secondary rounded-circle p-2 d-flex align-items-center justify-content-center flex-shrink-0" style="width: 44px; height: 44px;">
            <i class="bi bi-info-circle fs-4"></i>
        </div>
        <div>
            <div class="fw-bold text-dark">Tidak Ada Periode Penilaian Aktif Saat Ini</div>
            <div class="text-muted small">Silakan tunggu pemberitahuan dari BKPSDM untuk pembukaan periode penilaian kinerja berikutnya.</div>
        </div>
    </div>
@endif

<!-- 3. Incoming Assessment Notification Banner -->
@if(isset($receivedAssessmentsCount) && $receivedAssessmentsCount > 0)
    <div class="glass-alert-success shadow-sm d-flex align-items-center gap-3 p-3 mb-4">
        <div class="bg-success text-white rounded-circle p-2 d-flex align-items-center justify-content-center flex-shrink-0" style="width: 44px; height: 44px;">
            <i class="bi bi-bell-fill fs-5"></i>
        </div>
        <div>
            <div class="fw-bold text-success" style="font-size: 15px;">Notifikasi Penilaian Kinerja Masuk</div>
            <div class="text-secondary small mt-1">
                Sudah ada <strong class="text-dark">{{ $receivedAssessmentsCount }}</strong> rekan pegawai yang telah memberikan penilaian kepada Anda untuk periode ini.
            </div>
        </div>
    </div>
@endif

<!-- 4. Evaluation Task Cards (4 Executive Cards Grid) -->
<div class="row g-3 mb-4">
    <!-- Card 1: Total Tugas Penilaian -->
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="executive-card p-4 h-100">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <span class="text-muted fw-semibold small text-uppercase tracking-wider">Total Tugas</span>
                <div class="kpi-icon-wrapper bg-primary bg-opacity-10 text-primary">
                    <i class="bi bi-card-checklist"></i>
                </div>
            </div>
            <div class="stat-number text-dark mb-1">{{ $totalTugas ?? 0 }}</div>
            <div class="text-muted small">Target evaluasi disiapkan</div>
        </div>
    </div>

    <!-- Card 2: Sudah Diisi -->
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="executive-card p-4 h-100">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <span class="text-muted fw-semibold small text-uppercase tracking-wider">Sudah Diisi</span>
                <div class="kpi-icon-wrapper bg-success bg-opacity-10 text-success">
                    <i class="bi bi-check-circle"></i>
                </div>
            </div>
            <div class="stat-number text-success mb-1">{{ $submittedCount }}</div>
            <div class="text-muted small">Formulir selesai diserahkan</div>
        </div>
    </div>

    <!-- Card 3: Belum Diisi -->
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="executive-card p-4 h-100">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <span class="text-muted fw-semibold small text-uppercase tracking-wider">Belum Diisi</span>
                <div class="kpi-icon-wrapper bg-warning bg-opacity-10 text-warning">
                    <i class="bi bi-clock-history"></i>
                </div>
            </div>
            <div class="stat-number text-warning mb-1">{{ $pendingCount }}</div>
            <div class="text-muted small">Menunggu pengisian Anda</div>
        </div>
    </div>

    <!-- Card 4: Atasan Langsung -->
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="executive-card p-4 h-100">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <span class="text-muted fw-semibold small text-uppercase tracking-wider">Atasan Langsung</span>
                <div class="kpi-icon-wrapper bg-indigo bg-opacity-10 text-indigo" style="background-color: #E0E7FF; color: #4F46E5;">
                    <i class="bi bi-person-up"></i>
                </div>
            </div>
            <div class="fw-bold text-dark text-truncate fs-6 mb-1" title="{{ $user->supervisor->name ?? 'Belum Ditentukan' }}">
                {{ $user->supervisor->name ?? 'Belum Ditentukan' }}
            </div>
            <div class="text-muted small">NIP. {{ $user->supervisor->nip ?? '-' }}</div>
        </div>
    </div>
</div>

@endsection
