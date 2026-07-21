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
    <div class="alert alert-info border-0 shadow-sm d-flex align-items-center justify-content-between p-3 mb-4">
        <div class="d-flex align-items-center gap-3">
            <div class="bg-info text-white rounded p-2.5 d-flex align-items-center justify-content-center">
                <i class="bi bi-calendar-check fs-4"></i>
            </div>
            <div>
                <div class="fw-bold text-dark">{{ $activePeriod->name }}</div>
                <small class="text-muted">
                    Batas Akhir: {{ \Carbon\Carbon::parse($activePeriod->end_date)->format('d M Y') }}
                </small>
            </div>
        </div>
        <span class="badge bg-success px-3 py-2">Periode Aktif</span>
    </div>

    <!-- Section 1: Atasan Langsung -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white py-3">
            <span class="fw-semibold text-primary"><i class="bi bi-person-up me-2"></i>1. Evaluasi Atasan Langsung</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th class="ps-3">Target Atasan</th>
                            <th>Jabatan</th>
                            <th>Unit Kerja</th>
                            <th>Status Evaluasi</th>
                            <th class="text-end pe-3">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if($superior)
                            <tr>
                                <td class="ps-3">
                                    <div class="fw-semibold text-dark">{{ $superior->name }}</div>
                                    <small class="text-muted">NIP. {{ $superior->nip }}</small>
                                </td>
                                <td>{{ $superior->position->name ?? '-' }}</td>
                                <td>{{ $superior->department->name ?? '-' }}</td>
                                <td>
                                    @if($superior->assessment_status === 'COMPLETED')
                                        <span class="badge bg-success bg-opacity-10 text-success"><i class="bi bi-check-circle me-1"></i>Selesai</span>
                                    @else
                                        <span class="badge bg-warning bg-opacity-10 text-warning"><i class="bi bi-clock me-1"></i>Belum Diisi</span>
                                    @endif
                                </td>
                                <td class="text-end pe-3">
                                    @if($superior->assessment_status === 'COMPLETED')
                                        <button class="btn btn-sm btn-outline-secondary" disabled><i class="bi bi-check-lg me-1"></i>Sudah Dinilai</button>
                                    @else
                                        <a href="{{ route('transaction.assessments.create', ['target_id' => $superior->id, 'type' => 'SUPERIOR']) }}" class="btn btn-sm btn-primary">
                                            <i class="bi bi-pencil-square me-1"></i>Isi Penilaian
                                        </a>
                                    @endif
                                </td>
                            </tr>
                        @else
                            <tr>
                                <td colspan="5" class="text-center text-muted py-3">Anda tidak memiliki atasan langsung terdaftar.</td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Section 2: Rekan Sejawat -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white py-3">
            <span class="fw-semibold text-primary"><i class="bi bi-people me-2"></i>2. Evaluasi Rekan Sejawat (Peers)</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th class="ps-3">Nama Rekan Sejawat</th>
                            <th>Jabatan</th>
                            <th>Unit Kerja</th>
                            <th>Status Evaluasi</th>
                            <th class="text-end pe-3">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($peers as $peer)
                            <tr>
                                <td class="ps-3">
                                    <div class="fw-semibold text-dark">{{ $peer->name }}</div>
                                    <small class="text-muted">NIP. {{ $peer->nip }}</small>
                                </td>
                                <td>{{ $peer->position->name ?? '-' }}</td>
                                <td>{{ $peer->department->name ?? '-' }}</td>
                                <td>
                                    @if($peer->assessment_status === 'COMPLETED')
                                        <span class="badge bg-success bg-opacity-10 text-success"><i class="bi bi-check-circle me-1"></i>Selesai</span>
                                    @else
                                        <span class="badge bg-warning bg-opacity-10 text-warning"><i class="bi bi-clock me-1"></i>Belum Diisi</span>
                                    @endif
                                </td>
                                <td class="text-end pe-3">
                                    @if($peer->assessment_status === 'COMPLETED')
                                        <button class="btn btn-sm btn-outline-secondary" disabled><i class="bi bi-check-lg me-1"></i>Sudah Dinilai</button>
                                    @else
                                        <a href="{{ route('transaction.assessments.create', ['target_id' => $peer->id, 'type' => 'PEER']) }}" class="btn btn-sm btn-primary">
                                            <i class="bi bi-pencil-square me-1"></i>Isi Penilaian
                                        </a>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted py-3">Tidak ada rekan sejawat dalam unit kerja yang sama.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Section 3: Bawahan (Jika Ada) -->
    @if($subordinates->count() > 0)
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white py-3">
                <span class="fw-semibold text-primary"><i class="bi bi-person-down me-2"></i>3. Evaluasi Bawahan Langsung</span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th class="ps-3">Nama Bawahan</th>
                                <th>Jabatan</th>
                                <th>Unit Kerja</th>
                                <th>Status Evaluasi</th>
                                <th class="text-end pe-3">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($subordinates as $sub)
                                <tr>
                                    <td class="ps-3">
                                        <div class="fw-semibold text-dark">{{ $sub->name }}</div>
                                        <small class="text-muted">NIP. {{ $sub->nip }}</small>
                                    </td>
                                    <td>{{ $sub->position->name ?? '-' }}</td>
                                    <td>{{ $sub->department->name ?? '-' }}</td>
                                    <td>
                                        @if($sub->assessment_status === 'COMPLETED')
                                            <span class="badge bg-success bg-opacity-10 text-success"><i class="bi bi-check-circle me-1"></i>Selesai</span>
                                        @else
                                            <span class="badge bg-warning bg-opacity-10 text-warning"><i class="bi bi-clock me-1"></i>Belum Diisi</span>
                                        @endif
                                    </td>
                                    <td class="text-end pe-3">
                                        @if($sub->assessment_status === 'COMPLETED')
                                            <button class="btn btn-sm btn-outline-secondary" disabled><i class="bi bi-check-lg me-1"></i>Sudah Dinilai</button>
                                        @else
                                            <a href="{{ route('transaction.assessments.create', ['target_id' => $sub->id, 'type' => 'SUBORDINATE']) }}" class="btn btn-sm btn-primary">
                                                <i class="bi bi-pencil-square me-1"></i>Isi Penilaian
                                            </a>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif
@endif
@endsection
