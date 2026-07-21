@extends('layouts.app')

@section('title', 'Penilaian Saya')
@section('header', 'Form Penilaian Kinerja 360°')
@section('subtitle', 'Daftar instrumen evaluasi kuesioner yang harus Anda isi pada periode aktif')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active" aria-current="page">Penilaian Saya</li>
@endsection

@section('content')
@if(!$activePeriod)
    <div class="alert alert-warning border-0 shadow-sm p-4 text-center">
        <i class="bi bi-calendar-x fs-1 text-warning d-block mb-2"></i>
        <h5 class="fw-bold">Tidak Ada Periode Penilaian Aktif</h5>
        <p class="text-muted mb-0">Saat ini belum ada periode penilaian 360° yang sedang berlangsung. Silakan hubungi Administrator BKPSDM.</p>
    </div>
@else
    <!-- Active Period Banner -->
    <div class="alert alert-info border-0 shadow-sm d-flex align-items-center justify-content-between p-3 mb-4 rounded-3">
        <div class="d-flex align-items-center gap-3">
            <div class="bg-primary text-white rounded-3 p-2.5 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                <i class="bi bi-calendar-check fs-4"></i>
            </div>
            <div>
                <div class="fw-bold text-dark fs-6">{{ $activePeriod->name }}</div>
                <small class="text-muted">
                    <i class="bi bi-clock me-1"></i>Batas Akhir: {{ \Carbon\Carbon::parse($activePeriod->end_date)->format('d M Y, H:i') }} WIB
                </small>
            </div>
        </div>
        <span class="badge bg-success px-3 py-2 fs-6">Periode Aktif</span>
    </div>

    @php
        $posName = strtolower($employee->position?->name ?? '');
        $isLevel1 = $employee->position?->level == '1' || str_contains($posName, 'kepala bkpsdm');
        $isLevel2 = $employee->position?->level == '2' || str_contains($posName, 'kepala bidang') || str_contains($posName, 'kabid');
        $hideSuperior = $isLevel1 || $isLevel2;
        $hidePeers = $isLevel1;
    @endphp

    <!-- Section 1: Atasan Langsung -->
    @if(!$hideSuperior)
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white py-3 border-bottom">
            <div class="d-flex align-items-center justify-content-between">
                <span class="fw-bold text-primary fs-6"><i class="bi bi-person-up me-2"></i>1. Evaluasi Atasan Langsung</span>
                <span class="badge bg-light text-secondary">Atasan</span>
            </div>
        </div>
        <div class="card-body p-3">
            @if($superior)
                <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-3">
                    <div class="col">
                        <div class="card h-100 border shadow-sm rounded-3">
                            <div class="card-body p-3">
                                <div class="d-flex align-items-start gap-3 mb-3">
                                    <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center fw-bold fs-5" style="width: 48px; height: 48px; min-width: 48px;">
                                        {{ strtoupper(substr($superior->name, 0, 1)) }}
                                    </div>
                                    <div class="flex-grow-1 min-w-0">
                                        <h6 class="fw-bold text-dark mb-1 text-truncate" title="{{ $superior->name }}">{{ $superior->name }}</h6>
                                        <small class="text-muted d-block text-truncate">NIP. {{ $superior->nip }}</small>
                                    </div>
                                </div>
                                <div class="bg-light p-2.5 rounded-2 mb-3 fs-7">
                                    <div class="d-flex justify-content-between mb-1">
                                        <span class="text-muted"><i class="bi bi-briefcase me-1"></i>Jabatan:</span>
                                        <span class="fw-semibold text-dark text-end ms-2">{{ $superior->position->name ?? '-' }}</span>
                                    </div>
                                    <div class="d-flex justify-content-between">
                                        <span class="text-muted"><i class="bi bi-building me-1"></i>Divisi:</span>
                                        <span class="fw-semibold text-dark text-end ms-2">{{ $superior->department->name ?? '-' }}</span>
                                    </div>
                                </div>
                                <div class="d-flex align-items-center justify-content-between pt-1 border-top">
                                    <span class="fs-7 text-muted">Status Evaluasi:</span>
                                    @if($superior->assessment_status === 'COMPLETED')
                                        <span class="badge bg-success bg-opacity-10 text-success"><i class="bi bi-check-circle me-1"></i>Selesai</span>
                                    @else
                                        <span class="badge bg-warning bg-opacity-10 text-warning"><i class="bi bi-clock me-1"></i>Belum Diisi</span>
                                    @endif
                                </div>
                            </div>
                            <div class="card-footer bg-white border-0 pt-0 pb-3 px-3">
                                @if($superior->assessment_status === 'COMPLETED')
                                    <button class="btn btn-sm btn-outline-secondary w-100" disabled><i class="bi bi-check-lg me-1"></i>Sudah Dinilai</button>
                                @else
                                    <a href="{{ route('transaction.assessments.create', ['target_id' => $superior->id, 'type' => 'SUPERIOR']) }}" class="btn btn-sm btn-primary w-100">
                                        <i class="bi bi-pencil-square me-1"></i>Isi Penilaian
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <div class="text-center text-muted py-4">
                    <i class="bi bi-person-x fs-3 d-block mb-1 text-secondary"></i>
                    Anda tidak memiliki atasan langsung terdaftar.
                </div>
            @endif
        </div>
    </div>
    @endif

    <!-- Section 2: Rekan Sejawat -->
    @if(!$hidePeers)
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white py-3 border-bottom">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="fw-bold text-primary fs-6"><i class="bi bi-people me-2"></i>2. Evaluasi Rekan Sejawat (Peers)</span>
                    <small class="text-muted d-block ms-4">Daftar rekan kerja (termasuk lintas divisi/bidang). Kuota maksimal 3 penilai per pegawai.</small>
                </div>
                <span class="badge bg-primary bg-opacity-10 text-primary">{{ $peers->count() }} Orang Rekan</span>
            </div>
        </div>
        <div class="card-body p-3">
            <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-3">
                @forelse($peers as $peer)
                    <div class="col">
                        @if($peer->assessment_status === 'FULL')
                            {{-- Greyed-out / Muted Card for Full Quota (3/3) --}}
                            <div class="card h-100 border bg-light opacity-75 rounded-3">
                                <div class="card-body p-3">
                                    <div class="d-flex align-items-start gap-3 mb-3">
                                        <div class="bg-secondary bg-opacity-25 text-secondary rounded-circle d-flex align-items-center justify-content-center fw-bold fs-5" style="width: 48px; height: 48px; min-width: 48px;">
                                            {{ strtoupper(substr($peer->name, 0, 1)) }}
                                        </div>
                                        <div class="flex-grow-1 min-w-0">
                                            <h6 class="fw-bold text-secondary mb-1 text-truncate" title="{{ $peer->name }}">{{ $peer->name }}</h6>
                                            <small class="text-muted d-block text-truncate">NIP. {{ $peer->nip }}</small>
                                        </div>
                                    </div>
                                    <div class="bg-body-secondary p-2.5 rounded-2 mb-3 fs-7">
                                        <div class="d-flex justify-content-between mb-1">
                                            <span class="text-muted"><i class="bi bi-briefcase me-1"></i>Jabatan:</span>
                                            <span class="fw-semibold text-secondary text-end ms-2">{{ $peer->position->name ?? '-' }}</span>
                                        </div>
                                        <div class="d-flex justify-content-between">
                                            <span class="text-muted"><i class="bi bi-building me-1"></i>Divisi:</span>
                                            <span class="fw-semibold text-secondary text-end ms-2">{{ $peer->department->name ?? '-' }}</span>
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-center justify-content-between pt-1 border-top border-secondary-subtle">
                                        <span class="fs-7 text-muted">Kuota Penilai:</span>
                                        <span class="badge bg-secondary bg-opacity-25 text-secondary"><i class="bi bi-lock me-1"></i>Kuota Penuh (3/3)</span>
                                    </div>
                                </div>
                                <div class="card-footer bg-transparent border-0 pt-0 pb-3 px-3">
                                    <button class="btn btn-sm btn-secondary w-100 opacity-75" disabled>
                                        <i class="bi bi-lock me-1"></i>Kuota 3/3 Terpenuhi
                                    </button>
                                </div>
                            </div>
                        @else
                            {{-- Active / Completed Card --}}
                            <div class="card h-100 border shadow-sm rounded-3">
                                <div class="card-body p-3">
                                    <div class="d-flex align-items-start gap-3 mb-3">
                                        <div class="bg-info bg-opacity-10 text-info rounded-circle d-flex align-items-center justify-content-center fw-bold fs-5" style="width: 48px; height: 48px; min-width: 48px;">
                                            {{ strtoupper(substr($peer->name, 0, 1)) }}
                                        </div>
                                        <div class="flex-grow-1 min-w-0">
                                            <h6 class="fw-bold text-dark mb-1 text-truncate" title="{{ $peer->name }}">{{ $peer->name }}</h6>
                                            <small class="text-muted d-block text-truncate">NIP. {{ $peer->nip }}</small>
                                        </div>
                                    </div>
                                    <div class="bg-light p-2.5 rounded-2 mb-3 fs-7">
                                        <div class="d-flex justify-content-between mb-1">
                                            <span class="text-muted"><i class="bi bi-briefcase me-1"></i>Jabatan:</span>
                                            <span class="fw-semibold text-dark text-end ms-2">{{ $peer->position->name ?? '-' }}</span>
                                        </div>
                                        <div class="d-flex justify-content-between">
                                            <span class="text-muted"><i class="bi bi-building me-1"></i>Divisi:</span>
                                            <span class="fw-semibold text-dark text-end ms-2">{{ $peer->department->name ?? '-' }}</span>
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-center justify-content-between pt-1 border-top">
                                        <small class="text-muted fs-7">
                                            <i class="bi bi-people me-1"></i>Penilai: <strong>{{ $peer->received_assessments_count ?? 0 }}/3</strong>
                                        </small>
                                        @if($peer->assessment_status === 'COMPLETED')
                                            <span class="badge bg-success bg-opacity-10 text-success"><i class="bi bi-check-circle me-1"></i>Selesai</span>
                                        @else
                                            <span class="badge bg-warning bg-opacity-10 text-warning"><i class="bi bi-clock me-1"></i>Belum Diisi</span>
                                        @endif
                                    </div>
                                </div>
                                <div class="card-footer bg-white border-0 pt-0 pb-3 px-3">
                                    @if($peer->assessment_status === 'COMPLETED')
                                        <button class="btn btn-sm btn-outline-secondary w-100" disabled><i class="bi bi-check-lg me-1"></i>Sudah Dinilai</button>
                                    @else
                                        <a href="{{ route('transaction.assessments.create', ['target_id' => $peer->id, 'type' => 'PEER']) }}" class="btn btn-sm btn-primary w-100">
                                            <i class="bi bi-pencil-square me-1"></i>Isi Penilaian
                                        </a>
                                    @endif
                                </div>
                            </div>
                        @endif
                    </div>
                @empty
                    <div class="col-12 w-100">
                        <div class="text-center text-muted py-4">
                            <i class="bi bi-people-fill fs-3 d-block mb-1 text-secondary"></i>
                            Tidak ada rekan kerja yang terdaftar.
                        </div>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
    @endif

    <!-- Section 3: Bawahan (Jika Ada) -->
    @if($subordinates->count() > 0)
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white py-3 border-bottom">
                <div class="d-flex align-items-center justify-content-between">
                    <span class="fw-bold text-primary fs-6"><i class="bi bi-person-down me-2"></i>3. Evaluasi Bawahan Langsung</span>
                    <span class="badge bg-light text-secondary">{{ $subordinates->count() }} Bawahan</span>
                </div>
            </div>
            <div class="card-body p-3">
                <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-3">
                    @foreach($subordinates as $sub)
                        <div class="col">
                            <div class="card h-100 border shadow-sm rounded-3">
                                <div class="card-body p-3">
                                    <div class="d-flex align-items-start gap-3 mb-3">
                                        <div class="bg-success bg-opacity-10 text-success rounded-circle d-flex align-items-center justify-content-center fw-bold fs-5" style="width: 48px; height: 48px; min-width: 48px;">
                                            {{ strtoupper(substr($sub->name, 0, 1)) }}
                                        </div>
                                        <div class="flex-grow-1 min-w-0">
                                            <h6 class="fw-bold text-dark mb-1 text-truncate" title="{{ $sub->name }}">{{ $sub->name }}</h6>
                                            <small class="text-muted d-block text-truncate">NIP. {{ $sub->nip }}</small>
                                        </div>
                                    </div>
                                    <div class="bg-light p-2.5 rounded-2 mb-3 fs-7">
                                        <div class="d-flex justify-content-between mb-1">
                                            <span class="text-muted"><i class="bi bi-briefcase me-1"></i>Jabatan:</span>
                                            <span class="fw-semibold text-dark text-end ms-2">{{ $sub->position->name ?? '-' }}</span>
                                        </div>
                                        <div class="d-flex justify-content-between">
                                            <span class="text-muted"><i class="bi bi-building me-1"></i>Divisi:</span>
                                            <span class="fw-semibold text-dark text-end ms-2">{{ $sub->department->name ?? '-' }}</span>
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-center justify-content-between pt-1 border-top">
                                        <span class="fs-7 text-muted">Status Evaluasi:</span>
                                        @if($sub->assessment_status === 'COMPLETED')
                                            <span class="badge bg-success bg-opacity-10 text-success"><i class="bi bi-check-circle me-1"></i>Selesai</span>
                                        @else
                                            <span class="badge bg-warning bg-opacity-10 text-warning"><i class="bi bi-clock me-1"></i>Belum Diisi</span>
                                        @endif
                                    </div>
                                </div>
                                <div class="card-footer bg-white border-0 pt-0 pb-3 px-3">
                                    @if($sub->assessment_status === 'COMPLETED')
                                        <button class="btn btn-sm btn-outline-secondary w-100" disabled><i class="bi bi-check-lg me-1"></i>Sudah Dinilai</button>
                                    @else
                                        <a href="{{ route('transaction.assessments.create', ['target_id' => $sub->id, 'type' => 'SUBORDINATE']) }}" class="btn btn-sm btn-primary w-100">
                                            <i class="bi bi-pencil-square me-1"></i>Isi Penilaian
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif
@endif
@endsection
