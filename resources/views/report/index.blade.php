@extends('layouts.app')

@php
    if ($activeTab === 'department') {
        $pageTitle = 'Laporan Per Bidang / Unit Kerja';
        $pageSubtitle = 'Rekapitulasi dan ringkasan nilai rata-rata kinerja pada tiap unit kerja';
    } elseif ($activeTab === 'analytics') {
        $pageTitle = 'Statistik & Analitik Kinerja';
        $pageSubtitle = 'Dashboard analitik dan distribusi predikat penilaian kinerja secara keseluruhan';
    } else {
        $pageTitle = 'Laporan Individu Pegawai';
        $pageSubtitle = 'Cetak dan ekspor hasil akhir penilaian kinerja tiap pegawai ASN secara detail';
    }
@endphp

@section('title', $pageTitle)
@section('header', $pageTitle)
@section('subtitle', $pageSubtitle)

@section('breadcrumb')
    <li class="breadcrumb-item active" aria-current="page">Laporan</li>
@endsection

@section('action_buttons')
    <div class="d-flex gap-2">
        <a href="{{ route('report.print', ['period_id' => $selectedPeriodId, 'department_id' => $selectedDepartmentId, 'tab' => $activeTab]) }}" target="_blank" class="btn btn-danger fw-semibold">
            <i class="bi bi-file-earmark-pdf me-1"></i> Ekspor PDF
        </a>
        <a href="{{ route('report.exportCsv', ['period_id' => $selectedPeriodId, 'department_id' => $selectedDepartmentId, 'tab' => $activeTab]) }}" class="btn btn-success fw-semibold">
            <i class="bi bi-file-earmark-excel me-1"></i> Ekspor Excel (.xlsx)
        </a>
    </div>
@endsection

@section('content')


