@extends('layouts.app')

@section('title', 'Laporan Hasil Penilaian')
@section('header', 'Laporan Rekapitulasi Penilaian Kinerja 360°')
@section('subtitle', 'Cetak dan ekspor hasil akhir penilaian kinerja ASN Kabupaten Pemalang')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active" aria-current="page">Laporan</li>
@endsection

@section('action_buttons')
    <div class="d-flex gap-2">
        <a href="{{ route('report.print', ['period_id' => $selectedPeriodId, 'department_id' => $selectedDepartmentId]) }}" target="_blank" class="btn btn-outline-primary">
            <i class="bi bi-printer me-1"></i> Cetak / PDF
        </a>
        <a href="{{ route('report.exportCsv', ['period_id' => $selectedPeriodId, 'department_id' => $selectedDepartmentId]) }}" class="btn btn-success fw-semibold">
            <i class="bi bi-file-earmark-excel me-1"></i> Ekspor Excel (.xlsx)
        </a>
    </div>
@endsection

@section('content')
<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white py-3">
        <form method="GET" action="{{ route('report.index') }}" class="row g-2">
            <div class="col-12 col-md-4">
                <select name="period_id" class="form-select bg-light" onchange="this.form.submit()">
                    <option value="">-- Pilih Periode Penilaian --</option>
                    @foreach($periods as $per)
                        <option value="{{ $per->id }}" {{ $selectedPeriodId == $per->id ? 'selected' : '' }}>
                            {{ $per->name }} ({{ $per->year }})
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-12 col-md-4">
                <select name="department_id" class="form-select bg-light" onchange="this.form.submit()">
                    <option value="">-- Semua Unit Kerja / Bidang --</option>
                    @foreach($departments as $dept)
                        <option value="{{ $dept->id }}" {{ $selectedDepartmentId == $dept->id ? 'selected' : '' }}>{{ $dept->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-12 col-md-4 d-flex gap-2">
                <div class="input-group">
                    <input type="text" name="search" class="form-control bg-light" placeholder="Cari NIP / Nama..." value="{{ request('search') }}">
                    <button class="btn btn-primary" type="submit"><i class="bi bi-search"></i></button>
                </div>
                @if($selectedPeriodId || $selectedDepartmentId || request('search'))
                    <a href="{{ route('report.index') }}" class="btn btn-outline-secondary" title="Reset Filter">
                        <i class="bi bi-x-circle"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>

    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover table-striped align-middle mb-0">
                <thead>
                    <tr>
                        <th class="ps-3" style="width: 50px;">No</th>
                        <th>NIP & Nama Pegawai</th>
                        <th>Unit Kerja & Jabatan</th>
                        <th class="text-center">Atasan (40%)</th>
                        <th class="text-center">Sejawat (30%)</th>
                        <th class="text-center">Bawahan (20%)</th>
                        <th class="text-center">Diri (10%)</th>
                        <th class="text-center">Nilai Akhir 360°</th>
                        <th class="text-center">Predikat</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($results as $index => $res)
                        <tr>
                            <td class="ps-3 fw-semibold text-muted">{{ $results->firstItem() + $index }}</td>
                            <td>
                                <div class="fw-semibold text-dark">{{ $res->employee->name ?? '-' }}</div>
                                <small class="text-muted">NIP. {{ $res->employee->nip ?? '-' }}</small>
                            </td>
                            <td>
                                <div class="small fw-medium text-dark">{{ $res->employee->department->name ?? '-' }}</div>
                                <small class="text-muted">{{ $res->employee->position->name ?? '-' }}</small>
                            </td>
                            <td class="text-center">{{ number_format($res->superior_score ?? 0, 2) }}</td>
                            <td class="text-center">{{ number_format($res->peer_score ?? 0, 2) }}</td>
                            <td class="text-center">{{ number_format($res->subordinate_score ?? 0, 2) }}</td>
                            <td class="text-center">{{ number_format($res->self_score ?? 0, 2) }}</td>
                            <td class="text-center">
                                <span class="fw-semibold text-dark">
                                    {{ number_format($res->final_score ?? 0, 2) }}
                                </span>
                            </td>
                            <td class="text-center">
                                @if($res->category)
                                    @php
                                        $catEnum = $res->category instanceof \App\Enums\ResultCategory ? $res->category : \App\Enums\ResultCategory::tryFrom($res->category);
                                        $textColor = match($catEnum) {
                                            \App\Enums\ResultCategory::VERY_GOOD => 'text-success',
                                            \App\Enums\ResultCategory::GOOD => 'text-primary',
                                            \App\Enums\ResultCategory::FAIR => 'text-warning',
                                            \App\Enums\ResultCategory::NEEDS_IMPROVEMENT => 'text-danger',
                                            default => 'text-secondary'
                                        };
                                        $style = $catEnum === \App\Enums\ResultCategory::FAIR ? 'style="color: #b58900 !important;"' : '';
                                    @endphp
                                    <span class="fw-semibold {{ $textColor }}" {!! $style !!}>
                                        {{ $catEnum ? $catEnum->label() : $res->category }}
                                    </span>
                                @else
                                    <span class="text-muted small">-</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center text-muted py-4">
                                <i class="bi bi-inbox fs-3 d-block mb-2 text-secondary"></i>
                                Belum ada data laporan hasil penilaian untuk filter yang dipilih.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if($results->hasPages())
        <div class="card-footer bg-white py-3">
            <div class="d-flex justify-content-between align-items-center">
                <small class="text-muted">
                    Menampilkan {{ $results->firstItem() }} - {{ $results->lastItem() }} dari total {{ $results->total() }} data
                </small>
                {{ $results->withQueryString()->links() }}
            </div>
        </div>
    @endif
</div>
@endsection
