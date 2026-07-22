@extends('layouts.app')

@section('title', 'Monitoring Penilaian')
@section('header', 'Monitoring Progres Penilaian 360°')
@section('subtitle', 'Pantau kelengkapan partisipasi pengisian kuesioner oleh pegawai secara real-time')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active" aria-current="page">Monitoring</li>
@endsection

@section('content')
<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white py-3">
        <form method="GET" action="{{ route('transaction.monitoring.index') }}" class="row g-2">
            <div class="col-12 col-md-6">
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0"><i class="bi bi-search text-muted"></i></span>
                    <input type="text" name="search" class="form-control border-start-0 bg-light" placeholder="Cari NIP atau nama pegawai evaluator..." value="{{ request('search') }}">
                </div>
            </div>
            <div class="col-12 col-md-4">
                <select name="department_id" class="form-select bg-light" onchange="this.form.submit()">
                    <option value="">-- Semua Unit Kerja --</option>
                    @foreach($departments as $dept)
                        <option value="{{ $dept->id }}" {{ request('department_id') == $dept->id ? 'selected' : '' }}>{{ $dept->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-12 col-md-2">
                @if(request('search') || request('department_id'))
                    <a href="{{ route('transaction.monitoring.index') }}" class="btn btn-outline-secondary w-100">
                        <i class="bi bi-x-circle me-1"></i> Reset
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
                        <th>Pegawai Evaluator</th>
                        <th>Unit Kerja & Jabatan</th>
                        <th>Evaluasi Atasan</th>
                        <th>Evaluasi Sejawat</th>
                        <th>Evaluasi Bawahan</th>
                        <th class="text-center">Total Diisi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($employees as $index => $emp)
                        <tr>
                            <td class="ps-3 fw-semibold text-muted">{{ $employees->firstItem() + $index }}</td>
                            <td>
                                <div class="fw-semibold text-dark">{{ $emp->name }}</div>
                                <small class="text-muted">NIP. {{ $emp->nip }}</small>
                            </td>
                            <td>
                                <div class="small fw-medium text-dark">{{ $emp->department->name ?? '-' }}</div>
                                <small class="text-muted">{{ $emp->position->name ?? '-' }}</small>
                            </td>
                            <td>
                                @if(str_contains($emp->monitoring_superior, 'Sudah'))
                                    <span class="badge bg-success bg-opacity-10 text-success"><i class="bi bi-check-circle me-1"></i>{{ $emp->monitoring_superior }}</span>
                                @else
                                    <span class="badge bg-warning bg-opacity-10 text-warning"><i class="bi bi-clock me-1"></i>Belum</span>
                                @endif
                            </td>
                            <td>
                                @if(str_contains($emp->monitoring_peer, 'Sudah'))
                                    <span class="badge bg-success bg-opacity-10 text-success"><i class="bi bi-check-circle me-1"></i>{{ $emp->monitoring_peer }}</span>
                                @else
                                    <span class="badge bg-warning bg-opacity-10 text-warning"><i class="bi bi-clock me-1"></i>Belum</span>
                                @endif
                            </td>
                            <td>
                                @if(str_contains($emp->monitoring_subordinate, 'Sudah'))
                                    <span class="badge bg-success bg-opacity-10 text-success"><i class="bi bi-check-circle me-1"></i>{{ $emp->monitoring_subordinate }}</span>
                                @else
                                    <span class="badge bg-secondary bg-opacity-10 text-secondary">N/A / Belum</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <span class="badge bg-primary px-3 py-1 fs-6" style="background-color: #1E3A5F !important;">
                                    {{ $emp->total_assessed ?? 0 }} Form
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">
                                <i class="bi bi-inbox fs-3 d-block mb-2 text-secondary"></i>
                                Belum ada data monitoring penilaian.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if($employees->hasPages())
        <div class="card-footer bg-white py-3">
            {{ $employees->withQueryString()->links() }}
        </div>
    @endif
</div>
@endsection
