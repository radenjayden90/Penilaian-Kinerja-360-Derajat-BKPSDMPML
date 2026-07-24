@extends('layouts.app')

@section('title', 'Detail Pegawai')
@section('header', 'Profil Detail Pegawai ASN')
@section('subtitle', 'Rincian identitas, jabatan, dan struktur atasan-bawahan')

@section('breadcrumb')
    <li class="breadcrumb-item"><a wire:navigate href="{{ route('master.index') }}">Master Data</a></li>
    <li class="breadcrumb-item"><a wire:navigate href="{{ route('master.employees.index') }}">Pegawai</a></li>
    <li class="breadcrumb-item active" aria-current="page">Detail</li>
@endsection

@section('action_buttons')
    <a wire:navigate href="{{ route('master.employees.edit', $employee) }}" class="btn btn-primary">
        <i class="bi bi-pencil me-1"></i> Edit Pegawai
    </a>
@endsection

@section('content')
<div class="row g-4">
    <!-- Left Column: Profile Card -->
    <div class="col-12 col-md-4">
        <div class="card border-0 shadow-sm text-center p-4">
            <div class="mx-auto mb-3">
                <div class="avatar-circle mx-auto" style="width: 80px; height: 80px; font-size: 32px;">
                    {{ strtoupper(substr($employee->name, 0, 1)) }}
                </div>
            </div>
            <h5 class="fw-bold mb-1 text-dark">{{ $employee->name }}</h5>
            <div class="text-muted small mb-2">NIP. {{ $employee->nip }}</div>
            <div class="mb-3">
                <span class="badge badge-role fs-6">{{ $employee->role->name ?? 'EMPLOYEE' }}</span>
                @if($employee->is_active)
                    <span class="badge bg-success bg-opacity-10 text-success ms-1 fs-6">Aktif</span>
                @else
                    <span class="badge bg-danger bg-opacity-10 text-danger ms-1 fs-6">Nonaktif</span>
                @endif
            </div>

            <hr class="my-3">

            <div class="text-start">
                <div class="mb-2">
                    <small class="text-muted d-block">Unit Kerja / Bidang</small>
                    <span class="fw-semibold text-dark">{{ $employee->department->name ?? '-' }}</span>
                </div>
                <div class="mb-2">
                    <small class="text-muted d-block">Jabatan</small>
                    <span class="fw-semibold text-dark">{{ $employee->position->name ?? '-' }}</span>
                </div>
                <div>
                    <small class="text-muted d-block">Atasan Langsung</small>
                    <span class="fw-semibold text-dark">{{ $employee->supervisor->name ?? 'Tidak Ada (Kepala Instansi)' }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Right Column: Details & Subordinates -->
    <div class="col-12 col-md-8">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white py-3 fw-semibold">
                <i class="bi bi-person-vcard me-2 text-primary"></i>Informasi Kontak & Akun
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-12 col-sm-6">
                        <small class="text-muted d-block">Email Official</small>
                        <span class="fw-medium text-dark">{{ $employee->email }}</span>
                    </div>
                    <div class="col-12 col-sm-6">
                        <small class="text-muted d-block">Nomor Telepon / WA</small>
                        <span class="fw-medium text-dark">{{ $employee->phone ?? '-' }}</span>
                    </div>
                    <div class="col-12 col-sm-6">
                        <small class="text-muted d-block">Jenis Kelamin</small>
                        <span class="fw-medium text-dark">
                            {{ $employee->gender === 'L' ? 'Laki-laki' : ($employee->gender === 'P' ? 'Perempuan' : '-') }}
                        </span>
                    </div>
                    <div class="col-12 col-sm-6">
                        <small class="text-muted d-block">Terdaftar Sejak</small>
                        <span class="fw-medium text-dark">{{ $employee->created_at ? $employee->created_at->format('d M Y') : '-' }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Subordinates Table -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3 fw-semibold">
                <i class="bi bi-people me-2 text-primary"></i>Daftar Bawahan Langsung ({{ $employee->subordinates->count() }})
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th class="ps-3">Nama Bawahan</th>
                                <th>Jabatan</th>
                                <th>Unit Kerja</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($employee->subordinates as $sub)
                                <tr>
                                    <td class="ps-3">
                                        <div class="fw-semibold text-dark">{{ $sub->name }}</div>
                                        <small class="text-muted">NIP. {{ $sub->nip }}</small>
                                    </td>
                                    <td>{{ $sub->position->name ?? '-' }}</td>
                                    <td>{{ $sub->department->name ?? '-' }}</td>
                                    <td>
                                        @if($sub->is_active)
                                            <span class="badge bg-success bg-opacity-10 text-success">Aktif</span>
                                        @else
                                            <span class="badge bg-danger bg-opacity-10 text-danger">Nonaktif</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-4">Pegawai ini tidak memiliki bawahan langsung.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