<!-- Global Filter Header -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white py-3">
        <form method="GET" action="{{ route('report.index') }}" class="row g-2 align-items-center" x-data @submit.prevent="Livewire.navigate($el.action + '?' + new URLSearchParams(new FormData($el)).toString())">
            <input type="hidden" name="tab" value="{{ $activeTab }}">
            @php
                $colPeriod = $activeTab === 'department' ? 'col-md-4' : 'col-md-3';
                $colDept = $activeTab === 'department' ? 'col' : 'col-md-4';
                $colSearch = $activeTab === 'department' ? 'col-auto' : 'col-md-5';
            @endphp
            <div class="{{ $colPeriod }}">
                <label class="form-label small fw-semibold text-muted mb-1">Periode Evaluation</label>
                <select name="period_id" class="form-select bg-light" onchange="this.form.requestSubmit()">
                    <option value="">-- Semua Periode --</option>
                    @foreach($periods as $per)
                        <option value="{{ $per->id }}" {{ $selectedPeriodId == $per->id ? 'selected' : '' }}>
                            {{ $per->name }} ({{ $per->year }})
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="{{ $colDept }}">
                <label class="form-label small fw-semibold text-muted mb-1">Unit Kerja / Bidang</label>
                <select name="department_id" class="form-select bg-light text-truncate" onchange="this.form.requestSubmit()">
                    <option value="">-- Semua Unit Kerja / Bidang --</option>
                    @foreach($departments as $dept)
                        <option value="{{ $dept->id }}" {{ $selectedDepartmentId == $dept->id ? 'selected' : '' }} title="{{ $dept->name }}">{{ $dept->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="{{ $colSearch }} d-flex gap-2">
                @if($activeTab !== 'department')
                    <div class="w-100">
                        <label class="form-label small fw-semibold text-muted mb-1">Cari Data</label>
                        <div class="input-group">
                            <input type="text" name="search" class="form-control bg-light" placeholder="Cari NIP / Nama Pegawai..." value="{{ request('search') }}" {{ $activeTab === 'analytics' ? 'disabled title="Pencarian tidak tersedia untuk Statistik"' : '' }}>
                            <button class="btn btn-primary" type="submit" {{ $activeTab === 'analytics' ? 'disabled' : '' }}><i class="bi bi-search"></i></button>
                        </div>
                    </div>
                @endif
                @if($selectedPeriodId || $selectedDepartmentId || request('search'))
                    <div>
                        <label class="form-label small mb-1 d-block">&nbsp;</label>
                        <a href="{{ route('report.index', ['tab' => $activeTab]) }}" class="btn btn-outline-secondary" title="Reset Filter" wire:navigate>
                            <i class="bi bi-x-circle"></i>
                        </a>
                    </div>
                @endif
            </div>
        </form>
    </div>
</div>


@if($activeTab === 'department')
    <!-- TAB: LAPORAN PER BIDANG -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white py-3 px-4 fw-bold text-dark d-flex align-items-center justify-content-between">
            <span><i class="bi bi-diagram-3-fill text-primary me-2"></i>Rekapitulasi Kinerja per Bidang / Unit Kerja</span>
            <span class="badge bg-primary text-white rounded-pill px-3 py-1">BKPSDM Kabupaten Pemalang</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" style="table-layout: fixed;">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4" style="width: 5%;">No</th>
                            <th style="width: 30%;">Bidang / Unit Kerja</th>
                            <th class="text-center" style="width: 15%;">Jumlah Pegawai Evaluasi</th>
                            <th class="text-center" style="width: 15%;">Rata-Rata Nilai</th>
                            <th class="text-center" style="width: 15%;">Nilai Tertinggi</th>
                            <th class="text-center" style="width: 20%;">Distribusi Predikat</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($departmentStats as $idx => $stat)
                            <tr>
                                <td class="ps-4 fw-semibold text-muted">{{ $idx + 1 }}</td>
                                <td>
                                    <div class="fw-semibold text-dark">{{ $stat['department']->name }}</div>
                                    <small class="text-muted">Kode: {{ $stat['department']->code ?? '-' }}</small>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-light text-dark border fw-medium rounded-pill">
                                        {{ $stat['total_evaluated'] }} Pegawai
                                    </span>
                                </td>
                                <td class="text-center">
                                    <span class="fw-semibold text-primary">
                                        {{ number_format($stat['average_score'], 2) }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <span class="fw-semibold text-success">
                                        {{ number_format($stat['highest_score'], 2) }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <div class="d-flex gap-2 justify-content-center flex-wrap">
                                        <span class="fw-bold text-primary" title="Sangat Baik">SB: {{ $stat['very_good'] }}</span>
                                        <span class="fw-bold text-success" title="Baik">B: {{ $stat['good'] }}</span>
                                        <span class="fw-bold text-warning" title="Cukup">C: {{ $stat['fair'] }}</span>
                                        <span class="fw-bold text-danger" title="Perlu Pembinaan">PB: {{ $stat['needs_improvement'] }}</span>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">Belum ada data unit kerja.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

        </div>
    </div>

@elseif($activeTab === 'analytics')
    <!-- TAB: STATISTIK & TREN KINERJA -->
    <div class="row g-3 mb-4">
        <div class="col-12 col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body p-4 text-center">
                    <span class="text-muted fw-semibold small text-uppercase d-block mb-1">Rata-Rata Nilai Instansi</span>
                    <h2 class="fw-extrabold text-primary display-5 mb-1">{{ number_format($overallAverage, 2) }}</h2>
                    <span class="badge bg-primary-subtle text-primary rounded-pill px-3">Skala 100</span>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-8">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white py-3 px-4 fw-bold text-dark">
                    <i class="bi bi-pie-chart-fill text-warning me-2"></i>Distribusi Predikat Kinerja ASN BKPSDM
                </div>
                <div class="card-body p-4">
                    <div class="row text-center g-3">
                        <div class="col-6 col-sm-3">
                            <div class="p-3 rounded-4 bg-emerald-50 border border-emerald-200 h-100 d-flex flex-column justify-content-center">
                                <div class="text-emerald-700 fw-bold small text-uppercase">Sangat Baik</div>
                                <h3 class="fw-bold text-emerald-600 mb-0 mt-1">{{ $categoryDistribution['VERY_GOOD'] }}</h3>
                            </div>
                        </div>
                        <div class="col-6 col-sm-3">
                            <div class="p-3 rounded-4 bg-blue-50 border border-blue-200 h-100 d-flex flex-column justify-content-center">
                                <div class="text-blue-700 fw-bold small text-uppercase">Baik</div>
                                <h3 class="fw-bold text-blue-600 mb-0 mt-1">{{ $categoryDistribution['GOOD'] }}</h3>
                            </div>
                        </div>
                        <div class="col-6 col-sm-3">
                            <div class="p-3 rounded-4 bg-amber-50 border border-amber-200 h-100 d-flex flex-column justify-content-center">
                                <div class="text-amber-700 fw-bold small text-uppercase">Cukup</div>
                                <h3 class="fw-bold text-amber-600 mb-0 mt-1">{{ $categoryDistribution['FAIR'] }}</h3>
                            </div>
                        </div>
                        <div class="col-6 col-sm-3">
                            <div class="p-3 rounded-4 bg-rose-50 border border-rose-200 h-100 d-flex flex-column justify-content-center">
                                <div class="text-rose-700 fw-bold small text-uppercase">Perlu Pembinaan</div>
                                <h3 class="fw-bold text-rose-600 mb-0 mt-1">{{ $categoryDistribution['NEEDS_IMPROVEMENT'] }}</h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endif

@if($activeTab === 'employee' || $activeTab === 'analytics')
    <!-- TAB: TABLE REPORT GENERAL / EMPLOYEE -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white py-3 px-4 fw-bold text-dark d-flex align-items-center justify-content-between">
            <span>
                <i class="bi bi-table text-primary me-2"></i>
                @if($activeTab === 'employee')
                    Laporan Rinci Individu Pegawai ASN
                @else
                    Tabel Evaluasi Kinerja 360°
                @endif
            </span>
            <span class="text-muted small">Total Data: {{ $results->total() }} Evaluasi</span>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-striped align-middle mb-0" style="table-layout: fixed;">
                    <thead>
                        <tr>
                            <th class="ps-3" style="width: 5%;">No</th>
                            <th style="width: 25%;">NIP & Nama Pegawai</th>
                            <th style="width: 26%;">Unit Kerja & Jabatan</th>
                            <th class="text-center" style="width: 9%;">Atasan</th>
                            <th class="text-center" style="width: 9%;">Sejawat</th>
                            <th class="text-center" style="width: 9%;">Bawahan</th>
                            <th class="text-center" style="width: 9%;">Nilai Akhir 360°</th>
                            <th class="text-center" style="width: 8%;">Predikat</th>
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
                                <td class="text-center">{{ number_format($res->subordinate_average ?? 0, 2) }}</td>
                                <td class="text-center">{{ number_format($res->peer_average ?? 0, 2) }}</td>
                                <td class="text-center">{{ ($res->superior_weight > 0) ? number_format($res->superior_average ?? 0, 2) : '-' }}</td>
                                <td class="text-center">
                                    <span class="fw-semibold text-dark">
                                        {{ number_format($res->final_score ?? 0, 2) }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    @if($res->category)
                                        @php
                                            $catVal = is_object($res->category) ? $res->category->value : (string)$res->category;
                                            $catEnum = \App\Enums\ResultCategory::tryFrom($catVal) ?? \App\Enums\ResultCategory::tryFrom(strtoupper(str_replace(' ', '_', $catVal)));
                                            $catLabel = \App\Enums\ResultCategory::formatLabel($res->category);
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
                                            {{ $catLabel }}
                                        </span>
                                    @else
                                        <span class="text-muted small">-</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted py-4">
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
                {{ $results->withQueryString()->links() }}
            </div>
        @endif
    </div>
@endif
@endsection
