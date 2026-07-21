@extends('layouts.app')

@section('title', 'Buka Periode Penilaian Baru')
@section('header', 'Buka Periode Penilaian Baru')
@section('subtitle', 'Form pengaturan jadwal dan periode evaluasi kinerja ASN')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('master.index') }}">Master Data</a></li>
    <li class="breadcrumb-item"><a href="{{ route('master.periods.index') }}">Periode Penilaian</a></li>
    <li class="breadcrumb-item active" aria-current="page">Tambah</li>
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-12 col-md-8 col-lg-6">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3">
                <span class="fw-semibold"><i class="bi bi-calendar-plus me-2 text-primary"></i>Form Periode Penilaian</span>
            </div>
            <div class="card-body p-4">
                <form action="{{ route('master.periods.store') }}" method="POST">
                    @csrf

                    <div class="mb-3">
                        <label for="name" class="form-label fw-semibold">Nama Periode Penilaian <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" placeholder="Contoh: Penilaian Kinerja Semester I Tahun 2026" required>
                        @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-12 col-sm-6">
                            <label for="year" class="form-label fw-semibold">Tahun <span class="text-danger">*</span></label>
                            <input type="number" name="year" id="year" class="form-control @error('year') is-invalid @enderror" value="{{ old('year', date('Y')) }}" required>
                            @error('year') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-12 col-sm-6">
                            <label for="month" class="form-label fw-semibold">Bulan ke- <span class="text-danger">*</span></label>
                            <input type="number" min="1" max="12" name="month" id="month" class="form-control @error('month') is-invalid @enderror" value="{{ old('month', date('n')) }}" required>
                            @error('month') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-12 col-sm-6">
                            <label for="start_date" class="form-label fw-semibold">Tanggal Mulai <span class="text-danger">*</span></label>
                            <input type="date" name="start_date" id="start_date" class="form-control @error('start_date') is-invalid @enderror" value="{{ old('start_date', date('Y-m-d')) }}" required>
                            @error('start_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-12 col-sm-6">
                            <label for="end_date" class="form-label fw-semibold">Tanggal Selesai <span class="text-danger">*</span></label>
                            <input type="date" name="end_date" id="end_date" class="form-control @error('end_date') is-invalid @enderror" value="{{ old('end_date', date('Y-m-d', strtotime('+30 days'))) }}" required>
                            @error('end_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-12 col-sm-6">
                            <label for="status" class="form-label fw-semibold">Status Periode <span class="text-danger">*</span></label>
                            <select name="status" id="status" class="form-select @error('status') is-invalid @enderror" required>
                                <option value="OPEN" {{ old('status', 'OPEN') === 'OPEN' ? 'selected' : '' }}>OPEN (Terbuka untuk Penilaian)</option>
                                <option value="CLOSED" {{ old('status') === 'CLOSED' ? 'selected' : '' }}>CLOSED (Ditutup)</option>
                                <option value="ARCHIVED" {{ old('status') === 'ARCHIVED' ? 'selected' : '' }}>ARCHIVED (Arsip)</option>
                            </select>
                            @error('status') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-12 col-sm-6 align-self-center mt-4">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', '1') == '1' ? 'checked' : '' }}>
                                <label class="form-check-label fw-semibold" for="is_active">Set Sebagai Periode Aktif</label>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2 border-top pt-3">
                        <a href="{{ route('master.periods.index') }}" class="btn btn-outline-secondary">Batal</a>
                        <button type="submit" class="btn btn-primary"><i class="bi bi-save me-1"></i> Simpan Periode</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
