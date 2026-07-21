@extends('layouts.app')

@section('title', 'Riwayat Hasil Penilaian Saya')
@section('header', 'Riwayat Hasil Penilaian Saya')
@section('subtitle', 'Histori rapor evaluasi kinerja 360° milik Anda dari seluruh periode penilaian.')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active" aria-current="page">Riwayat Penilaian Saya</li>
@endsection

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <!-- Filter Card -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body py-3">
                <form method="GET" action="{{ route('assessment.index') }}" class="row g-2 align-items-center">
                    <div class="col-12 col-md-8">
                        <select name="period_id" class="form-select bg-light" onchange="this.form.submit()">
                            <option value="">-- Semua Periode Penilaian --</option>
                            @foreach($periods as $p)
                                <option value="{{ $p->id }}" {{ request('period_id') == $p->id ? 'selected' : '' }}>
                                    {{ $p->name }} ({{ $p->year }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-12 col-md-4">
                        @if(request('period_id'))
                            <a href="{{ route('assessment.index') }}" class="btn btn-outline-secondary w-100">
                                <i class="bi bi-x-circle me-1"></i> Reset Filter
                            </a>
                        @endif
                    </div>
                </form>
            </div>
        </div>

        <!-- Content Card -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3">
                <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-award me-2 text-primary"></i>Histori Rapor Evaluasi Kinerja 360° Murni Pegawai</h6>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover table-striped align-middle mb-0">
                        <thead>
                            <tr>
                                <th class="ps-3" style="width: 50px;">No</th>
                                <th>Periode Penilaian</th>
                                <th class="text-center">Skor Atasan (50%)</th>
                                <th class="text-center">Skor Sejawat</th>
                                <th class="text-center">Skor Bawahan</th>
                                <th class="text-center">Skor Akhir 360°</th>
                                <th class="text-center">Predikat Kategori</th>
                                <th class="text-end pe-3" style="width: 140px;">Aksi Rapor</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($myResults as $index => $res)
                                <tr>
                                    <td class="ps-3 fw-semibold text-muted">{{ $myResults->firstItem() + $index }}</td>
                                    <td>
                                        <div class="fw-semibold text-dark">{{ $res->period->name ?? '-' }}</div>
                                        <small class="text-muted">Tahun {{ $res->period->year ?? '-' }}</small>
                                    </td>
                                    <td class="text-center font-monospace fs-6">
                                        {{ number_format($res->superior_average ?? 0, 2) }}
                                    </td>
                                    <td class="text-center font-monospace fs-6">
                                        {{ number_format($res->peer_average ?? 0, 2) }}
                                    </td>
                                    <td class="text-center font-monospace fs-6">
                                        {{ number_format($res->subordinate_average ?? 0, 2) }}
                                    </td>
                                    <td class="text-center">
                                        @if($res->final_score !== null)
                                            <span class="fw-bold fs-5 text-primary" style="color: #1E3A5F !important;">
                                                {{ number_format($res->final_score, 2) }}
                                            </span>
                                        @else
                                            <span class="text-muted small">-</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if($res->category)
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
                                            <span class="badge bg-light text-muted border">Proses Penilaian</span>
                                        @endif
                                    </td>
                                    <td class="text-end pe-3">
                                        <a href="{{ route('transaction.calculations.show', $employee) }}" class="btn btn-sm btn-outline-info" title="Lihat Rapor Lengkap">
                                            <i class="bi bi-file-earmark-bar-graph me-1"></i>Rapor
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center text-muted py-4">
                                        <i class="bi bi-inbox fs-3 d-block mb-2 text-secondary"></i>
                                        Belum ada hasil evaluasi penilaian 360° untuk akun Anda.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($myResults->hasPages())
                    <div class="card-footer bg-white py-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <small class="text-muted">
                                Menampilkan {{ $myResults->firstItem() }} - {{ $myResults->lastItem() }} dari total {{ $myResults->total() }} data
                            </small>
                            {{ $myResults->withQueryString()->links() }}
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
