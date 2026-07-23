@extends('layouts.app')

@section('title', 'Dashboard Admin')
@section('header', 'Dashboard Administrator')
@section('subtitle', 'Overview statistik master data dan status penilaian kinerja 360° ASN BKPSDM Kabupaten Pemalang')

@section('content')
<!-- Executive Hero Banner -->
<div class="card border-0 rounded-4 shadow-sm mb-4 overflow-hidden" style="background: linear-gradient(135deg, #0F172A 0%, #1E3A5F 55%, #2563EB 100%);">
    <div class="card-body p-4 p-lg-5 text-white position-relative">
        <div class="row align-items-center g-3">
            <div class="col-12 col-lg-8">
                <div class="d-inline-flex align-items-center gap-2 px-3 py-1.5 rounded-pill bg-white bg-opacity-10 text-white border border-white border-opacity-20 mb-3" style="backdrop-filter: blur(10px); font-size: 12px; font-weight: 600; letter-spacing: 0.5px;">
                    <i class="bi bi-shield-lock-fill text-warning"></i> BKPSDM KABUPATEN PEMALANG • PANEL ADMINISTRATOR
                </div>
                <h2 class="fw-extrabold text-white mb-2" style="font-size: 26px; letter-spacing: -0.5px;">
                    Selamat Datang, Administrator 👋
                </h2>
                <p class="text-white text-opacity-80 mb-0" style="font-size: 14px; max-width: 680px; line-height: 1.6;">
                    Kelola dan pantau seluruh instrumen penilaian kinerja 360 derajat ASN, data pegawai, unit kerja, indikator evaluasi, dan kalkulasi hasil akhir secara real-time.
                </p>
            </div>
            <div class="col-12 col-lg-4 text-lg-end">
                <div class="d-inline-flex flex-column align-items-lg-end p-3 rounded-4 bg-white bg-opacity-10 border border-white border-opacity-15" style="backdrop-filter: blur(10px);">
                    <span class="text-white text-opacity-75 small mb-1">
                        <i class="bi bi-calendar-event me-1"></i> Periode Penilaian Aktif
                    </span>
                    <span class="fw-bold text-white fs-6 mb-1">
                        {{ $stats['active_period']->name ?? 'Tidak Ada Periode Aktif' }}
                    </span>
                    @if(isset($stats['active_period']) && $stats['active_period']->end_date)
                        <span class="badge bg-success bg-opacity-20 text-success-light border border-success border-opacity-30 rounded-pill px-3 py-1" style="font-size: 11px;">
                            <i class="bi bi-clock me-1"></i> s/d {{ \Carbon\Carbon::parse($stats['active_period']->end_date)->locale('id')->isoFormat('D MMMM Y') }}
                        </span>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Executive Row Stat Cards -->
