@extends('layouts.app')

@section('title', 'Master Unit Kerja / Bidang')
@section('header', 'Master Data Unit Kerja / Bidang')
@section('subtitle', 'Kelola struktur organisasi dan bidang kerja di instansi')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('master.index') }}">Master Data</a></li>
    <li class="breadcrumb-item active" aria-current="page">Unit Kerja</li>
@endsection

@section('action_buttons')
    <a href="{{ route('master.departments.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-lg me-1"></i> Tambah Unit Kerja
    </a>
@endsection

@section('content')
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white py-3">
        <form method="GET" action="{{ route('master.departments.index') }}" class="row g-2">
            <div class="col-12 col-md-7">
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0"><i class="bi bi-search text-muted"></i></span>
                    <input type="text" name="search" class="form-control border-start-0 bg-light" placeholder="Cari nama atau kode unit kerja..." value="{{ request('search') }}">
                </div>
            </div>
            <div class="col-12 col-md-5 d-flex gap-2">
                <select name="status" class="form-select bg-light" onchange="this.form.submit()">
                    <option value="">-- Semua Status --</option>
                    <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>Aktif</option>
                    <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>Nonaktif</option>
                </select>
                @if(request('search') || request('status') !== null)
                    <a href="{{ route('master.departments.index') }}" class="btn btn-outline-secondary" title="Reset Filter">
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
                        <th>Kode</th>
                        <th>Nama Unit Kerja / Bidang</th>
                        <th>Keterangan</th>
                        <th>Status</th>
                        <th class="text-end pe-3" style="width: 140px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($departments as $index => $dept)
                        <tr>
                            <td class="ps-3 fw-semibold text-muted">{{ $departments->firstItem() + $index }}</td>
                            <td><span class="badge bg-secondary bg-opacity-10 text-dark border">{{ $dept->code ?? '-' }}</span></td>
                            <td>
                                <div class="fw-semibold text-dark">{{ $dept->name }}</div>
                            </td>
                            <td><small class="text-muted">{{ Str::limit($dept->description ?? '-', 50) }}</small></td>
                            <td>
                                @if($dept->is_active)
                                    <span class="badge bg-success bg-opacity-10 text-success">Aktif</span>
                                @else
                                    <span class="badge bg-danger bg-opacity-10 text-danger">Nonaktif</span>
                                @endif
                            </td>
                            <td class="text-end pe-3">
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('master.departments.edit', $dept) }}" class="btn btn-outline-primary" title="Edit Data">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form action="{{ route('master.departments.destroy', $dept) }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus unit kerja ini?')">
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
                                Belum ada data unit kerja ditemukan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if($departments->hasPages())
        <div class="card-footer bg-white py-3">
            {{ $departments->withQueryString()->links() }}
        </div>
    @endif
</div>
@endsection
