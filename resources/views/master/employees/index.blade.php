@extends('layouts.app')

@section('title', 'Master Data Pegawai')
@section('header', 'Master Data Pegawai ASN')
@section('subtitle', 'Kelola informasi identitas, jabatan, unit kerja, dan atasan pegawai')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('master.index') }}">Master Data</a></li>
    <li class="breadcrumb-item active" aria-current="page">Pegawai</li>
@endsection

@section('action_buttons')
    <a href="{{ route('master.employees.create') }}" class="btn btn-primary">
        <i class="bi bi-person-plus me-1"></i> Tambah Pegawai
    </a>
@endsection

@section('content')
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white py-3">
        <form method="GET" action="{{ route('master.employees.index') }}" class="row g-2">
            <div class="col-12 col-md-4">
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0"><i class="bi bi-search text-muted"></i></span>
                    <input type="text" name="search" class="form-control border-start-0 bg-light" placeholder="Cari Nama / NIP / Email..." value="{{ request('search') }}">
                </div>
            </div>
            <div class="col-12 col-md-3">
                <select name="department_id" class="form-select bg-light" onchange="this.form.submit()">
                    <option value="">-- Semua Unit Kerja --</option>
                    @foreach($departments as $dept)
                        <option value="{{ $dept->id }}" {{ request('department_id') == $dept->id ? 'selected' : '' }}>{{ $dept->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-12 col-md-3">
                <select name="position_id" class="form-select bg-light" onchange="this.form.submit()">
                    <option value="">-- Semua Jabatan --</option>
                    @foreach($positions as $pos)
                        <option value="{{ $pos->id }}" {{ request('position_id') == $pos->id ? 'selected' : '' }}>{{ $pos->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-12 col-md-2 d-flex gap-2">
                <select name="status" class="form-select bg-light" onchange="this.form.submit()">
                    <option value="">-- Status --</option>
                    <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>Aktif</option>
                    <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>Nonaktif</option>
                </select>
                @if(request('search') || request('department_id') || request('position_id') || request('status') !== null)
                    <a href="{{ route('master.employees.index') }}" class="btn btn-outline-secondary" title="Reset Filter">
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
                        <th>Jabatan & Unit Kerja</th>
                        <th>Atasan Langsung</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th class="text-end pe-3" style="width: 150px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($employees as $index => $emp)
                        <tr>
                            <td class="ps-3 fw-semibold text-muted">{{ $employees->firstItem() + $index }}</td>
                            <td>
                                <div class="fw-semibold text-dark">{{ $emp->name }}</div>
                                <small class="text-muted">NIP. {{ $emp->nip }} | {{ $emp->email }}</small>
                            </td>
                            <td>
                                <div class="fw-medium text-dark">{{ $emp->position->name ?? '-' }}</div>
                                <small class="text-muted">{{ $emp->department->name ?? '-' }}</small>
                            </td>
                            <td>
                                @if($emp->supervisor)
                                    <div class="small fw-medium text-dark">{{ $emp->supervisor->name }}</div>
                                    <small class="text-muted">NIP. {{ $emp->supervisor->nip }}</small>
                                @else
                                    <span class="text-muted small">-</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge badge-role">
                                    {{ $emp->role->name ?? 'EMPLOYEE' }}
                                </span>
                            </td>
                            <td>
                                @if($emp->is_active)
                                    <span class="badge bg-success bg-opacity-10 text-success">Aktif</span>
                                @else
                                    <span class="badge bg-danger bg-opacity-10 text-danger">Nonaktif</span>
                                @endif
                            </td>
                            <td class="text-end pe-3">
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('master.employees.show', $emp) }}" class="btn btn-outline-info" title="Detail Profile">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="{{ route('master.employees.edit', $emp) }}" class="btn btn-outline-primary" title="Edit Data">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form action="{{ route('master.employees.destroy', $emp) }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data pegawai ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-outline-danger" title="Hapus Data">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">
                                <i class="bi bi-inbox fs-3 d-block mb-2 text-secondary"></i>
                                Belum ada data pegawai ditemukan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if($employees->hasPages())
        <div class="card-footer bg-white py-3">
            <div class="d-flex justify-content-between align-items-center">
                <small class="text-muted">
                    Menampilkan {{ $employees->firstItem() }} - {{ $employees->lastItem() }} dari total {{ $employees->total() }} data
                </small>
                {{ $employees->withQueryString()->links() }}
            </div>
        </div>
    @endif
</div>
@endsection