<div class="row g-3 mb-4">
    <!-- Stat 1: Total Pegawai -->
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card h-100 border-0 rounded-4 shadow-sm hover-lift" style="border-top: 4px solid #2563EB !important;">
            <div class="card-body p-4">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-uppercase fw-bold text-muted" style="font-size: 11px; letter-spacing: 0.5px;">Total Pegawai ASN</span>
                        <h3 class="fw-extrabold mb-0 mt-2" style="color: #0F172A; font-size: 28px;">{{ number_format($stats['total_pegawai']) }}</h3>
                        <div class="mt-3">
                            <a href="{{ route('master.employees.index') }}" class="text-decoration-none text-primary fw-semibold small d-inline-flex align-items-center">
                                Kelola Pegawai <i class="bi bi-arrow-right ms-1 transition-icon"></i>
                            </a>
                        </div>
                    </div>
                    <div class="rounded-4 p-3 d-flex align-items-center justify-content-center" style="width: 56px; height: 56px; background: #EFF6FF; color: #2563EB;">
                        <i class="bi bi-people-fill fs-3"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Stat 2: Unit Kerja / Bidang -->
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card h-100 border-0 rounded-4 shadow-sm hover-lift" style="border-top: 4px solid #0EA5E9 !important;">
            <div class="card-body p-4">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-uppercase fw-bold text-muted" style="font-size: 11px; letter-spacing: 0.5px;">Unit Kerja / Bidang</span>
                        <h3 class="fw-extrabold mb-0 mt-2" style="color: #0F172A; font-size: 28px;">{{ number_format($stats['total_department']) }}</h3>
                        <div class="mt-3">
                            <a href="{{ route('master.departments.index') }}" class="text-decoration-none text-info fw-semibold small d-inline-flex align-items-center">
                                Lihat Unit Kerja <i class="bi bi-arrow-right ms-1 transition-icon"></i>
                            </a>
                        </div>
                    </div>
                    <div class="rounded-4 p-3 d-flex align-items-center justify-content-center" style="width: 56px; height: 56px; background: #E0F2FE; color: #0EA5E9;">
                        <i class="bi bi-building fs-3"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Stat 3: Master Jabatan -->
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card h-100 border-0 rounded-4 shadow-sm hover-lift" style="border-top: 4px solid #8B5CF6 !important;">
            <div class="card-body p-4">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-uppercase fw-bold text-muted" style="font-size: 11px; letter-spacing: 0.5px;">Master Jabatan</span>
                        <h3 class="fw-extrabold mb-0 mt-2" style="color: #0F172A; font-size: 28px;">{{ number_format($stats['total_position']) }}</h3>
                        <div class="mt-3">
                            <a href="{{ route('master.positions.index') }}" class="text-decoration-none text-purple fw-semibold small d-inline-flex align-items-center" style="color: #8B5CF6;">
                                Lihat Jabatan <i class="bi bi-arrow-right ms-1 transition-icon"></i>
                            </a>
                        </div>
                    </div>
                    <div class="rounded-4 p-3 d-flex align-items-center justify-content-center" style="width: 56px; height: 56px; background: #F3E8FF; color: #8B5CF6;">
                        <i class="bi bi-person-badge fs-3"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Stat 4: Periode Penilaian -->
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card h-100 border-0 rounded-4 shadow-sm hover-lift" style="border-top: 4px solid #10B981 !important;">
            <div class="card-body p-4">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-uppercase fw-bold text-muted" style="font-size: 11px; letter-spacing: 0.5px;">Status Periode</span>
                        <h3 class="fw-bold mb-0 mt-2 text-truncate" style="color: #0F172A; font-size: 18px; max-width: 140px;">
                            {{ $stats['active_period']->name ?? 'Belum Aktif' }}
                        </h3>
                        <div class="mt-3">
                            <a href="{{ route('master.periods.index') }}" class="text-decoration-none text-success fw-semibold small d-inline-flex align-items-center">
                                Kelola Periode <i class="bi bi-arrow-right ms-1 transition-icon"></i>
                            </a>
                        </div>
                    </div>
                    <div class="rounded-4 p-3 d-flex align-items-center justify-content-center" style="width: 56px; height: 56px; background: #D1FAE5; color: #10B981;">
                        <i class="bi bi-calendar-check fs-3"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Performance Distribution Breakdown Cards -->
<div class="row g-3 mb-4">
    <div class="col-12">
        <div class="card border-0 rounded-4 shadow-sm">
            <div class="card-header bg-white py-3 px-4 fw-bold border-bottom-0 d-flex align-items-center justify-content-between">
                <span class="d-flex align-items-center gap-2 text-dark" style="font-size: 15px;">
                    <i class="bi bi-bar-chart-line-fill text-primary"></i> Distribusi Predikat Kinerja ASN Periode Aktif
                </span>
                <span class="badge bg-slate-100 text-slate-600 rounded-pill px-3 py-1" style="font-size: 11px;">
                    Kalkulasi BerAKHLAK 360°
                </span>
            </div>
            <div class="card-body px-4 pb-4 pt-0">
