@extends('layouts.app')

@section('title', 'Perhitungan Nilai 360°')
@section('header', 'Kalkulasi Skor Kinerja 360° ASN')
@section('subtitle', 'Proses kalkulasi nilai agregat berdasarkan pembobotan (Atasan, Sejawat, Bawahan, Diri Sendiri)')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active" aria-current="page">Perhitungan Nilai</li>
@endsection

@section('action_buttons')
    <form action="{{ route('transaction.calculations.calculateAll') }}" method="POST" class="d-inline" onsubmit="return confirm('Proses ini akan menghitung ulang skor akhir seluruh pegawai pada periode aktif. Lanjutkan?')">
        @csrf
        <button type="submit" class="btn btn-success fw-semibold">
            <i class="bi bi-calculator me-1"></i> Hitung Ulang Seluruh Pegawai
        </button>
    </form>
@endsection

@section('content')
<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white py-3">
        <form method="GET" action="{{ route('transaction.calculations.index') }}" class="row g-2">
            <div class="col-12 col-md-6">
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0"><i class="bi bi-search text-muted"></i></span>
                    <input type="text" name="search" class="form-control border-start-0 bg-light" placeholder="Cari NIP atau nama pegawai..." value="{{ request('search') }}">
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
                    <a href="{{ route('transaction.calculations.index') }}" class="btn btn-outline-secondary w-100">
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
                        <th>Pegawai ASN</th>
                        <th>Jabatan & Unit Kerja</th>
                        <th class="text-center">Skor Akhir 360°</th>
                        <th class="text-center">Kategori Nilai</th>
                        <th class="text-end pe-3" style="width: 170px;">Aksi Kalkulasi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($employees as $index => $emp)
                        @php
                            $res = $emp->assessmentResult;
                        @endphp
                        <tr>
                            <td class="ps-3 fw-semibold text-muted">{{ $employees->firstItem() + $index }}</td>
                            <td>
                                <div class="fw-semibold text-dark">{{ $emp->name }}</div>
                                <small class="text-muted">NIP. {{ $emp->nip }}</small>
                            </td>
                            <td>
                                <div class="fw-medium text-dark">{{ $emp->position->name ?? '-' }}</div>
                                <small class="text-muted">{{ $emp->department->name ?? '-' }}</small>
                            </td>
                            <td class="text-center">
                                @if($res && $res->final_score !== null)
                                    <span class="fw-bold fs-5 text-primary" style="color: #1E3A5F !important;">
                                        {{ number_format($res->final_score, 2) }}
                                    </span>
                                @else
                                    <span class="text-muted small">-</span>
                                @endif
                            </td>
                            <td class="text-center">
                                @if($res && $res->category)
                                    @php
                                        $catVal = is_object($res->category) ? $res->category->value : $res->category;
                                        $badgeColor = match($catVal) {
                                            'SANGAT_BAIK' => 'bg-success',
                                            'BAIK' => 'bg-primary',
                                            'CUKUP' => 'bg-warning text-dark',
                                            'KURANG' => 'bg-danger',
                                            default => 'bg-secondary'
                                        };
                                    @endphp
                                    <span class="badge {{ $badgeColor }} px-3 py-1.5 fs-6">
                                        {{ str_replace('_', ' ', $catVal) }}
                                    </span>
                                @else
                                    <span class="badge bg-light text-muted border">Belum Dihitung</span>
                                @endif
                            </td>
                            <td class="text-end pe-3">
                                <div class="btn-group btn-group-sm">
                                    <form action="{{ route('transaction.calculations.calculate', $emp) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-outline-primary" title="Hitung Skor Pegawai Ini">
                                            <i class="bi bi-calculator me-1"></i>Hitung
                                        </button>
                                    </form>
                                    @if($res)
                                        <a href="{{ route('transaction.calculations.show', $emp) }}" class="btn btn-outline-info" title="Lihat Rincian Rapor">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">
                                <i class="bi bi-inbox fs-3 d-block mb-2 text-secondary"></i>
                                Belum ada data perhitungan nilai pegawai.
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
