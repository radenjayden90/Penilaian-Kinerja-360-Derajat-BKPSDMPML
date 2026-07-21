@extends('layouts.app')

@section('title', 'Profil Saya & Rapor Kinerja')
@section('header', 'Profil Saya & Visualisasi Nilai Kinerja 360°')
@section('subtitle', 'Biodata singkat pegawai dan analisis grafik penilaian kinerja 360° terbaru.')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active" aria-current="page">Profil Saya</li>
@endsection

@section('content')
<div class="row g-4">
    <!-- Left Column: Data Singkat Pegawai -->
    <div class="col-12 col-lg-5">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white py-3 border-bottom">
                <h6 class="fw-bold text-dark mb-0">
                    <i class="bi bi-person-badge text-primary me-2"></i>Data Singkat Pegawai
                </h6>
            </div>
            <div class="card-body p-4">
                <div class="text-center mb-4 pb-3 border-bottom">
                    <div class="avatar-circle bg-primary text-white mx-auto mb-3 d-flex align-items-center justify-content-center fw-bold shadow-sm" style="width: 80px; height: 80px; font-size: 2rem; border-radius: 50%; background-color: #1E3A5F !important;">
                        {{ strtoupper(substr($employee->name ?? $user->name, 0, 1)) }}
                    </div>
                    <h5 class="fw-bold text-dark mb-1">{{ $employee->name ?? $user->name }}</h5>
                    <div class="badge bg-light text-secondary border px-3 py-1 mb-2">
                        NIP. {{ $employee->nip ?? '-' }}
                    </div>
                    <div>
                        @php
                            $roleName = $employee->role->name ?? 'Pegawai';
                            $roleBadge = match($roleName) {
                                'ADMIN' => 'bg-danger',
                                'HEAD' => 'bg-warning text-dark',
                                default => 'bg-info text-dark'
                            };
                        @endphp
                        <span class="badge {{ $roleBadge }} me-1"><i class="bi bi-shield-lock me-1"></i>Role: {{ $roleName }}</span>
                        <span class="badge bg-success"><i class="bi bi-check-circle me-1"></i>Status: Aktif</span>
                    </div>
                </div>

                <div class="space-y-3">
                    <div class="d-flex align-items-center justify-content-between py-2 border-bottom">
                        <span class="text-muted"><i class="bi bi-person-workspace me-2 text-primary"></i>Jabatan</span>
                        <span class="fw-semibold text-dark text-end ms-2">{{ $employee->position->name ?? '-' }}</span>
                    </div>
                    <div class="d-flex align-items-center justify-content-between py-2 border-bottom">
                        <span class="text-muted"><i class="bi bi-building me-2 text-primary"></i>Unit Kerja</span>
                        <span class="fw-semibold text-dark text-end ms-2">{{ $employee->department->name ?? '-' }}</span>
                    </div>
                    <div class="d-flex align-items-center justify-content-between py-2 border-bottom">
                        <span class="text-muted"><i class="bi bi-person-up me-2 text-primary"></i>Atasan Langsung</span>
                        <span class="fw-semibold text-dark text-end ms-2">{{ $employee->supervisor->name ?? 'Tidak Ada (Pimpinan Top)' }}</span>
                    </div>
                    <div class="d-flex align-items-center justify-content-between py-2 border-bottom">
                        <span class="text-muted"><i class="bi bi-envelope me-2 text-primary"></i>Email</span>
                        <span class="fw-semibold text-dark ms-2">{{ $employee->email ?? '-' }}</span>
                    </div>
                    <div class="d-flex align-items-center justify-content-between py-2 border-bottom">
                        <span class="text-muted"><i class="bi bi-gender-ambiguous me-2 text-primary"></i>Jenis Kelamin</span>
                        <span class="fw-semibold text-dark ms-2">{{ ($employee->gender ?? '') === 'L' ? 'Laki-Laki' : (($employee->gender ?? '') === 'P' ? 'Perempuan' : '-') }}</span>
                    </div>
                    <div class="d-flex align-items-center justify-content-between py-2">
                        <span class="text-muted"><i class="bi bi-telephone me-2 text-primary"></i>No. Telepon / HP</span>
                        <span class="fw-semibold text-dark ms-2">{{ $employee->phone ?? '-' }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Right Column: Visualisasi Nilai Kinerja Terakhir (Chart.js) -->
    <div class="col-12 col-lg-7">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center border-bottom">
                <h6 class="fw-bold text-dark mb-0">
                    <i class="bi bi-pie-chart text-success me-2"></i>Visualisasi Nilai Kinerja 360° Terakhir
                </h6>
                @if($latestResult)
                    <span class="badge bg-light text-dark border">
                        <i class="bi bi-calendar-check me-1 text-primary"></i>{{ $latestResult->period->name ?? 'Periode Aktif' }}
                    </span>
                @endif
            </div>

            <div class="card-body p-4">
                @if($latestResult)
                    <!-- Key Summary Cards -->
                    <div class="row g-3 mb-4">
                        <div class="col-6 col-md-6">
                            <div class="p-3 rounded border bg-light text-center">
                                <small class="text-muted d-block mb-1 fw-semibold">Nilai Akhir 360°</small>
                                <span class="fs-2 fw-bold text-primary" style="color: #1E3A5F !important;">
                                    {{ number_format($latestResult->final_score ?? 0, 2) }}
                                </span>
                                <small class="text-muted d-block mt-1" style="font-size: 11px;">Skala 10 - 100</small>
                            </div>
                        </div>
                        <div class="col-6 col-md-6">
                            <div class="p-3 rounded border bg-light text-center">
                                <small class="text-muted d-block mb-1 fw-semibold">Predikat Kategori</small>
                                @php
                                    $catEnum = $latestResult->category instanceof \App\Enums\ResultCategory ? $latestResult->category : \App\Enums\ResultCategory::tryFrom($latestResult->category);
                                    $textColor = match($catEnum) {
                                        \App\Enums\ResultCategory::VERY_GOOD => 'text-success',
                                        \App\Enums\ResultCategory::GOOD => 'text-primary',
                                        \App\Enums\ResultCategory::FAIR => 'text-warning',
                                        \App\Enums\ResultCategory::NEEDS_IMPROVEMENT => 'text-danger',
                                        default => 'text-secondary'
                                    };
                                    $style = $catEnum === \App\Enums\ResultCategory::FAIR ? 'style="color: #b58900 !important;"' : '';
                                @endphp
                                <div class="mt-2 fw-semibold {{ $textColor }}" {!! $style !!}>
                                    {{ $catEnum ? $catEnum->label() : ($latestResult->category ?? '-') }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Chart Canvas -->
                    <div class="mb-3">
                        <h6 class="fw-bold text-secondary mb-3 fs-7 text-uppercase tracking-wider">
                            <i class="bi bi-bar-chart-fill me-1"></i> Breakdown Nilai Per Komponen (Skala 1-10)
                        </h6>
                        <div style="position: relative; height: 260px; width: 100%;">
                            <canvas id="profileAssessmentChart"></canvas>
                        </div>
                    </div>

                @else
                    <div class="text-center py-5 text-muted">
                        <i class="bi bi-bar-chart-line fs-1 d-block mb-3 text-secondary"></i>
                        <h6 class="fw-semibold text-dark mb-1">Belum Ada Data Penilaian 360°</h6>
                        <small>Nilai dan grafik visualisasi kinerja Anda akan muncul secara otomatis setelah penilaian periode ini selesai dikalkulasi.</small>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
@if($latestResult)
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const ctx = document.getElementById('profileAssessmentChart').getContext('2d');
        
        @php
            $posName = strtolower($employee->position?->name ?? '');
            $isKabid = ($employee->position?->level == '2' || str_contains($posName, 'kepala bidang') || str_contains($posName, 'kabid') || str_contains($posName, 'sekretaris'));
        @endphp

        @if($isKabid)
            const labels = ['Skor Atasan (50%)', 'Skor Sejawat (30%)', 'Skor Bawahan (20%)'];
            const data = [
                {{ number_format($latestResult->subordinate_average ?? 0, 2) }},
                {{ number_format($latestResult->peer_average ?? 0, 2) }},
                {{ number_format($latestResult->superior_average ?? 0, 2) }}
            ];
            const bgColors = [
                'rgba(30, 58, 95, 0.85)',
                'rgba(13, 110, 253, 0.75)',
                'rgba(108, 117, 125, 0.75)'
            ];
            const borderColors = [
                '#1E3A5F',
                '#0d6efd',
                '#6c757d'
            ];
        @else
            const labels = ['Skor Atasan (50%)', 'Skor Sejawat (50%)'];
            const data = [
                {{ number_format($latestResult->subordinate_average ?? 0, 2) }},
                {{ number_format($latestResult->peer_average ?? 0, 2) }}
            ];
            const bgColors = [
                'rgba(30, 58, 95, 0.85)',
                'rgba(13, 110, 253, 0.75)'
            ];
            const borderColors = [
                '#1E3A5F',
                '#0d6efd'
            ];
        @endif

        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Skor Penilaian',
                    data: data,
                    backgroundColor: bgColors,
                    borderColor: borderColors,
                    borderWidth: 1.5,
                    borderRadius: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return context.dataset.label + ': ' + context.parsed.y.toFixed(2) + ' / 10';
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 10,
                        ticks: {
                            stepSize: 2
                        },
                        grid: {
                            color: '#e2e8f0'
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        }
                    }
                }
            }
        });
    });
</script>
@endif
@endpush
