@extends('layouts.app')

@section('title', 'Edit Periode Penilaian')
@section('header', 'Edit Data Periode Penilaian')
@section('subtitle', 'Pembaruan jadwal dan status periode evaluasi')

@section('breadcrumb')
    <li class="breadcrumb-item"><a wire:navigate href="{{ route('master.index') }}">Master Data</a></li>
    <li class="breadcrumb-item"><a wire:navigate href="{{ route('master.periods.index') }}">Periode Penilaian</a></li>
    <li class="breadcrumb-item active" aria-current="page">Edit</li>
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-12 col-md-8 col-lg-7">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3">
                <span class="fw-semibold"><i class="bi bi-pencil-square me-2 text-primary"></i>Edit Periode</span>
            </div>
            <div class="card-body p-4">
                @if($period->end_date && $period->end_date->isPast())
                    <div class="alert alert-danger border-0 mb-4 p-3 rounded-3">
                        <div class="d-flex align-items-center">
                            <i class="bi bi-lock-fill me-2 fs-4"></i>
                            <div>
                                <strong>Periode Telah Selesai:</strong> Tanggal & jam selesai periode ini telah terlewat ({{ $period->end_date->format('d M Y, H:i') }} WIB). Periode ini berstatus <strong>CLOSED / Selesai</strong> dan tidak dapat diaktifkan kembali.
                            </div>
                        </div>
                    </div>
                @endif

                {{-- Live Client-Side Warning Alert Container --}}
                <div id="date-warning-alert" class="alert alert-warning alert-dismissible fade show d-none" role="alert">
                    <i class="bi bi-exclamation-triangle-fill me-2 fs-5 align-middle"></i>
                    <span id="date-warning-text">Tanggal dan jam yang Anda pilih sudah terlewat!</span>
                </div>

                <form action="{{ route('master.periods.update', $period) }}" method="POST" id="period-form">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label for="name" class="form-label fw-semibold">Nama Periode Penilaian <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $period->name) }}" required>
                        @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-12 col-sm-6">
                            <label for="year" class="form-label fw-semibold">Tahun <span class="text-danger">*</span></label>
                            <input type="number" name="year" id="year" class="form-control @error('year') is-invalid @enderror" value="{{ old('year', $period->year) }}" required>
                            @error('year') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-12 col-sm-6">
                            <label for="month" class="form-label fw-semibold">Bulan ke- <span class="text-danger">*</span></label>
                            <input type="number" min="1" max="12" name="month" id="month" class="form-control @error('month') is-invalid @enderror" value="{{ old('month', $period->month) }}" required>
                            @error('month') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    {{-- Tanggal & Jam Mulai (Terpisah) --}}
                    <div class="row g-3 mb-3">
                        <div class="col-12 col-sm-6">
                            <label for="start_date" class="form-label fw-semibold">Tanggal Mulai <span class="text-danger">*</span></label>
                            <input type="date" name="start_date" id="start_date" class="form-control @error('start_date') is-invalid @enderror" value="{{ old('start_date', $period->start_date ? $period->start_date->format('Y-m-d') : '') }}" required>
                            @error('start_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-12 col-sm-6">
                            <label for="start_time" class="form-label fw-semibold">Jam Mulai <span class="text-danger">*</span></label>
                            <input type="time" name="start_time" id="start_time" class="form-control @error('start_time') is-invalid @enderror" value="{{ old('start_time', $period->start_date ? $period->start_date->format('H:i') : '08:00') }}" required>
                            @error('start_time') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    {{-- Tanggal & Jam Selesai (Terpisah) --}}
                    <div class="row g-3 mb-4">
                        <div class="col-12 col-sm-6">
                            <label for="end_date" class="form-label fw-semibold">Tanggal Selesai <span class="text-danger">*</span></label>
                            <input type="date" name="end_date" id="end_date" class="form-control @error('end_date') is-invalid @enderror" value="{{ old('end_date', $period->end_date ? $period->end_date->format('Y-m-d') : '') }}" required>
                            @error('end_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-12 col-sm-6">
                            <label for="end_time" class="form-label fw-semibold">Jam Selesai <span class="text-danger">*</span></label>
                            <input type="time" name="end_time" id="end_time" class="form-control @error('end_time') is-invalid @enderror" value="{{ old('end_time', $period->end_date ? $period->end_date->format('H:i') : '23:59') }}" required>
                            @error('end_time') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-12 col-sm-6">
                            <label for="status" class="form-label fw-semibold">Status Periode <span class="text-danger">*</span></label>
                            <select name="status" id="status" class="form-select @error('status') is-invalid @enderror" required>
                                <option value="OPEN" {{ old('status', $period->status?->value ?? $period->status) === 'OPEN' ? 'selected' : '' }}>OPEN (Terbuka untuk Penilaian)</option>
                                <option value="CLOSED" {{ old('status', $period->status?->value ?? $period->status) === 'CLOSED' ? 'selected' : '' }}>CLOSED (Ditutup / Selesai)</option>
                                <option value="ARCHIVED" {{ old('status', $period->status?->value ?? $period->status) === 'ARCHIVED' ? 'selected' : '' }}>ARCHIVED (Arsip)</option>
                            </select>
                            @error('status') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-12 col-sm-6 align-self-center mt-4">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', $period->is_active) ? 'checked' : '' }} {{ ($period->end_date && $period->end_date->isPast()) ? 'disabled' : '' }}>
                                <label class="form-check-label fw-semibold" for="is_active">Set Sebagai Periode Aktif</label>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2 border-top pt-3">
                        <a wire:navigate href="{{ route('master.periods.index') }}" class="btn btn-outline-secondary">Batal</a>
                        <button type="submit" class="btn btn-primary"><i class="bi bi-save me-1"></i> Perbarui Periode</button>
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
            warningText.innerText = 'Peringatan: Tanggal & jam selesai yang Anda pilih sudah terlewat!';
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
