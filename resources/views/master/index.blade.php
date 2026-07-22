@extends('layouts.app')

@section('title', 'Master Data')
@section('header', 'Pengelolaan Master Data')
@section('subtitle', 'Pusat integrasi data acuan ASN, unit kerja, jabatan, dan instrumen penilaian 360° BKPSDM')

@section('content')
<!-- Header Portal Banner -->
<div class="card border-0 rounded-4 shadow-sm mb-4 overflow-hidden" style="background: linear-gradient(135deg, #1E3A5F 0%, #0F172A 100%);">
    <div class="card-body p-4 text-white">
        <div class="row align-items-center">
            <div class="col-12 col-md-8">
                <div class="d-inline-flex align-items-center gap-2 px-3 py-1 rounded-pill bg-white bg-opacity-10 text-white border border-white border-opacity-20 mb-2" style="font-size: 11px; font-weight: 600;">
                    <i class="bi bi-database-fill text-warning"></i> MASTER DATA CONFIGURATION
                </div>
                <h2 class="fw-bold text-white mb-1" style="font-size: 22px;">Modul Pengelolaan Master Data BKPSDM</h2>
                <p class="text-white text-opacity-80 mb-0" style="font-size: 13px;">
                    Silakan pilih modul data acuan di bawah ini untuk mengelola entitas sistem penilaian kinerja 360 derajat.
                </p>
            </div>
            <div class="col-12 col-md-4 text-md-end mt-3 mt-md-0">
                <a href="{{ route('dashboard') }}" class="btn btn-light text-primary fw-semibold px-3 py-2 rounded-3 shadow-sm" style="font-size: 13px;">
                    <i class="bi bi-arrow-left me-1"></i> Kembali ke Dashboard
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Grid Master Data Cards -->
<div class="row g-4 mb-4">
    <!-- Card 1: Data Pegawai ASN -->
    <div class="col-12 col-md-6 col-lg-4">
        <div class="card border-0 rounded-4 shadow-sm h-100 hover-lift">
            <div class="card-body p-4">
                <div class="d-flex align-items-center gap-3 mb-3">
                    <div class="rounded-4 p-3 d-flex align-items-center justify-content-center bg-primary text-white" style="width: 52px; height: 52px; background: linear-gradient(135deg, #1E40AF 0%, #2563EB 100%) !important;">
                        <i class="bi bi-people-fill fs-4"></i>
                    </div>
                    <div>
                        <h5 class="fw-bold text-dark mb-0" style="font-size: 16px;">Data Pegawai ASN</h5>
                        <small class="text-muted">Master Aparatur Sipil Negara</small>
                    </div>
                </div>
                <p class="text-secondary mb-4" style="font-size: 13px; line-height: 1.5;">
                    Kelola NIP, nama, email, atasan langsung, unit kerja, serta peran hak akses pegawai.
                </p>
                <a href="{{ route('master.employees.index') }}" class="btn btn-primary w-100 fw-semibold rounded-3 py-2" style="font-size: 13px;">
                    Kelola Pegawai <i class="bi bi-arrow-right ms-1"></i>
                </a>
            </div>
        </div>
    </div>

    <!-- Card 2: Unit Kerja / Bidang -->
    <div class="col-12 col-md-6 col-lg-4">
        <div class="card border-0 rounded-4 shadow-sm h-100 hover-lift">
            <div class="card-body p-4">
                <div class="d-flex align-items-center gap-3 mb-3">
                    <div class="rounded-4 p-3 d-flex align-items-center justify-content-center bg-info text-white" style="width: 52px; height: 52px; background: linear-gradient(135deg, #0284C7 0%, #0EA5E9 100%) !important;">
                        <i class="bi bi-building fs-4"></i>
                    </div>
                    <div>
                        <h5 class="fw-bold text-dark mb-0" style="font-size: 16px;">Unit Kerja / Bidang</h5>
                        <small class="text-muted">Struktur Perangkat Daerah</small>
                    </div>
                </div>
                <p class="text-secondary mb-4" style="font-size: 13px; line-height: 1.5;">
                    Kelola daftar bidang, sekretariat, dan unit kerja operasional di lingkungan BKPSDM.
                </p>
                <a href="{{ route('master.departments.index') }}" class="btn btn-info text-white w-100 fw-semibold rounded-3 py-2" style="font-size: 13px; background-color: #0EA5E9; border-color: #0EA5E9;">
                    Kelola Unit Kerja <i class="bi bi-arrow-right ms-1"></i>
                </a>
            </div>
        </div>
    </div>

    <!-- Card 3: Master Jabatan -->
    <div class="col-12 col-md-6 col-lg-4">
        <div class="card border-0 rounded-4 shadow-sm h-100 hover-lift">
            <div class="card-body p-4">
                <div class="d-flex align-items-center gap-3 mb-3">
                    <div class="rounded-4 p-3 d-flex align-items-center justify-content-center text-white" style="width: 52px; height: 52px; background: linear-gradient(135deg, #7C3AED 0%, #8B5CF6 100%) !important;">
                        <i class="bi bi-person-badge-fill fs-4"></i>
                    </div>
                    <div>
                        <h5 class="fw-bold text-dark mb-0" style="font-size: 16px;">Master Jabatan</h5>
                        <small class="text-muted">Nomenklatur Jabatan ASN</small>
                    </div>
                </div>
                <p class="text-secondary mb-4" style="font-size: 13px; line-height: 1.5;">
                    Kelola tingkatan jabatan struktural, fungsional umum, dan fungsional tertentu.
                </p>
                <a href="{{ route('master.positions.index') }}" class="btn btn-purple text-white w-100 fw-semibold rounded-3 py-2" style="font-size: 13px; background-color: #8B5CF6; border-color: #8B5CF6;">
                    Kelola Jabatan <i class="bi bi-arrow-right ms-1"></i>
                </a>
            </div>
        </div>
    </div>

    <!-- Card 4: Periode Penilaian -->
    <div class="col-12 col-md-6 col-lg-4">
        <div class="card border-0 rounded-4 shadow-sm h-100 hover-lift">
            <div class="card-body p-4">
                <div class="d-flex align-items-center gap-3 mb-3">
                    <div class="rounded-4 p-3 d-flex align-items-center justify-content-center bg-success text-white" style="width: 52px; height: 52px; background: linear-gradient(135deg, #059669 0%, #10B981 100%) !important;">
                        <i class="bi bi-calendar-range-fill fs-4"></i>
                    </div>
                    <div>
                        <h5 class="fw-bold text-dark mb-0" style="font-size: 16px;">Periode Penilaian</h5>
                        <small class="text-muted">Jadwal & Status Evaluasi</small>
                    </div>
                </div>
                <p class="text-secondary mb-4" style="font-size: 13px; line-height: 1.5;">
                    Atur tanggal mulai, batas waktu pengisian kuesioner, dan status aktif periode penilaian.
                </p>
                <a href="{{ route('master.periods.index') }}" class="btn btn-success w-100 fw-semibold rounded-3 py-2" style="font-size: 13px;">
                    Kelola Periode <i class="bi bi-arrow-right ms-1"></i>
                </a>
            </div>
        </div>
    </div>

    <!-- Card 5: Kategori Penilaian -->
    <div class="col-12 col-md-6 col-lg-4">
        <div class="card border-0 rounded-4 shadow-sm h-100 hover-lift">
            <div class="card-body p-4">
                <div class="d-flex align-items-center gap-3 mb-3">
                    <div class="rounded-4 p-3 d-flex align-items-center justify-content-center text-white" style="width: 52px; height: 52px; background: linear-gradient(135deg, #D97706 0%, #F59E0B 100%) !important;">
                        <i class="bi bi-tags-fill fs-4"></i>
                    </div>
                    <div>
                        <h5 class="fw-bold text-dark mb-0" style="font-size: 16px;">Kategori BerAKHLAK</h5>
                        <small class="text-muted">7 Dimensi Core Values</small>
                    </div>
                </div>
                <p class="text-secondary mb-4" style="font-size: 13px; line-height: 1.5;">
                    Kelola 7 dimensi utama BerAKHLAK (Berorientasi Pelayanan, Akuntabel, Kompeten, dst).
                </p>
                <a href="{{ route('master.assessment-categories.index') }}" class="btn btn-warning text-dark w-100 fw-semibold rounded-3 py-2" style="font-size: 13px; background-color: #F59E0B; border-color: #F59E0B; color: #FFFFFF !important;">
                    Kelola Kategori <i class="bi bi-arrow-right ms-1"></i>
                </a>
            </div>
        </div>
    </div>

    <!-- Card 6: Indikator Evaluasi -->
    <div class="col-12 col-md-6 col-lg-4">
        <div class="card border-0 rounded-4 shadow-sm h-100 hover-lift">
            <div class="card-body p-4">
                <div class="d-flex align-items-center gap-3 mb-3">
                    <div class="rounded-4 p-3 d-flex align-items-center justify-content-center text-white" style="width: 52px; height: 52px; background: linear-gradient(135deg, #E11D48 0%, #F43F5E 100%) !important;">
                        <i class="bi bi-card-checklist fs-4"></i>
                    </div>
                    <div>
                        <h5 class="fw-bold text-dark mb-0" style="font-size: 16px;">Indikator Kuesioner</h5>
                        <small class="text-muted">Butir Pertanyaan Evaluasi</small>
                    </div>
                </div>
                <p class="text-secondary mb-4" style="font-size: 13px; line-height: 1.5;">
                    Kelola daftar butir pertanyaan kuesioner 360 derajat untuk masing-masing kategori BerAKHLAK.
                </p>
                <a href="{{ route('master.assessment-indicators.index') }}" class="btn btn-danger w-100 fw-semibold rounded-3 py-2" style="font-size: 13px;">
                    Kelola Indikator <i class="bi bi-arrow-right ms-1"></i>
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