<<<<<<< HEAD
                <div class="row g-3">
                    <!-- Sangat Baik -->
                    <div class="col-12 col-sm-6 col-xl-3">
                        <div class="p-4 rounded-4 bg-white border h-100 position-relative overflow-hidden hover-lift" style="border-color: #A7F3D0 !important; box-shadow: 0 4px 12px rgba(16, 185, 129, 0.05);">
                            <div class="d-flex align-items-center gap-2 mb-3 position-relative z-1">
                                <div class="bg-success bg-opacity-10 text-success rounded-3 p-2 d-flex align-items-center justify-content-center" style="width: 34px; height: 34px;">
                                    <i class="bi bi-stars fs-6"></i>
                                </div>
                                <span class="fw-bold text-success" style="font-size: 13px; text-transform: uppercase; letter-spacing: 0.5px;">Sangat Baik</span>
                            </div>
                            <h3 class="fw-extrabold text-dark mb-1 position-relative z-1" style="font-size: 34px; letter-spacing: -1px;">{{ $categoryStats['sangat_baik'] ?? 0 }} <span class="text-muted fw-medium" style="font-size: 14px; letter-spacing: 0;">ASN</span></h3>
                            <div class="text-muted position-relative z-1" style="font-size: 12px; font-weight: 500;">Performa melampaui target</div>
                        </div>
                    </div>
                    
                    <!-- Baik -->
                    <div class="col-12 col-sm-6 col-xl-3">
                        <div class="p-4 rounded-4 bg-white border h-100 position-relative overflow-hidden hover-lift" style="border-color: #BFDBFE !important; box-shadow: 0 4px 12px rgba(37, 99, 235, 0.05);">
                            <div class="d-flex align-items-center gap-2 mb-3 position-relative z-1">
                                <div class="bg-primary bg-opacity-10 text-primary rounded-3 p-2 d-flex align-items-center justify-content-center" style="width: 34px; height: 34px;">
                                    <i class="bi bi-hand-thumbs-up-fill fs-6"></i>
                                </div>
                                <span class="fw-bold text-primary" style="font-size: 13px; text-transform: uppercase; letter-spacing: 0.5px;">Baik</span>
                            </div>
                            <h3 class="fw-extrabold text-dark mb-1 position-relative z-1" style="font-size: 34px; letter-spacing: -1px;">{{ $categoryStats['baik'] ?? 0 }} <span class="text-muted fw-medium" style="font-size: 14px; letter-spacing: 0;">ASN</span></h3>
                            <div class="text-muted position-relative z-1" style="font-size: 12px; font-weight: 500;">Sesuai dengan ekspektasi</div>
                        </div>
                    </div>

                    <!-- Cukup -->
                    <div class="col-12 col-sm-6 col-xl-3">
                        <div class="p-4 rounded-4 bg-white border h-100 position-relative overflow-hidden hover-lift" style="border-color: #FDE68A !important; box-shadow: 0 4px 12px rgba(245, 158, 11, 0.05);">
                            <div class="d-flex align-items-center gap-2 mb-3 position-relative z-1">
                                <div class="bg-warning bg-opacity-10 text-warning rounded-3 p-2 d-flex align-items-center justify-content-center" style="width: 34px; height: 34px; color: #D97706 !important;">
                                    <i class="bi bi-exclamation-circle-fill fs-6"></i>
                                </div>
                                <span class="fw-bold text-warning" style="font-size: 13px; text-transform: uppercase; letter-spacing: 0.5px; color: #D97706 !important;">Cukup</span>
                            </div>
                            <h3 class="fw-extrabold text-dark mb-1 position-relative z-1" style="font-size: 34px; letter-spacing: -1px;">{{ $categoryStats['cukup'] ?? 0 }} <span class="text-muted fw-medium" style="font-size: 14px; letter-spacing: 0;">ASN</span></h3>
                            <div class="text-muted position-relative z-1" style="font-size: 12px; font-weight: 500;">Butuh peningkatan kapasitas</div>
                        </div>
                    </div>

                    <!-- Kurang -->
                    <div class="col-12 col-sm-6 col-xl-3">
                        <div class="p-4 rounded-4 bg-white border h-100 position-relative overflow-hidden hover-lift" style="border-color: #FECDD3 !important; box-shadow: 0 4px 12px rgba(225, 29, 72, 0.05);">
                            <div class="d-flex align-items-center gap-2 mb-3 position-relative z-1">
                                <div class="bg-danger bg-opacity-10 text-danger rounded-3 p-2 d-flex align-items-center justify-content-center" style="width: 34px; height: 34px;">
                                    <i class="bi bi-shield-x fs-6"></i>
                                </div>
                                <span class="fw-bold text-danger" style="font-size: 13px; text-transform: uppercase; letter-spacing: 0.5px;">Kurang</span>
                            </div>
                            <h3 class="fw-extrabold text-dark mb-1 position-relative z-1" style="font-size: 34px; letter-spacing: -1px;">{{ $categoryStats['kurang'] ?? 0 }} <span class="text-muted fw-medium" style="font-size: 14px; letter-spacing: 0;">ASN</span></h3>
                            <div class="text-muted position-relative z-1" style="font-size: 12px; font-weight: 500;">Perlu pembinaan khusus</div>
