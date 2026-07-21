@extends('layouts.app')

@section('title', 'Dashboard Admin')
@section('header', 'Dashboard Administrator')
@section('subtitle', 'Overview statistik master data dan status penilaian kinerja 360° ASN BKPSDM Kabupaten Pemalang')

@section('content')
<!-- Row Status Cards -->
<div class="row g-3 mb-4">
    <!-- Stat 1: Pegawai -->
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card h-100 border-0 shadow-sm">
            <div class="card-body p-3">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <small class="text-uppercase fw-semibold text-muted" style="font-size: 11px;">Total Pegawai</small>
                        <h3 class="fw-bold mb-0 mt-1" style="color: #1E3A5F;">{{ number_format($stats['total_pegawai']) }}</h3>
                        <div class="mt-2" style="font-size: 12px;">
                            <a href="{{ route('master.employees.index') }}" class="text-decoration-none text-primary fw-medium">
                                Lihat Pegawai <i class="bi bi-arrow-right ms-1"></i>
                            </a>
                        </div>
                    </div>
                    <div class="rounded-3 p-3 bg-primary bg-opacity-10 text-primary">
                        <i class="bi bi-people-fill fs-3" style="color: #1E3A5F;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Stat 2: Unit Kerja -->
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card h-100 border-0 shadow-sm">
            <div class="card-body p-3">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <small class="text-uppercase fw-semibold text-muted" style="font-size: 11px;">Unit Kerja / Bidang</small>
                        <h3 class="fw-bold mb-0 mt-1" style="color: #1E3A5F;">{{ number_format($stats['total_department']) }}</h3>
                        <div class="mt-2" style="font-size: 12px;">
                            <a href="{{ route('master.departments.index') }}" class="text-decoration-none text-primary fw-medium">
                                Lihat Unit Kerja <i class="bi bi-arrow-right ms-1"></i>
                            </a>
                        </div>
                    </div>
                    <div class="rounded-3 p-3 bg-info bg-opacity-10 text-info">
                        <i class="bi bi-building fs-3"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Stat 3: Jabatan -->
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card h-100 border-0 shadow-sm">
            <div class="card-body p-3">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <small class="text-uppercase fw-semibold text-muted" style="font-size: 11px;">Master Jabatan</small>
                        <h3 class="fw-bold mb-0 mt-1" style="color: #1E3A5F;">{{ number_format($stats['total_position']) }}</h3>
                        <div class="mt-2" style="font-size: 12px;">
                            <a href="{{ route('master.positions.index') }}" class="text-decoration-none text-primary fw-medium">
                                Lihat Jabatan <i class="bi bi-arrow-right ms-1"></i>
                            </a>
                        </div>
                    </div>
                    <div class="rounded-3 p-3 bg-secondary bg-opacity-10 text-secondary">
                        <i class="bi bi-person-badge fs-3"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Stat 4: Periode Penilaian -->
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card h-100 border-0 shadow-sm">
            <div class="card-body p-3">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <small class="text-uppercase fw-semibold text-muted" style="font-size: 11px;">Periode Aktif</small>
                        <div class="fw-bold text-truncate mt-1" style="color: #1E3A5F; font-size: 16px; max-width: 140px;">
                            {{ $stats['active_period']->name ?? 'Belum Ada Aktif' }}
                        </div>
                        <div class="mt-2" style="font-size: 12px;">
                            <a href="{{ route('master.periods.index') }}" class="text-decoration-none text-primary fw-medium">
                                Kelola Periode <i class="bi bi-arrow-right ms-1"></i>
                            </a>
                        </div>
                    </div>
                    <div class="rounded-3 p-3 bg-success bg-opacity-10 text-success">
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
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3 fw-semibold">
                <i class="bi bi-bar-chart-fill me-2 text-primary"></i>Distribusi Predikat Kinerja ASN Periode Aktif
            </div>
            <div class="card-body p-3">
                <div class="row text-center g-3">
                    <div class="col-6 col-md-3">
                        <div class="p-3 border rounded bg-light">
                            <small class="text-uppercase fw-semibold text-muted" style="font-size: 11px;">Sangat Baik</small>
                            <h3 class="fw-bold text-success mb-0 mt-1">{{ $categoryStats['sangat_baik'] ?? 0 }}</h3>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="p-3 border rounded bg-light">
                            <small class="text-uppercase fw-semibold text-muted" style="font-size: 11px;">Baik</small>
                            <h3 class="fw-bold text-primary mb-0 mt-1" style="color: #1E3A5F !important;">{{ $categoryStats['baik'] ?? 0 }}</h3>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="p-3 border rounded bg-light">
                            <small class="text-uppercase fw-semibold text-muted" style="font-size: 11px;">Cukup</small>
                            <h3 class="fw-bold text-warning mb-0 mt-1">{{ $categoryStats['cukup'] ?? 0 }}</h3>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="p-3 border rounded bg-light">
                            <small class="text-uppercase fw-semibold text-muted" style="font-size: 11px;">Kurang</small>
                            <h3 class="fw-bold text-danger mb-0 mt-1">{{ $categoryStats['kurang'] ?? 0 }}</h3>
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
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white border-bottom fw-semibold">
                <i class="bi bi-lightning-charge text-warning me-2"></i>Aksi Cepat Administrator
            </div>
            <div class="card-body p-3">
                <div class="list-group list-group-flush">
                    <a href="{{ route('master.employees.index') }}" class="list-group-item list-group-item-action border-0 rounded-2 mb-2 p-2.5 d-flex align-items-center bg-light">
                        <div class="bg-primary text-white rounded p-2 me-3 d-flex align-items-center justify-content-center" style="width: 38px; height: 38px; background-color: #1E3A5F !important;">
                            <i class="bi bi-person-plus-fill"></i>
                        </div>
                        <div>
                            <div class="fw-semibold text-dark mb-0" style="font-size: 14px;">Kelola Data Pegawai</div>
                            <small class="text-muted" style="font-size: 12px;">Tambah, edit & nonaktifkan ASN</small>
                        </div>
                    </a>

                    <a href="{{ route('master.periods.index') }}" class="list-group-item list-group-item-action border-0 rounded-2 mb-2 p-2.5 d-flex align-items-center bg-light">
                        <div class="bg-success text-white rounded p-2 me-3 d-flex align-items-center justify-content-center" style="width: 38px; height: 38px;">
                            <i class="bi bi-plus-circle"></i>
                        </div>
                        <div>
                            <div class="fw-semibold text-dark mb-0" style="font-size: 14px;">Buka Periode Penilaian</div>
                            <small class="text-muted" style="font-size: 12px;">Atur jadwal penilaian semester/tahunan</small>
                        </div>
                    </a>

                    <a href="{{ route('master.assessment-indicators.index') }}" class="list-group-item list-group-item-action border-0 rounded-2 mb-2 p-2.5 d-flex align-items-center bg-light">
                        <div class="bg-info text-white rounded p-2 me-3 d-flex align-items-center justify-content-center" style="width: 38px; height: 38px;">
                            <i class="bi bi-card-checklist"></i>
                        </div>
                        <div>
                            <div class="fw-semibold text-dark mb-0" style="font-size: 14px;">Indikator Penilaian 360°</div>
                            <small class="text-muted" style="font-size: 12px;">Atur pertanyaan & bobot penilaian</small>
                        </div>
                    </a>

                    <a href="{{ route('transaction.calculations.index') }}" class="list-group-item list-group-item-action border-0 rounded-2 p-2.5 d-flex align-items-center bg-light">
                        <div class="bg-warning text-dark rounded p-2 me-3 d-flex align-items-center justify-content-center" style="width: 38px; height: 38px;">
                            <i class="bi bi-calculator"></i>
                        </div>
                        <div>
                            <div class="fw-semibold text-dark mb-0" style="font-size: 14px;">Hitung Hasil Penilaian</div>
                            <small class="text-muted" style="font-size: 12px;">Kalkulasi skor akhir 360 derajat</small>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Top 5 Employees by Score Table -->
    <div class="col-12 col-lg-8">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white border-bottom d-flex align-items-center justify-content-between">
                <span class="fw-semibold"><i class="bi bi-trophy text-primary me-2"></i>5 Pegawai dengan Skor Kinerja Tertinggi</span>
                <a href="{{ route('transaction.calculations.index') }}" class="btn btn-sm btn-outline-primary">Lihat Semua</a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th class="ps-3">Nama / NIP</th>
                                <th>Unit Kerja</th>
                                <th>Jabatan</th>
                                <th class="text-center">Skor Akhir 360°</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($topResults as $res)
                                @php $emp = $res->employee; @endphp
                                @if($emp)
                                    <tr>
                                        <td class="ps-3">
                                            <div class="fw-semibold text-dark">{{ $emp->name }}</div>
                                            <small class="text-muted">NIP. {{ $emp->nip }}</small>
                                        </td>
                                        <td>{{ $emp->department->name ?? '-' }}</td>
                                        <td>{{ $emp->position->name ?? '-' }}</td>
                                        <td class="text-center">
                                            <span class="fw-bold text-primary">{{ number_format($res->final_score, 2) }}</span>
                                        </td>
                                    </tr>
                                @endif
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-4">Belum ada data perhitungan nilai pegawai.</td>
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
