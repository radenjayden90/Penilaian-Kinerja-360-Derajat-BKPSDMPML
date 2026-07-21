@extends('layouts.app')

@section('title', 'Rapor Perhitungan Nilai')
@section('header', 'Rapor Perhitungan Hasil Kinerja 360°')
@section('subtitle', 'Rincian bobot dan skor agregat kinerja individu pegawai')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('transaction.calculations.index') }}">Perhitungan Nilai</a></li>
    <li class="breadcrumb-item active" aria-current="page">Rapor Individu</li>
@endsection

@section('content')
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body p-4">
        <div class="row align-items-center">
            <div class="col-12 col-md-8">
                <small class="text-uppercase fw-semibold text-primary" style="font-size: 11px;">Identitas Pegawai Evaluasi</small>
                <h4 class="fw-bold text-dark mb-1">{{ $employee->name }}</h4>
                <p class="text-muted mb-0">
                    NIP. {{ $employee->nip }} | {{ $employee->position->name ?? '-' }} | {{ $employee->department->name ?? '-' }}
                </p>
            </div>
            <div class="col-12 col-md-4 text-md-end mt-3 mt-md-0">
                <div class="bg-light p-3 rounded border text-center">
                    <small class="text-uppercase fw-semibold text-muted" style="font-size: 11px;">Nilai Akhir 360°</small>
                    <h2 class="fw-bold text-primary mb-0 mt-1" style="color: #1E3A5F !important;">
                        {{ number_format($result->final_score ?? 0, 2) }}
                    </h2>
                    @php
                        $catEnum = $result->category instanceof \App\Enums\ResultCategory ? $result->category : \App\Enums\ResultCategory::tryFrom($result->category);
                        $textColor = match($catEnum) {
                            \App\Enums\ResultCategory::VERY_GOOD => 'text-success',
                            \App\Enums\ResultCategory::GOOD => 'text-primary',
                            \App\Enums\ResultCategory::FAIR => 'text-warning',
                            \App\Enums\ResultCategory::NEEDS_IMPROVEMENT => 'text-danger',
                            default => 'text-secondary'
                        };
                        $style = $catEnum === \App\Enums\ResultCategory::FAIR ? 'style="color: #b58900 !important;"' : '';
                    @endphp
                    <div class="fw-semibold mt-1 {{ $textColor }}" {!! $style !!}>
                        Kategori: {{ $catEnum ? $catEnum->label() : $result->category }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@php
    $posName = strtolower($employee->position?->name ?? '');
    $isKabid = ($employee->position?->level == '2' || str_contains($posName, 'kepala bidang') || str_contains($posName, 'kabid') || str_contains($posName, 'sekretaris'));
@endphp

<!-- Score Breakdown Table -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white py-3 fw-semibold">
        <i class="bi bi-pie-chart me-2 text-primary"></i>Rincian Skor Berdasarkan Sumber Penilai (360 Degree)
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-3">Sumber Evaluator</th>
                        <th>Bobot (%)</th>
                        <th class="text-center">Skor Rata-Rata (0-10)</th>
                        <th class="text-center">Skor Terbobot</th>
                    </tr>
                </thead>
                <tbody>
                    @if($isKabid)
                        <tr>
                            <td class="ps-3 fw-semibold"><i class="bi bi-person-up me-2 text-primary"></i>Atasan (Kepala BKPSDM)</td>
                            <td>{{ number_format(($result->subordinate_weight ?? 0.50) * 100, 0) }}%</td>
                            <td class="text-center fw-semibold">{{ number_format($result->subordinate_average ?? 0, 2) }}</td>
                            <td class="text-center fw-bold text-dark">{{ number_format(($result->subordinate_average ?? 0) * ($result->subordinate_weight ?? 0.50), 2) }}</td>
                        </tr>
                        <tr>
                            <td class="ps-3 fw-semibold"><i class="bi bi-people me-2 text-info"></i>Rekan Sejawat (Rekan Kabid)</td>
                            <td>{{ number_format(($result->peer_weight ?? 0.30) * 100, 0) }}%</td>
                            <td class="text-center fw-semibold">{{ number_format($result->peer_average ?? 0, 2) }}</td>
                            <td class="text-center fw-bold text-dark">{{ number_format(($result->peer_average ?? 0) * ($result->peer_weight ?? 0.30), 2) }}</td>
                        </tr>
                        <tr>
                            <td class="ps-3 fw-semibold"><i class="bi bi-person-down me-2 text-warning"></i>Bawahan Langsung (Staff)</td>
                            <td>{{ number_format(($result->superior_weight ?? 0.20) * 100, 0) }}%</td>
                            <td class="text-center fw-semibold">{{ number_format($result->superior_average ?? 0, 2) }}</td>
                            <td class="text-center fw-bold text-dark">{{ number_format(($result->superior_average ?? 0) * ($result->superior_weight ?? 0.20), 2) }}</td>
                        </tr>
                    @else
                        <tr>
                            <td class="ps-3 fw-semibold"><i class="bi bi-person-up me-2 text-primary"></i>Atasan (Kepala Bidang)</td>
                            <td>{{ number_format(($result->subordinate_weight ?? 0.50) * 100, 0) }}%</td>
                            <td class="text-center fw-semibold">{{ number_format($result->subordinate_average ?? 0, 2) }}</td>
                            <td class="text-center fw-bold text-dark">{{ number_format(($result->subordinate_average ?? 0) * ($result->subordinate_weight ?? 0.50), 2) }}</td>
                        </tr>
                        <tr>
                            <td class="ps-3 fw-semibold"><i class="bi bi-people me-2 text-info"></i>Rekan Sejawat (Peer Staff)</td>
                            <td>{{ number_format(($result->peer_weight ?? 0.50) * 100, 0) }}%</td>
                            <td class="text-center fw-semibold">{{ number_format($result->peer_average ?? 0, 2) }}</td>
                            <td class="text-center fw-bold text-dark">{{ number_format(($result->peer_average ?? 0) * ($result->peer_weight ?? 0.50), 2) }}</td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="d-flex justify-content-start mb-4">
    <a href="{{ route('transaction.calculations.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i> Kembali ke Daftar Perhitungan
    </a>
</div>
@endsection