=======
                <div class="row text-center g-3">
                    <div class="col-6 col-md-3">
                        <div class="p-3.5 rounded-4 bg-emerald-50 border border-emerald-200">
                            <div class="d-flex align-items-center justify-content-center mb-1 text-emerald-700 fw-bold" style="font-size: 12px; text-transform: uppercase;">
                                <i class="bi bi-stars me-2 fs-6"></i> Sangat Baik
                            </div>
                            <h3 class="fw-extrabold text-emerald-600 mb-0 mt-1" style="font-size: 26px;">{{ $categoryStats['sangat_baik'] ?? 0 }}</h3>
                            <small class="text-emerald-600 opacity-75 d-block mt-0.5" style="font-size: 11px;">ASN Terbanyak</small>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="p-3.5 rounded-4 bg-blue-50 border border-blue-200">
                            <div class="d-flex align-items-center justify-content-center mb-1 text-blue-700 fw-bold" style="font-size: 12px; text-transform: uppercase;">
                                <i class="bi bi-hand-thumbs-up-fill me-2 fs-6"></i> Baik
                            </div>
                            <h3 class="fw-extrabold text-blue-600 mb-0 mt-1" style="font-size: 26px;">{{ $categoryStats['baik'] ?? 0 }}</h3>
                            <small class="text-blue-600 opacity-75 d-block mt-0.5" style="font-size: 11px;">Sesuai Ekspektasi</small>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="p-3.5 rounded-4 bg-amber-50 border border-amber-200">
                            <div class="d-flex align-items-center justify-content-center mb-1 text-amber-700 fw-bold" style="font-size: 12px; text-transform: uppercase;">
                                <i class="bi bi-exclamation-circle-fill me-2 fs-6"></i> Cukup
                            </div>
                            <h3 class="fw-extrabold text-amber-600 mb-0 mt-1" style="font-size: 26px;">{{ $categoryStats['cukup'] ?? 0 }}</h3>
                            <small class="text-amber-600 opacity-75 d-block mt-0.5" style="font-size: 11px;">Butuh Peningkatan</small>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="p-3.5 rounded-4 bg-rose-50 border border-rose-200">
                            <div class="d-flex align-items-center justify-content-center mb-1 text-rose-700 fw-bold" style="font-size: 12px; text-transform: uppercase;">
                                <i class="bi bi-shield-x me-2 fs-6"></i> Kurang
                            </div>
                            <h3 class="fw-extrabold text-rose-600 mb-0 mt-1" style="font-size: 26px;">{{ $categoryStats['kurang'] ?? 0 }}</h3>
                            <small class="text-rose-600 opacity-75 d-block mt-0.5" style="font-size: 11px;">Perlu Pembinaan</small>
