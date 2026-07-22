@extends('layouts.app')

@section('title', 'Penilaian Saya')
@section('header', 'Penilaian Saya')
@section('subtitle', 'Daftar instrumen evaluasi kuesioner yang harus Anda isi pada periode aktif secara obyektif, transparan, dan akuntabel.')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active" aria-current="page">Penilaian Saya</li>
@endsection

@push('styles')
<style>
    .hero-banner-penilaian {
        background: linear-gradient(135deg, #1E40AF 0%, #2563EB 50%, #3B82F6 100%);
        border-radius: 20px;
        color: #FFFFFF;
        padding: 20px 28px;
        box-shadow: 0 10px 30px -5px rgba(37, 99, 235, 0.25);
        position: relative;
        overflow: hidden;
        animation: heroFadeIn 400ms ease-out forwards;
    }
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
        box-shadow: 0 12px 28px -4px rgba(37, 99, 235, 0.08);
        border-color: #CBD5E1;
    }

    .executive-card-header {
        padding: 20px 24px;
        border-bottom: 1px solid #F1F5F9;
        background: #FFFFFF;
        border-top-left-radius: 20px;
        border-top-right-radius: 20px;
    }

    .target-card {
        background: #FFFFFF;
        border: 1px solid var(--card-border);
        border-radius: 16px;
        transition: all 250ms cubic-bezier(0.4, 0, 0.2, 1);
    }

    .target-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 12px 24px -4px rgba(37, 99, 235, 0.12);
        border-color: #BFDBFE;
    }

    .target-card-muted {
        background: #F8FAFC;
        border: 1px solid #E2E8F0;
        border-radius: 16px;
        opacity: 0.82;
    }

    .kpi-stat-card {
        background: #FFFFFF;
        border: 1px solid var(--card-border);
        border-radius: 16px;
        padding: 16px 20px;
        box-shadow: 0 2px 10px rgba(15, 23, 42, 0.03);
        transition: all 200ms ease;
    }

    .kpi-stat-card:hover {
        transform: translateY(-2px);
        border-color: #CBD5E1;
        box-shadow: 0 6px 16px rgba(15, 23, 42, 0.06);
    }

    .kpi-icon-wrapper {
        width: 44px;
        height: 44px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.2rem;
    }

    .glass-alert {
        border-radius: 16px;
        border: 1px solid rgba(37, 99, 235, 0.15);
        background: #F0F6FF;
        padding: 18px 24px;
    }

    .glass-alert-success {
        border-radius: 16px;
        border: 1px solid rgba(22, 163, 74, 0.2);
        background: #F0FDF4;
        padding: 18px 24px;
    }

    .glass-alert-empty {
        border-radius: 16px;
        border: 1px solid #E2E8F0;
        background: #F8FAFC;
        padding: 24px;
    }

    .avatar-initial {
        width: 48px;
        height: 48px;
        min-width: 48px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 1.1rem;
    }

    .details-bg {
        background: #F8FAFC;
        border-radius: 12px;
        padding: 10px 14px;
        font-size: 13px;
    }
    .min-w-0 {
        min-width: 0 !important;
    }

    .badge-pill-custom {
        font-size: 12px;
        font-weight: 600;
        padding: 5px 12px;
        border-radius: 9999px;
        display: inline-flex;
        align-items: center;
        gap: 6px;
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
</style>
@endpush

@section('content')

@if(!$activePeriod)
    <div class="glass-alert-empty shadow-sm d-flex align-items-center gap-3 mb-4">
        <div class="bg-secondary bg-opacity-10 text-secondary rounded-circle p-2 d-flex align-items-center justify-content-center flex-shrink-0" style="width: 50px; height: 50px;">
            <i class="bi bi-calendar-x fs-3"></i>
        </div>
        <div>
            <h5 class="fw-bold text-dark mb-1">Tidak Ada Periode Penilaian Aktif</h5>
            <p class="text-muted small mb-0">Saat ini belum ada periode penilaian 360° yang sedang berlangsung. Silakan hubungi Administrator BKPSDM.</p>
        </div>
    </div>
@else
    <!-- Active Period Banner -->
    <div class="glass-alert shadow-sm d-flex flex-column flex-sm-row align-items-sm-center justify-content-between gap-3 mb-4">
        <div class="d-flex align-items-center gap-3">
            <div class="bg-primary bg-opacity-10 text-primary rounded-circle p-2 d-flex align-items-center justify-content-center flex-shrink-0" style="width: 46px; height: 46px;">
                <i class="bi bi-calendar-check fs-4"></i>
            </div>
            <div>
                <div class="fw-bold text-dark fs-6" style="color: #0F172A !important;">
                    Periode Penilaian Aktif: {{ $activePeriod->name }}
                </div>
                <div class="text-muted small mt-1">
                    <i class="bi bi-clock me-1"></i> Batas Akhir Pengisian: 
                    <strong>{{ \Carbon\Carbon::parse($activePeriod->end_date)->isoFormat('D MMMM Y') }}</strong> ({{ \Carbon\Carbon::parse($activePeriod->end_date)->format('H:i') }} WIB)
                </div>
            </div>
        </div>
        <div>
            <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-20 px-3 py-2 rounded-pill fw-semibold" style="font-size: 13px;">
                <i class="bi bi-check-circle-fill me-1"></i> Periode Aktif
            </span>
        </div>
    </div>

    @if(isset($isLimitReached) && $isLimitReached)
        <div class="glass-alert-success shadow-sm d-flex align-items-center gap-3 mb-4">
            <div class="bg-success text-white rounded-circle p-2 d-flex align-items-center justify-content-center flex-shrink-0" style="width: 44px; height: 44px;">
                <i class="bi bi-check2-all fs-4"></i>
            </div>
            <div>
                <div class="fw-bold text-success" style="font-size: 15px;">Seluruh Tugas Penilaian Telah Selesai</div>
                <div class="text-secondary small mt-1">Anda telah menyelesaikan seluruh instrumen penilaian Anda untuk periode ini (Total: <strong>{{ $totalTugas ?? 0 }}</strong> penilaian). Terima kasih atas partisipasi aktif Anda.</div>
            </div>
        </div>
    @endif

    <!-- Summary KPI Stat Cards -->
    @php
        $totTugas = $totalTugas ?? 0;
        $totSelesai = $submittedCount ?? 0;
        $totBelum = max(0, $totTugas - $totSelesai);
    @endphp
    <div class="row g-3 mb-4">
        <div class="col-12 col-sm-4">
            <div class="kpi-stat-card d-flex align-items-center gap-3">
                <div class="kpi-icon-wrapper bg-primary bg-opacity-10 text-primary">
                    <i class="bi bi-card-checklist"></i>
                </div>
                <div>
                    <div class="text-muted small fw-medium">Total Evaluasi Wajib</div>
                    <div class="fs-4 fw-bold text-dark lh-1 mt-1">{{ $totTugas }} <span class="fs-6 fw-normal text-muted">Orang</span></div>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-4">
            <div class="kpi-stat-card d-flex align-items-center gap-3">
                <div class="kpi-icon-wrapper bg-success bg-opacity-10 text-success">
                    <i class="bi bi-check-circle-fill"></i>
                </div>
                <div>
                    <div class="text-muted small fw-medium">Sudah Evaluasi</div>
                    <div class="fs-4 fw-bold text-success lh-1 mt-1">{{ $totSelesai }} <span class="fs-6 fw-normal text-muted">Selesai</span></div>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-4">
            <div class="kpi-stat-card d-flex align-items-center gap-3">
                <div class="kpi-icon-wrapper bg-warning bg-opacity-10 text-warning">
                    <i class="bi bi-hourglass-split"></i>
                </div>
                <div>
                    <div class="text-muted small fw-medium">Sisa Evaluasi</div>
                    <div class="fs-4 fw-bold text-warning lh-1 mt-1">{{ $totBelum }} <span class="fs-6 fw-normal text-muted">Belum Diisi</span></div>
                </div>
            </div>
        </div>
    </div>

    @php
        $posName = strtolower($employee->position?->name ?? '');
        $isLevel1 = $employee->position?->level == '1' || str_contains($posName, 'kepala bkpsdm');
        $isLevel2 = $employee->position?->level == '2' || str_contains($posName, 'kepala bidang') || str_contains($posName, 'kabid');
        $hideSuperior = $isLevel1 || $isLevel2;
        $hidePeers = $isLevel1;
    @endphp

    <!-- Section 1: Atasan Langsung -->
    @if(!$hideSuperior)
    <div class="executive-card mb-4">
        <div class="executive-card-header d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center gap-2">
                <div class="kpi-icon-wrapper bg-primary bg-opacity-10 text-primary" style="width: 36px; height: 36px; font-size: 1rem;">
                    <i class="bi bi-person-up"></i>
                </div>
                <div>
                    <h6 class="fw-bold text-dark mb-0">1. Evaluasi Atasan Langsung</h6>
                    <small class="text-muted">Instrumen evaluasi kinerja terhadap atasan hirarki Anda</small>
                </div>
            </div>
            <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-20 px-3 py-1 rounded-pill fw-semibold">
                Atasan Langsung
            </span>
        </div>
        <div class="card-body p-4">
            @if($superior)
                <div class="row row-cols-1 row-cols-md-2 row-cols-xl-2 g-3">
                    <div class="col">
                        <div class="target-card h-100 p-3 d-flex flex-column justify-content-between">
                            <div>
                                <div class="d-flex align-items-start gap-3 mb-3">
                                    <div class="avatar-initial bg-primary bg-opacity-10 text-primary">
                                        {{ strtoupper(substr($superior->name, 0, 1)) }}
                                    </div>
                                    <div class="flex-grow-1 min-w-0">
                                        <h6 class="fw-bold text-dark mb-1 lh-sm" style="font-size: 14.5px;" title="{{ $superior->name }}">{{ $superior->name }}</h6>
                                        <small class="text-muted d-block">NIP. {{ $superior->nip }}</small>
                                    </div>
                                </div>
                                <div class="details-bg mb-3">
                                    <div class="d-flex align-items-start mb-2">
                                        <div class="text-muted flex-shrink-0" style="width: 75px;"><i class="bi bi-briefcase me-1"></i>Jabatan</div>
                                        <div class="text-muted px-1">:</div>
                                        <div class="fw-semibold text-dark lh-sm">{{ $superior->position->name ?? '-' }}</div>
                                    </div>
                                    <div class="d-flex align-items-start">
                                        <div class="text-muted flex-shrink-0" style="width: 75px;"><i class="bi bi-building me-1"></i>Divisi</div>
                                        <div class="text-muted px-1">:</div>
                                        <div class="fw-semibold text-dark lh-sm">{{ $superior->department->name ?? '-' }}</div>
                                    </div>
                                </div>
                            </div>
                            <div>
                                <div class="d-flex align-items-center justify-content-between pt-2 mb-3 border-top">
                                    <span class="small text-muted">Status Evaluasi:</span>
                                    @if($superior->assessment_status === 'COMPLETED')
                                        <span class="badge-pill-custom bg-success bg-opacity-10 text-success border border-success border-opacity-20">
                                            <i class="bi bi-check-circle-fill"></i> Selesai
                                        </span>
                                    @else
                                        <span class="badge-pill-custom bg-warning bg-opacity-10 text-warning border border-warning border-opacity-20">
                                            <i class="bi bi-clock-history"></i> Belum Diisi
                                        </span>
                                    @endif
                                </div>
                                <div>
                                    @if($superior->assessment_status === 'COMPLETED')
                                        <button class="btn btn-sm btn-outline-secondary w-100 rounded-3 py-2 fw-semibold" disabled>
                                            <i class="bi bi-check-lg me-1"></i>Sudah Dinilai
                                        </button>
                                    @elseif(isset($isLimitReached) && $isLimitReached)
                                        <button class="btn btn-sm btn-outline-secondary w-100 rounded-3 py-2 fw-semibold" disabled>
                                            <i class="bi bi-lock-fill me-1"></i>Batas Tugas Terpenuhi
                                        </button>
                                    @else
                                        <a href="{{ route('transaction.assessments.create', ['target_id' => $superior->id, 'type' => 'SUPERIOR']) }}" class="btn btn-sm btn-primary w-100 rounded-3 py-2 fw-semibold shadow-sm btn-isi-penilaian" data-target-id="{{ $superior->id }}">
                                            <i class="bi bi-pencil-square me-1"></i>Isi Penilaian
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <div class="text-center text-muted py-4">
                    <div class="bg-light rounded-circle d-inline-flex align-items-center justify-content-center mb-2" style="width: 54px; height: 54px;">
                        <i class="bi bi-person-x fs-3 text-secondary"></i>
                    </div>
                    <div class="fw-semibold text-dark mb-1">Belum Ada Atasan Terdaftar</div>
                    <small class="text-muted">Anda tidak memiliki atasan langsung terdaftar pada sistem.</small>
                </div>
            @endif
        </div>
    </div>
    @endif

    <!-- Section 2: Rekan Sejawat -->
    @if(!$hidePeers)
    <div id="section-peers" class="executive-card mb-4" style="scroll-margin-top: 100px;">
        <div class="executive-card-header d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center gap-2">
                <div class="kpi-icon-wrapper bg-info bg-opacity-10 text-info" style="width: 36px; height: 36px; font-size: 1rem;">
                    <i class="bi bi-people"></i>
                </div>
                <div>
                    <h6 class="fw-bold text-dark mb-0">2. Evaluasi Rekan Sejawat (Peers)</h6>
                    <small class="text-muted">Daftar rekan kerja (termasuk lintas bidang). Kuota maksimal 3 penilai per pegawai.</small>
                </div>
            </div>
            <span class="badge bg-info bg-opacity-10 text-info border border-info border-opacity-20 px-3 py-1 rounded-pill fw-semibold">
                {{ $peers->total() }} Orang Rekan
            </span>
        </div>
        <div class="card-body p-4">
            <div class="row row-cols-1 row-cols-md-2 row-cols-xl-2 g-3">
                @forelse($peers as $peer)
                    <div class="col">
                        @if($peer->assessment_status === 'FULL' || $peer->assessment_status === 'LIMIT_REACHED')
                            {{-- Greyed-out / Muted Card --}}
                            <div class="target-card-muted h-100 p-3 d-flex flex-column justify-content-between">
                                <div>
                                    <div class="d-flex align-items-start gap-3 mb-3">
                                        <div class="avatar-initial bg-secondary bg-opacity-20 text-secondary">
                                            {{ strtoupper(substr($peer->name, 0, 1)) }}
                                        </div>
                                        <div class="flex-grow-1 min-w-0">
                                            <h6 class="fw-bold text-secondary mb-1 lh-sm" style="font-size: 14.5px;" title="{{ $peer->name }}">{{ $peer->name }}</h6>
                                            <small class="text-muted d-block">NIP. {{ $peer->nip }}</small>
                                        </div>
                                    </div>
                                    <div class="details-bg mb-3">
                                        <div class="d-flex align-items-start mb-2">
                                            <div class="text-muted flex-shrink-0" style="width: 75px;"><i class="bi bi-briefcase me-1"></i>Jabatan</div>
                                            <div class="text-muted px-1">:</div>
                                            <div class="fw-semibold text-secondary lh-sm">{{ $peer->position->name ?? '-' }}</div>
                                        </div>
                                        <div class="d-flex align-items-start">
                                            <div class="text-muted flex-shrink-0" style="width: 75px;"><i class="bi bi-building me-1"></i>Divisi</div>
                                            <div class="text-muted px-1">:</div>
                                            <div class="fw-semibold text-secondary lh-sm">{{ $peer->department->name ?? '-' }}</div>
                                        </div>
                                    </div>
                                </div>
                                <div>
                                    <div class="d-flex align-items-center justify-content-between pt-2 mb-3 border-top border-secondary-subtle">
                                        <small class="text-muted">
                                            <i class="bi bi-people me-1"></i>Penilai: <strong>{{ $peer->received_assessments_count ?? 0 }}</strong>
                                        </small>
                                        @if($peer->assessment_status === 'FULL')
                                            <span class="badge-pill-custom bg-secondary bg-opacity-20 text-secondary">
                                                <i class="bi bi-lock-fill"></i> Kuota Penuh
                                            </span>
                                        @else
                                            <span class="badge-pill-custom bg-secondary bg-opacity-20 text-secondary">
                                                <i class="bi bi-lock-fill"></i> Batas Terpenuhi
                                            </span>
                                        @endif
                                    </div>
                                    <div>
                                        <button class="btn btn-sm btn-secondary w-100 rounded-3 py-2 fw-semibold opacity-75" disabled>
                                            @if($peer->assessment_status === 'FULL')
                                                <i class="bi bi-lock me-1"></i>Kuota Terpenuhi
                                            @else
                                                <i class="bi bi-lock me-1"></i>Batas 3 Rekan Terpenuhi
                                            @endif
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @else
                            {{-- Active / Completed Card --}}
                            <div class="target-card h-100 p-3 d-flex flex-column justify-content-between">
                                <div>
                                    <div class="d-flex align-items-start gap-3 mb-3">
                                        <div class="avatar-initial bg-info bg-opacity-10 text-info">
                                            {{ strtoupper(substr($peer->name, 0, 1)) }}
                                        </div>
                                        <div class="flex-grow-1 min-w-0">
                                            <h6 class="fw-bold text-dark mb-1 lh-sm" style="font-size: 14.5px;" title="{{ $peer->name }}">{{ $peer->name }}</h6>
                                            <small class="text-muted d-block">NIP. {{ $peer->nip }}</small>
                                        </div>
                                    </div>
                                    <div class="details-bg mb-3">
                                        <div class="d-flex align-items-start mb-2">
                                            <div class="text-muted flex-shrink-0" style="width: 75px;"><i class="bi bi-briefcase me-1"></i>Jabatan</div>
                                            <div class="text-muted px-1">:</div>
                                            <div class="fw-semibold text-dark lh-sm">{{ $peer->position->name ?? '-' }}</div>
                                        </div>
                                        <div class="d-flex align-items-start">
                                            <div class="text-muted flex-shrink-0" style="width: 75px;"><i class="bi bi-building me-1"></i>Divisi</div>
                                            <div class="text-muted px-1">:</div>
                                            <div class="fw-semibold text-dark lh-sm">{{ $peer->department->name ?? '-' }}</div>
                                        </div>
                                    </div>
                                </div>
                                <div>
                                    <div class="d-flex align-items-center justify-content-between pt-2 mb-3 border-top">
                                        <small class="text-muted">
                                            <i class="bi bi-people me-1"></i>Penilai: <strong>{{ $peer->received_assessments_count ?? 0 }}</strong>
                                        </small>
                                        @if($peer->assessment_status === 'COMPLETED')
                                            <span class="badge-pill-custom bg-success bg-opacity-10 text-success border border-success border-opacity-20">
                                                <i class="bi bi-check-circle-fill"></i> Selesai
                                            </span>
                                        @else
                                            <span class="badge-pill-custom bg-warning bg-opacity-10 text-warning border border-warning border-opacity-20">
                                                <i class="bi bi-clock-history"></i> Belum Diisi
                                            </span>
                                        @endif
                                    </div>
                                    <div>
                                        @if($peer->assessment_status === 'COMPLETED')
                                            <button class="btn btn-sm btn-outline-secondary w-100 rounded-3 py-2 fw-semibold" disabled>
                                                <i class="bi bi-check-lg me-1"></i>Sudah Dinilai
                                            </button>
                                        @elseif(isset($isLimitReached) && $isLimitReached)
                                            <button class="btn btn-sm btn-outline-secondary w-100 rounded-3 py-2 fw-semibold" disabled>
                                                <i class="bi bi-lock-fill me-1"></i>Batas Tugas Terpenuhi
                                            </button>
                                        @else
                                            <a href="{{ route('transaction.assessments.create', ['target_id' => $peer->id, 'type' => 'PEER']) }}" class="btn btn-sm btn-primary w-100 rounded-3 py-2 fw-semibold shadow-sm btn-isi-penilaian" data-target-id="{{ $peer->id }}">
                                                <i class="bi bi-pencil-square me-1"></i>Isi Penilaian
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                @empty
                    <div class="col-12 w-100">
                        <div class="text-center text-muted py-4">
                            <div class="bg-light rounded-circle d-inline-flex align-items-center justify-content-center mb-2" style="width: 54px; height: 54px;">
                                <i class="bi bi-people fs-3 text-secondary"></i>
                            </div>
                            <div class="fw-semibold text-dark mb-1">Tidak Ada Rekan Kerja Terdaftar</div>
                            <small class="text-muted">Tidak ditemukan rekan kerja eligible pada periode ini.</small>
                        </div>
                    </div>
                @endforelse
            </div>
            @if($peers->hasPages())
                <div class="mt-4 d-flex justify-content-center">
                    {{ $peers->appends(['subs_page' => request('subs_page')])->fragment('section-peers')->links('pagination::bootstrap-5') }}
                </div>
            @endif
        </div>
    </div>
    @endif

    <!-- Section 3: Bawahan (Jika Ada) -->
    @if($subordinates->total() > 0)
        <div id="section-subordinates" class="executive-card mb-4" style="scroll-margin-top: 100px;">
            <div class="executive-card-header d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center gap-2">
                    <div class="kpi-icon-wrapper bg-success bg-opacity-10 text-success" style="width: 36px; height: 36px; font-size: 1rem;">
                        <i class="bi bi-person-down"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold text-dark mb-0">3. Evaluasi Bawahan Langsung</h6>
                        <small class="text-muted">Daftar staf/bawahan yang berada di bawah kepemimpinan Anda</small>
                    </div>
                </div>
                <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-20 px-3 py-1 rounded-pill fw-semibold">
                    {{ $subordinates->total() }} Bawahan
                </span>
            </div>
            <div class="card-body p-4">
                <div class="row row-cols-1 row-cols-md-2 row-cols-xl-2 g-3">
                    @foreach($subordinates as $sub)
                        <div class="col">
                            <div class="target-card h-100 p-3 d-flex flex-column justify-content-between">
                                <div>
                                    <div class="d-flex align-items-start gap-3 mb-3">
                                        <div class="avatar-initial bg-success bg-opacity-10 text-success">
                                            {{ strtoupper(substr($sub->name, 0, 1)) }}
                                        </div>
                                        <div class="flex-grow-1 min-w-0">
                                            <h6 class="fw-bold text-dark mb-1 lh-sm" style="font-size: 14.5px;" title="{{ $sub->name }}">{{ $sub->name }}</h6>
                                            <small class="text-muted d-block">NIP. {{ $sub->nip }}</small>
                                        </div>
                                    </div>
                                    <div class="details-bg mb-3">
                                        <div class="d-flex align-items-start mb-2">
                                            <div class="text-muted flex-shrink-0" style="width: 75px;"><i class="bi bi-briefcase me-1"></i>Jabatan</div>
                                            <div class="text-muted px-1">:</div>
                                            <div class="fw-semibold text-dark lh-sm">{{ $sub->position->name ?? '-' }}</div>
                                        </div>
                                        <div class="d-flex align-items-start">
                                            <div class="text-muted flex-shrink-0" style="width: 75px;"><i class="bi bi-building me-1"></i>Divisi</div>
                                            <div class="text-muted px-1">:</div>
                                            <div class="fw-semibold text-dark lh-sm">{{ $sub->department->name ?? '-' }}</div>
                                        </div>
                                    </div>
                                </div>
                                <div>
                                    <div class="d-flex align-items-center justify-content-between pt-2 mb-3 border-top">
                                        <span class="small text-muted">Status Evaluasi:</span>
                                        @if($sub->assessment_status === 'COMPLETED')
                                            <span class="badge-pill-custom bg-success bg-opacity-10 text-success border border-success border-opacity-20">
                                                <i class="bi bi-check-circle-fill"></i> Selesai
                                            </span>
                                        @else
                                            <span class="badge-pill-custom bg-warning bg-opacity-10 text-warning border border-warning border-opacity-20">
                                                <i class="bi bi-clock-history"></i> Belum Diisi
                                            </span>
                                        @endif
                                    </div>
                                    <div>
                                        @if($sub->assessment_status === 'COMPLETED')
                                            <button class="btn btn-sm btn-outline-secondary w-100 rounded-3 py-2 fw-semibold" disabled>
                                                <i class="bi bi-check-lg me-1"></i>Sudah Dinilai
                                            </button>
                                        @elseif(isset($isLimitReached) && $isLimitReached)
                                            <button class="btn btn-sm btn-outline-secondary w-100 rounded-3 py-2 fw-semibold" disabled>
                                                <i class="bi bi-lock-fill me-1"></i>Batas Tugas Terpenuhi
                                            </button>
                                        @else
                                            <a href="{{ route('transaction.assessments.create', ['target_id' => $sub->id, 'type' => 'SUBORDINATE']) }}" class="btn btn-sm btn-primary w-100 rounded-3 py-2 fw-semibold shadow-sm btn-isi-penilaian" data-target-id="{{ $sub->id }}">
                                                <i class="bi bi-pencil-square me-1"></i>Isi Penilaian
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                @if($subordinates->hasPages())
                    <div class="mt-4 d-flex justify-content-center">
                        {{ $subordinates->appends(['peers_page' => request('peers_page')])->fragment('section-subordinates')->links('pagination::bootstrap-5') }}
                    </div>
                @endif
            </div>
        </div>
    @endif
@endif

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const isiButtons = document.querySelectorAll('.btn-isi-penilaian');
    isiButtons.forEach(btn => {
        const targetId = btn.dataset.targetId;
        const draftKey = 'draft_assessment_' + targetId;
        if (localStorage.getItem(draftKey)) {
            btn.classList.remove('btn-primary', 'shadow-sm');
            btn.classList.add('btn-warning', 'text-dark', 'fw-bold', 'shadow-sm');
            btn.innerHTML = '<i class="bi bi-play-circle-fill me-1"></i>Lanjutkan Penilaian';
            
            const card = btn.closest('.target-card');
            if (card) {
                card.style.border = '2px dashed #F59E0B';
                card.style.backgroundColor = '#FFFDF5';
                
                const statusBadge = card.querySelector('.badge-pill-custom');
                if (statusBadge) {
                    statusBadge.className = 'badge-pill-custom bg-warning text-dark border border-warning font-weight-bold';
                    statusBadge.innerHTML = '<i class="bi bi-file-earmark-diff-fill me-1"></i>Draft Tersimpan';
                }
            }
        }
    });
});
</script>
@endpush
@endsection
