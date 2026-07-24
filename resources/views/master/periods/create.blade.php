@extends('layouts.app')

@section('title', 'Buka Periode Penilaian Baru')
@section('header', 'Buka Periode Penilaian Baru')
@section('subtitle', 'Form pengaturan jadwal dan periode evaluasi kinerja ASN')

@section('breadcrumb')
    <li class="breadcrumb-item"><a wire:navigate href="{{ route('master.index') }}">Master Data</a></li>
    <li class="breadcrumb-item"><a wire:navigate href="{{ route('master.periods.index') }}">Periode Penilaian</a></li>
    <li class="breadcrumb-item active" aria-current="page">Tambah</li>
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-12 col-md-8 col-lg-7">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3">
                <span class="fw-semibold"><i class="bi bi-calendar-plus me-2 text-primary"></i>Form Periode Penilaian</span>
            </div>
            <div class="card-body p-4">
                {{-- Live Client-Side Warning Alert Container --}}
                <div id="date-warning-alert" class="alert alert-warning alert-dismissible fade show d-none" role="alert">
                    <i class="bi bi-exclamation-triangle-fill me-2 fs-5 align-middle"></i>
                    <span id="date-warning-text">Tanggal dan jam yang Anda pilih sudah terlewat!</span>
                </div>

                <form action="{{ route('master.periods.store') }}" method="POST" id="period-form">
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

                    {{-- Tanggal & Jam Mulai (Terpisah) --}}
                    <div class="row g-3 mb-3">
                        <div class="col-12 col-sm-6">
                            <label for="start_date" class="form-label fw-semibold">Tanggal Mulai <span class="text-danger">*</span></label>
                            <input type="date" name="start_date" id="start_date" class="form-control @error('start_date') is-invalid @enderror" value="{{ old('start_date', date('Y-m-d')) }}" required>
                            @error('start_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-12 col-sm-6">
                            <label for="start_time" class="form-label fw-semibold">Jam Mulai <span class="text-danger">*</span></label>
                            <input type="time" name="start_time" id="start_time" class="form-control @error('start_time') is-invalid @enderror" value="{{ old('start_time', date('H:i')) }}" required>
                            @error('start_time') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    {{-- Tanggal & Jam Selesai (Terpisah) --}}
                    <div class="row g-3 mb-4">
                        <div class="col-12 col-sm-6">
                            <label for="end_date" class="form-label fw-semibold">Tanggal Selesai <span class="text-danger">*</span></label>
                            <input type="date" name="end_date" id="end_date" class="form-control @error('end_date') is-invalid @enderror" value="{{ old('end_date', date('Y-m-d', strtotime('+30 days'))) }}" required>
                            @error('end_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-12 col-sm-6">
                            <label for="end_time" class="form-label fw-semibold">Jam Selesai <span class="text-danger">*</span></label>
                            <input type="time" name="end_time" id="end_time" class="form-control @error('end_time') is-invalid @enderror" value="{{ old('end_time', '23:59') }}" required>
                            @error('end_time') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <div class="alert alert-info border-0 bg-light-subtle text-primary mb-4 p-3 rounded-3">
                        <div class="d-flex align-items-center">
                            <i class="bi bi-info-circle-fill me-2 fs-5"></i>
                            <div>
                                <strong>Aktivasi Otomatis:</strong> Setelah disimpan, periode ini akan secara <strong>otomatis diaktifkan</strong> oleh sistem dan menutup periode sebelumnya (jika ada), selama tanggal & jam selesai belum terlewat.
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2 border-top pt-3">
                        <a wire:navigate href="{{ route('master.periods.index') }}" class="btn btn-outline-secondary">Batal</a>
                        <button type="submit" class="btn btn-primary" id="btn-submit"><i class="bi bi-save me-1"></i> Simpan & Aktifkan Periode</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const startDateInput = document.getElementById('start_date');
    const startTimeInput = document.getElementById('start_time');
    const endDateInput = document.getElementById('end_date');
    const endTimeInput = document.getElementById('end_time');
    const warningAlert = document.getElementById('date-warning-alert');
    const warningText = document.getElementById('date-warning-text');

    function checkDates() {
        const now = new Date();

        const startDateStr = startDateInput.value && startTimeInput.value ? `${startDateInput.value}T${startTimeInput.value}` : null;
        const endDateStr = endDateInput.value && endTimeInput.value ? `${endDateInput.value}T${endTimeInput.value}` : null;

        const startDate = startDateStr ? new Date(startDateStr) : null;
        const endDate = endDateStr ? new Date(endDateStr) : null;

        if (endDate && endDate <= now) {
            warningText.innerText = 'Peringatan: Tanggal & jam selesai yang Anda pilih sudah terlewat! Silakan periksa kembali.';
            warningAlert.classList.remove('d-none');
        } else if (startDate && endDate && endDate <= startDate) {
            warningText.innerText = 'Peringatan: Waktu selesai harus lebih besar dari waktu mulai!';
            warningAlert.classList.remove('d-none');
        } else {
            warningAlert.classList.add('d-none');
        }
    }

    startDateInput.addEventListener('change', checkDates);
    startTimeInput.addEventListener('change', checkDates);
    endDateInput.addEventListener('change', checkDates);
    endTimeInput.addEventListener('change', checkDates);
    checkDates();
});
</script>
@endpush
