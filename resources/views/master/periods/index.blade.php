@extends('layouts.app')

@section('title', 'Periode Penilaian')
@section('header', 'Kelola Periode Penilaian 360°')
@section('subtitle', 'Pengaturan jadwal pelaksanaan evaluasi kinerja ASN semester/tahunan')

@section('breadcrumb')
    <li class="breadcrumb-item"><a wire:navigate href="{{ route('master.index') }}">Master Data</a></li>
    <li class="breadcrumb-item active" aria-current="page">Periode Penilaian</li>
@endsection

@section('action_buttons')
    <a wire:navigate href="{{ route('master.periods.create') }}" class="btn btn-primary">
        <i class="bi bi-calendar-plus me-1"></i> Buka Periode Baru
    </a>
@endsection

@section('content')
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white py-3">
        <form method="GET" action="{{ route('master.periods.index') }}" class="row g-2" x-data @submit.prevent="Livewire.navigate($el.action + '?' + new URLSearchParams(new FormData($el)).toString())">
            <div class="col-12 col-md-5">
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0"><i class="bi bi-search text-muted"></i></span>
                    <input type="text" name="search" class="form-control border-start-0 bg-light" placeholder="Cari nama periode..." value="{{ request('search') }}">
                </div>
            </div>
            <div class="col-12 col-md-3">
                <select name="year" class="form-select bg-light" onchange="this.form.requestSubmit()">
                    <option value="">-- Semua Tahun --</option>
                    @for($y = date('Y') + 1; $y >= 2024; $y--)
                        <option value="{{ $y }}" {{ request('year') == $y ? 'selected' : '' }}>Tahun {{ $y }}</option>
                    @endfor
                </select>
            </div>
            <div class="col-12 col-md-4 d-flex gap-2">
                <select name="status" class="form-select bg-light" onchange="this.form.requestSubmit()">
                    <option value="">-- Status --</option>
                    <option value="OPEN" {{ request('status') === 'OPEN' ? 'selected' : '' }}>Aktif / Terbuka</option>
                    <option value="CLOSED" {{ request('status') === 'CLOSED' ? 'selected' : '' }}>Selesai / Ditutup</option>
                    <option value="ARCHIVED" {{ request('status') === 'ARCHIVED' ? 'selected' : '' }}>Arsip</option>
                </select>
                @if(request('search') || request('year') || request('status'))
                    <a href="{{ route('master.periods.index') }}" class="btn btn-outline-secondary" title="Reset Filter" wire:navigate>
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
                        <th>Nama Periode</th>
                        <th>Tahun / Bulan</th>
                        <th>Jadwal Pelaksanaan (Tanggal & Jam)</th>
                        <th>Status Periode</th>
                        <th class="text-end pe-3" style="width: 140px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($periods as $index => $period)
                        <tr>
                            <td class="ps-3 fw-semibold text-muted">{{ $periods->firstItem() + $index }}</td>
                            <td>
                                <div class="fw-semibold text-dark">{{ $period->name }}</div>
                            </td>
                            <td>Tahun {{ $period->year }} (Bulan {{ $period->month }})</td>
                            <td>
                                <small class="text-dark d-block">
                                    <i class="bi bi-clock text-primary me-1"></i>
                                    <strong>Mulai:</strong> {{ \Carbon\Carbon::parse($period->start_date)->format('d M Y, H:i') }} WIB
                                </small>
                                <small class="text-dark d-block">
                                    <i class="bi bi-clock-history text-danger me-1"></i>
                                    <strong>Selesai:</strong> {{ \Carbon\Carbon::parse($period->end_date)->format('d M Y, H:i') }} WIB
                                </small>
                            </td>
                            <td>
                                @if($period->is_active)
                                    <span class="badge bg-success bg-opacity-10 text-success px-3 py-1 fs-6">
                                        <i class="bi bi-check-circle me-1"></i>Aktif (OPEN)
                                    </span>
                                @elseif($period->end_date && \Carbon\Carbon::parse($period->end_date)->isPast())
                                    <span class="badge bg-danger bg-opacity-10 text-danger px-3 py-1">
                                        <i class="bi bi-lock me-1"></i>Selesai (CLOSED)
                                    </span>
                                @else
                                    <span class="badge bg-secondary bg-opacity-10 text-secondary px-3 py-1">Tutup / Nonaktif</span>
                                @endif
                            </td>
                            <td class="text-end pe-3">
                                <div class="btn-group btn-group-sm">
                                    <a wire:navigate href="{{ route('master.periods.edit', $period) }}" class="btn btn-outline-primary" title="Edit Periode">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form action="{{ route('master.periods.destroy', $period) }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus periode ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-outline-danger" title="Hapus Periode">
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
                                Belum ada data periode penilaian.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if($periods->hasPages())
        <div class="card-footer bg-white py-3">
            {{ $periods->withQueryString()->links() }}
        </div>
    @endif
</div>
@endsection
