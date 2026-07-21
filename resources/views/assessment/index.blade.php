@extends('layouts.app')

@section('title', 'Riwayat Hasil Penilaian Saya')
@section('header', 'Riwayat Hasil Penilaian Saya')
@section('subtitle', 'Histori rapor evaluasi kinerja 360° milik Anda dari seluruh periode penilaian.')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active" aria-current="page">Riwayat Penilaian Saya</li>
@endsection

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <!-- Filter Card -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body py-3">
                <form method="GET" action="{{ route('assessment.index') }}" class="row g-2 align-items-center">
                    <div class="col-12 col-md-8">
                        <select name="period_id" class="form-select bg-light" onchange="this.form.submit()">
                            <option value="">-- Semua Periode Penilaian --</option>
                            @foreach($periods as $p)
                                <option value="{{ $p->id }}" {{ request('period_id') == $p->id ? 'selected' : '' }}>
                                    {{ $p->name }} ({{ $p->year }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-12 col-md-4">
                        @if(request('period_id'))
                            <a href="{{ route('assessment.index') }}" class="btn btn-outline-secondary w-100">
                                <i class="bi bi-x-circle me-1"></i> Reset Filter
                            </a>
                        @endif
                    </div>
                </form>
            </div>
        </div>

@php
    $posName = strtolower($employee->position?->name ?? '');
    $isKabid = ($employee->position?->level == '2' || str_contains($posName, 'kepala bidang') || str_contains($posName, 'kabid') || str_contains($posName, 'sekretaris'));
@endphp

        <!-- Content Card -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
                <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-award me-2 text-primary"></i>Histori Rapor Evaluasi Kinerja 360° Murni Pegawai</h6>
                <div class="d-flex gap-2">
                    <a href="{{ route('assessment.exportAllPdf') }}" target="_blank" class="btn btn-sm btn-danger">
                        <i class="bi bi-file-pdf me-1"></i>Ekspor Rekap PDF
                    </a>
                    <a href="{{ route('assessment.exportAllExcel') }}" class="btn btn-sm btn-success">
                        <i class="bi bi-file-excel me-1"></i>Ekspor Rekap Excel
                    </a>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover table-striped align-middle mb-0">
                        <thead>
                            <tr>
                                <th class="ps-3" style="width: 50px;">No</th>
                                <th>Periode Penilaian</th>
                                @if($isKabid)
                                    <th class="text-center">Skor Atasan (50%)</th>
                                    <th class="text-center">Skor Sejawat (30%)</th>
                                    <th class="text-center">Skor Bawahan (20%)</th>
                                @else
                                    <th class="text-center">Skor Atasan (50%)</th>
                                    <th class="text-center">Skor Sejawat (50%)</th>
                                @endif
                                <th class="text-center">Skor Akhir 360°</th>
                                <th class="text-center">Predikat Kategori</th>
                                <th class="text-end pe-3" style="width: 160px;">Ekspor Rapor</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($myResults as $index => $res)
                                <tr>
                                    <td class="ps-3 fw-semibold text-muted">{{ $myResults->firstItem() + $index }}</td>
                                    <td>
                                        <div class="fw-semibold text-dark">{{ $res->period->name ?? '-' }}</div>
                                        <small class="text-muted">Tahun {{ $res->period->year ?? '-' }}</small>
                                    </td>
                                    @if($isKabid)
                                        <td class="text-center font-monospace fs-6">
                                            {{ number_format($res->subordinate_average ?? 0, 2) }}
                                        </td>
                                        <td class="text-center font-monospace fs-6">
                                            {{ number_format($res->peer_average ?? 0, 2) }}
                                        </td>
                                        <td class="text-center font-monospace fs-6">
                                            {{ number_format($res->superior_average ?? 0, 2) }}
                                        </td>
                                    @else
                                        <td class="text-center font-monospace fs-6">
                                            {{ number_format($res->subordinate_average ?? 0, 2) }}
                                        </td>
                                        <td class="text-center font-monospace fs-6">
                                            {{ number_format($res->peer_average ?? 0, 2) }}
                                        </td>
                                    @endif
                                    <td class="text-center">
                                        @if($res->final_score !== null)
                                            <span class="fw-bold fs-5 text-primary" style="color: #1E3A5F !important;">
                                                {{ number_format($res->final_score, 2) }}
                                            </span>
                                        @else
                                            <span class="text-muted small">-</span>
                                        @endif
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
                                            <span class="text-muted small">Proses Penilaian</span>
                                        @endif
                                    </td>
                                    <td class="text-end pe-3">
                                        <div class="d-flex gap-1 justify-content-end">
                                            <a href="{{ route('assessment.exportPdf', $res->id) }}" target="_blank" class="btn btn-sm btn-outline-danger" title="Ekspor PDF">
                                                <i class="bi bi-file-pdf me-1"></i>PDF
                                            </a>
                                            <a href="{{ route('assessment.exportExcel', $res->id) }}" class="btn btn-sm btn-outline-success" title="Ekspor Excel">
                                                <i class="bi bi-file-excel me-1"></i>Excel
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="{{ $isKabid ? 8 : 7 }}" class="text-center text-muted py-4">
                                        <i class="bi bi-inbox fs-3 d-block mb-2 text-secondary"></i>
                                        Belum ada hasil evaluasi penilaian 360° untuk akun Anda.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($myResults->hasPages())
                    <div class="card-footer bg-white py-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <small class="text-muted">
                                Menampilkan {{ $myResults->firstItem() }} - {{ $myResults->lastItem() }} dari total {{ $myResults->total() }} data
                            </small>
                            {{ $myResults->withQueryString()->links() }}
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
