@extends('layouts.app')

@section('title', 'Master Kategori Penilaian')
@section('header', 'Kategori / Aspek Penilaian 360°')
@section('subtitle', 'Kelola kelompok aspek kompetensi penilaian kinerja ASN')

@section('breadcrumb')
    <li class="breadcrumb-item"><a wire:navigate href="{{ route('master.index') }}">Master Data</a></li>
    <li class="breadcrumb-item"><a wire:navigate href="{{ route('master.assessment-indicators.index') }}">Pertanyaan</a></li>
    <li class="breadcrumb-item active" aria-current="page">Kategori Aspek</li>
@endsection

@section('action_buttons')
    <a wire:navigate href="{{ route('master.assessment-categories.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-lg me-1"></i> Tambah Kategori Aspek
    </a>
@endsection

@section('content')
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white py-3">
        <form method="GET" action="{{ route('master.assessment-categories.index') }}" class="row g-2" x-data @submit.prevent="Livewire.navigate($el.action + '?' + new URLSearchParams(new FormData($el)).toString())">
            <div class="col-12 col-md-8">
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0"><i class="bi bi-search text-muted"></i></span>
                    <input type="text" name="search" class="form-control border-start-0 bg-light" placeholder="Cari nama atau deskripsi kategori..." value="{{ request('search') }}">
                </div>
            </div>
            <div class="col-12 col-md-4 d-flex gap-2">
                <select name="status" class="form-select bg-light" onchange="this.form.requestSubmit()">
                    <option value="">-- Status --</option>
                    <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>Aktif</option>
                    <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>Nonaktif</option>
                </select>
                @if(request('search') || request('status') !== null)
                    <a href="{{ route('master.assessment-categories.index') }}" class="btn btn-outline-secondary" title="Reset Filter" wire:navigate>
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
                        <th class="ps-3" style="width: 50px;">Urutan</th>
                        <th>Kode</th>
                        <th>Nama Aspek Kategori</th>
                        <th>Bobot (%)</th>
                        <th>Status</th>
                        <th class="text-end pe-3" style="width: 140px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($categories as $index => $cat)
                        <tr>
                            <td class="ps-3 fw-semibold text-muted">{{ $cat->display_order ?? ($categories->firstItem() + $index) }}</td>
                            <td><span class="badge bg-secondary bg-opacity-10 text-dark border">{{ $cat->code ?? '-' }}</span></td>
                            <td>
                                <div class="fw-semibold text-dark">{{ $cat->name }}</div>
                                @if($cat->description)
                                    <small class="text-muted">{{ Str::limit($cat->description, 60) }}</small>
                                @endif
                            </td>
                            <td><span class="badge bg-light text-dark border">{{ $cat->weight ?? 0 }}%</span></td>
                            <td>
                                @if($cat->is_active)
                                    <span class="badge bg-success bg-opacity-10 text-success">Aktif</span>
                                @else
                                    <span class="badge bg-danger bg-opacity-10 text-danger">Nonaktif</span>
                                @endif
                            </td>
                            <td class="text-end pe-3">
                                <div class="btn-group btn-group-sm">
                                    <a wire:navigate href="{{ route('master.assessment-categories.edit', $cat) }}" class="btn btn-outline-primary" title="Edit Kategori">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form action="{{ route('master.assessment-categories.destroy', $cat) }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus kategori ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-outline-danger" title="Hapus Kategori">
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
                                Belum ada data kategori aspek penilaian.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if($categories->hasPages())
        <div class="card-footer bg-white py-3">
            {{ $categories->withQueryString()->links() }}
        </div>
    @endif
</div>
@endsection