>>>>>>> 2924dbec52fc53cd83008afe4458c285212c265c
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- Quick Access Actions -->
    <div class="col-12 col-lg-4">
        <div class="card border-0 rounded-4 shadow-sm h-100">
            <div class="card-header bg-white py-3 px-4 border-bottom fw-bold text-dark d-flex align-items-center justify-content-between">
                <span><i class="bi bi-lightning-charge-fill text-warning me-2"></i>Aksi Cepat Admin</span>
                <span class="badge bg-amber-100 text-amber-800 rounded-pill px-2.5 py-1" style="font-size: 10px;">Shortcut</span>
            </div>
            <div class="card-body p-3 d-flex flex-column">
                <div class="d-flex flex-column gap-2 h-100 justify-content-between">
                    <a href="{{ route('master.employees.index') }}" class="px-3 py-2 rounded-3 d-flex align-items-center gap-2 text-decoration-none action-shortcut-item h-100">
                        <div class="rounded-3 p-2 d-flex align-items-center justify-content-center bg-primary text-white" style="width: 36px; height: 36px; background: linear-gradient(135deg, #1E40AF 0%, #2563EB 100%) !important;">
                            <i class="bi bi-person-plus-fill" style="font-size: 1.1rem;"></i>
                        </div>
                        <div>
                            <div class="fw-bold text-dark mb-0" style="font-size: 13px;">Kelola Data Pegawai</div>
                            <small class="text-muted" style="font-size: 11px;">Tambah, edit & atur ASN Pemalang</small>
                        </div>
                    </a>

                    <a href="{{ route('master.periods.index') }}" class="px-3 py-2 rounded-3 d-flex align-items-center gap-2 text-decoration-none action-shortcut-item h-100">
                        <div class="rounded-3 p-2 d-flex align-items-center justify-content-center bg-success text-white" style="width: 36px; height: 36px; background: linear-gradient(135deg, #059669 0%, #10B981 100%) !important;">
                            <i class="bi bi-plus-circle-fill" style="font-size: 1.1rem;"></i>
                        </div>
                        <div>
                            <div class="fw-bold text-dark mb-0" style="font-size: 13px;">Buka Periode Penilaian</div>
                            <small class="text-muted" style="font-size: 11px;">Atur jadwal penilaian</small>
                        </div>
                    </a>

                    <a href="{{ route('master.assessment-indicators.index') }}" class="px-3 py-2 rounded-3 d-flex align-items-center gap-2 text-decoration-none action-shortcut-item h-100">
                        <div class="rounded-3 p-2 d-flex align-items-center justify-content-center bg-info text-white" style="width: 36px; height: 36px; background: linear-gradient(135deg, #0284C7 0%, #0EA5E9 100%) !important;">
                            <i class="bi bi-card-checklist" style="font-size: 1.1rem;"></i>
                        </div>
                        <div>
                            <div class="fw-bold text-dark mb-0" style="font-size: 13px;">Indikator Penilaian 360°</div>
                            <small class="text-muted" style="font-size: 11px;">Atur instrumen & indikator kuesioner</small>
                        </div>
                    </a>

                    <a href="{{ route('transaction.calculations.index') }}" class="px-3 py-2 rounded-3 d-flex align-items-center gap-2 text-decoration-none action-shortcut-item h-100">
                        <div class="rounded-3 p-2 d-flex align-items-center justify-content-center bg-warning text-dark" style="width: 36px; height: 36px; background: linear-gradient(135deg, #D97706 0%, #F59E0B 100%) !important; color: #FFF !important;">
                            <i class="bi bi-calculator-fill" style="font-size: 1.1rem;"></i>
                        </div>
                        <div>
                            <div class="fw-bold text-dark mb-0" style="font-size: 13px;">Hitung Hasil Penilaian</div>
                            <small class="text-muted" style="font-size: 11px;">Kalkulasi skor akhir 360 derajat</small>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Top 5 Employees by Score Table -->
    <div class="col-12 col-lg-8">
        <div class="card border-0 rounded-4 shadow-sm h-100">
            <div class="card-header bg-white py-3 px-4 border-bottom d-flex align-items-center justify-content-between">
                <span class="fw-bold text-dark" style="font-size: 15px;">
                    <i class="bi bi-trophy-fill text-amber-500 me-2"></i>5 Pegawai Skor Kinerja Tertinggi
                </span>
                <a href="{{ route('transaction.calculations.index') }}" class="btn btn-sm btn-outline-primary rounded-pill px-3 fw-semibold" style="font-size: 12px;">
                    Lihat Semua <i class="bi bi-arrow-right ms-1"></i>
                </a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-slate-50 text-slate-600" style="font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px;">
                            <tr>
                                <th class="ps-4 py-3 text-center" style="width: 70px;">Peringkat</th>
                                <th class="py-3 text-center" style="min-width: 220px;">Nama Pegawai</th>
                                <th class="py-3 text-center" style="min-width: 150px;">NIP</th>
                                <th class="py-3 text-center" style="min-width: 280px;">Unit Kerja</th>
                                <th class="text-center pe-4 py-3" style="width: 100px;">Skor 360°</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($topResults as $index => $res)
                                @php $emp = $res->employee; @endphp
                                @if($emp)
                                    <tr>
                                        <td class="ps-4 py-3 text-center">
                                            <span class="fw-bold fs-6">
                                                @if($index === 0) 🥇
                                                @elseif($index === 1) 🥈
                                                @elseif($index === 2) 🥉
                                                @else <span class="badge bg-slate-100 text-slate-600 rounded-circle p-1.5" style="font-size: 11px;">{{ $index + 1 }}</span>
                                                @endif
                                            </span>
                                        </td>
                                        <td class="py-3 text-center" style="max-width: 0;">
                                            <div class="fw-bold text-dark text-wrap text-break mx-auto" style="font-size: 13.5px; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;" title="{{ $emp->name }}">{{ $emp->name }}</div>
                                        </td>
                                        <td class="py-3 text-secondary text-center" style="font-size: 13px;">
                                            {{ $emp->nip }}
                                        </td>
                                        <td class="py-3 text-center">
                                            <span class="badge bg-slate-100 text-slate-700 fw-medium px-2.5 py-1 rounded-2 text-wrap text-center" style="font-size: 11px; line-height: 1.4;">
                                                {{ $emp->department->name ?? '-' }}
                                            </span>
                                        </td>
                                        <td class="text-center pe-4 py-3">
                                            <span class="badge bg-blue-50 text-blue-700 border border-blue-200 fw-extrabold px-3 py-1.5 rounded-pill" style="font-size: 13px;">
                                                {{ number_format($res->final_score, 2) }}
                                            </span>
                                        </td>
                                    </tr>
                                @endif
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-5">
                                        <i class="bi bi-inbox text-slate-300 display-6 d-block mb-2"></i>
                                        Belum ada data kalkulasi nilai pegawai pada periode ini.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
