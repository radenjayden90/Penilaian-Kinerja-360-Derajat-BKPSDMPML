@extends('layouts.app')

@section('title', 'Master Data Jabatan')
@section('header', 'Master Data Jabatan')
@section('subtitle', 'Kelola data seluruh jabatan ASN di lingkungan instansi')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('master.index') }}">Master Data</a></li>
    <li class="breadcrumb-item active" aria-current="page">Jabatan</li>
@endsection

@section('action_buttons')
    <a href="{{ route('master.positions.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-lg me-1"></i> Tambah Jabatan
    </a>
@endsection

@section('content')
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white py-3">
        <form method="GET" action="{{ route('master.positions.index') }}" class="row g-2">
            <div class="col-12 col-md-5">
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0"><i class="bi bi-search text-muted"></i></span>
                    <input type="text" name="search" class="form-control border-start-0 bg-light" placeholder="Cari nama jabatan..." value="{{ request('search') }}" style="text-overflow: ellipsis; overflow: hidden; white-space: nowrap; padding-right: 1.5rem;">
                </div>
            </div>
            <div class="col-12 col-md-4">
                <select name="department_id" class="form-select bg-light" onchange="this.form.submit()" style="text-overflow: ellipsis; overflow: hidden; white-space: nowrap; padding-right: 2.5rem;">
                    <option value="">-- Semua Unit Kerja / Bidang --</option>
                    @foreach($departments as $dept)
                        <option value="{{ $dept->id }}" {{ request('department_id') == $dept->id ? 'selected' : '' }}>{{ $dept->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-12 col-md-3 d-flex gap-2">
                <select name="status" class="form-select bg-light" onchange="this.form.submit()" style="text-overflow: ellipsis; overflow: hidden; white-space: nowrap; padding-right: 2.5rem;">
                    <option value="">-- Semua Status --</option>
                    <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>Aktif</option>
                    <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>Nonaktif</option>
                </select>
                @if(request('search') || request('department_id') || request('status') !== null)
                    <a href="{{ route('master.positions.index') }}" class="btn btn-outline-secondary" title="Reset Filter">
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
                        <th>Nama Jabatan</th>
                        <th>Unit Kerja / Bidang</th>
                        <th>Level</th>
                        <th>Status</th>
                        <th class="text-end pe-3" style="width: 140px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($positions as $index => $pos)
                        <tr>
                            <td class="ps-3 fw-semibold text-muted">{{ $positions->firstItem() + $index }}</td>
                            <td>
                                <div class="fw-semibold text-dark">{{ $pos->name }}</div>
                            </td>
                            <td>{{ $pos->department->name ?? '-' }}</td>
                            <td><span class="badge bg-light text-dark border">{{ $pos->level ?? '-' }}</span></td>
                            <td>
                                @if($pos->is_active)
                                    <span class="badge bg-success bg-opacity-10 text-success">Aktif</span>
                                @else
                                    <span class="badge bg-danger bg-opacity-10 text-danger">Nonaktif</span>
                                @endif
                            </td>
                            <td class="text-end pe-3">
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('master.positions.edit', $pos) }}" class="btn btn-outline-primary" title="Edit Data">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form action="{{ route('master.positions.destroy', $pos) }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus jabatan ini?')">
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
                            <td colspan="6" class="text-center text-muted py-4">
                                <i class="bi bi-inbox fs-3 d-block mb-2 text-secondary"></i>
                                Belum ada data jabatan ditemukan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if($positions->hasPages())
        <div class="card-footer bg-white py-3">
            {{ $positions->withQueryString()->links() }}
        </div>
    @endif
</div>
@endsection
