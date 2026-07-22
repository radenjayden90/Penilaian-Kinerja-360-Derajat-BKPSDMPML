@extends('layouts.app')

@section('title', 'Dashboard Pegawai')
@section('header', 'Dashboard Pegawai')
@section('subtitle', 'Portal Penilaian Kinerja 360 Derajat ASN Kabupaten Pemalang')

@section('content')
<!-- Welcome Banner -->
<div class="card border-0 shadow-sm mb-4" style="background: linear-gradient(135deg, #1E3A5F 0%, #152A45 100%); color: #FFFFFF;">
    <div class="card-body p-4">
        <div class="row align-items-center">
            <div class="col-12 col-md-8">
                <h4 class="fw-bold mb-1">Selamat Datang, {{ $user->name ?? 'Pegawai' }}!</h4>
                <p class="mb-2 text-white-50" style="font-size: 14px;">
                    NIP. {{ $user->nip ?? '-' }} | {{ $user->position->name ?? 'Jabatan Belum Diatur' }}
                </p>
                <div class="d-flex flex-wrap gap-2 mt-3">
                    <span class="badge bg-white text-dark px-3 py-2" style="font-weight: 500;">
                        <i class="bi bi-building me-1 text-primary"></i> {{ $user->department->name ?? 'Unit Kerja Belum Diatur' }}
                    </span>
                    <span class="badge bg-warning text-dark px-3 py-2" style="font-weight: 500;">
                        <i class="bi bi-person-badge me-1"></i> {{ $user->role->name ?? 'EMPLOYEE' }}
                    </span>
                </div>
            </div>
            <div class="col-12 col-md-4 text-md-end mt-3 mt-md-0">
                <a href="{{ route('transaction.assessments.index') }}" class="btn btn-light fw-semibold text-primary px-4 py-2">
                    <i class="bi bi-pencil-square me-2"></i>Mulai Penilaian
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Active Period Alert Banner -->
@if($activePeriod)
    <div class="alert alert-info border-0 shadow-sm d-flex align-items-center justify-content-between p-3 mb-4">
        <div class="d-flex align-items-center gap-3">
            <div class="bg-info text-white rounded-circle p-2 d-flex align-items-center justify-content-center" style="width: 42px; height: 42px;">
                <i class="bi bi-calendar-event fs-5"></i>
            </div>
            <div>
                <div class="fw-bold text-dark">Periode Penilaian Aktif: {{ $activePeriod->name }}</div>
                <small class="text-muted">
                    Jadwal: {{ \Carbon\Carbon::parse($activePeriod->start_date)->format('d M Y') }} s/d {{ \Carbon\Carbon::parse($activePeriod->end_date)->format('d M Y') }}
                </small>
            </div>
        </div>
        <span class="badge bg-success px-3 py-2">Aktif</span>
    </div>
@else
    <div class="alert alert-secondary border-0 shadow-sm d-flex align-items-center gap-3 p-3 mb-4">
        <i class="bi bi-info-circle fs-4 text-muted"></i>
        <div>
            <div class="fw-bold">Tidak Ada Periode Penilaian Aktif Saat Ini</div>
            <small class="text-muted">Silakan tunggu pemberitahuan dari BKPSDM untuk pembukaan periode penilaian kinerja berikutnya.</small>
        </div>
    </div>
@endif

<!-- Incoming Assessment Notification Banner -->
@if(isset($receivedAssessmentsCount) && $receivedAssessmentsCount > 0)
    <div class="alert alert-success border-0 shadow-sm d-flex align-items-center gap-3 p-3 mb-4">
        <div class="bg-success text-white rounded-circle p-2 d-flex align-items-center justify-content-center" style="width: 42px; height: 42px;">
            <i class="bi bi-bell-fill fs-5"></i>
        </div>
        <div>
            <div class="fw-bold text-success-emphasis" style="color: #0f5132 !important;">Notifikasi Penilaian Kinerja Masuk</div>
            <small class="text-muted">Sudah ada <strong>{{ $receivedAssessmentsCount }}</strong> user yang telah memberikan penilaian kepada Anda untuk periode ini.</small>
        </div>
    </div>
@endif

<!-- Evaluation Task Cards -->
<div class="row g-3 mb-4">
    <div class="col-12 col-sm-6 col-lg-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body p-3">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="fw-semibold text-muted" style="font-size: 13px;">Total Tugas Penilaian</span>
                    <i class="bi bi-card-checklist text-primary fs-4"></i>
                </div>
                <h3 class="fw-bold mb-0" style="color: #1E3A5F;">{{ $totalTugas ?? 0 }}</h3>
                <small class="text-muted">Target evaluasi disiapkan</small>
            </div>
        </div>
    </div>

    <div class="col-12 col-sm-6 col-lg-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body p-3">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="fw-semibold text-muted" style="font-size: 13px;">Sudah Diisi</span>
                    <i class="bi bi-check-circle text-success fs-4"></i>
                </div>
                <h3 class="fw-bold text-success mb-0">{{ $submittedCount }}</h3>
                <small class="text-muted">Formulir selesai diserahkan</small>
            </div>
        </div>
    </div>

    <div class="col-12 col-sm-6 col-lg-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body p-3">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="fw-semibold text-muted" style="font-size: 13px;">Belum Diisi</span>
                    <i class="bi bi-clock-history text-warning fs-4"></i>
                </div>
                <h3 class="fw-bold text-warning mb-0">{{ $pendingCount }}</h3>
                <small class="text-muted">Menunggu pengisian Anda</small>
            </div>
        </div>
    </div>

    <div class="col-12 col-sm-6 col-lg-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body p-3">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="fw-semibold text-muted" style="font-size: 13px;">Atasan Langsung</span>
                    <i class="bi bi-person-up text-info fs-4"></i>
                </div>
                <div class="fw-bold text-dark text-truncate" style="font-size: 14px;">
                    {{ $user->supervisor->name ?? 'Belum Ditentukan' }}
                </div>
                <small class="text-muted">NIP. {{ $user->supervisor->nip ?? '-' }}</small>
            </div>
        </div>
    </div>
</div>


@endsection
