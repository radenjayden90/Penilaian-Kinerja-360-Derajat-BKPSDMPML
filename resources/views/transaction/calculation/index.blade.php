@extends('layouts.app')

@section('title', 'Perhitungan Nilai 360°')
@section('header', 'Kalkulasi Skor Kinerja 360° ASN')
@section('subtitle', 'Proses kalkulasi nilai agregat berdasarkan pembobotan (Atasan, Sejawat, Bawahan)')

@section('breadcrumb')
    <li class="breadcrumb-item active" aria-current="page">Perhitungan Nilai</li>
@endsection

@section('action_buttons')
    <!-- Automatic Calculation -->
@endsection

@section('content')
<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white py-3">
        <form method="GET" action="{{ route('transaction.calculations.index') }}" class="row g-2" x-data @submit.prevent="Livewire.navigate($el.action + '?' + new URLSearchParams(new FormData($el)).toString())">
            <div class="col-12 col-md-6">
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0"><i class="bi bi-search text-muted"></i></span>
                    <input type="text" name="search" class="form-control border-start-0 bg-light" placeholder="Cari NIP atau nama pegawai..." value="{{ request('search') }}" style="text-overflow: ellipsis; overflow: hidden; white-space: nowrap; padding-right: 1.5rem;">
                </div>
            </div>
            <div class="col-12 col-md-6 d-flex gap-2">
                <select name="department_id" class="form-select bg-light" onchange="this.form.requestSubmit()" style="text-overflow: ellipsis; overflow: hidden; white-space: nowrap; padding-right: 2.5rem;">
                    <option value="">-- Semua Unit Kerja --</option>
                    @foreach($departments as $dept)
                        <option value="{{ $dept->id }}" {{ request('department_id') == $dept->id ? 'selected' : '' }}>{{ $dept->name }}</option>
                    @endforeach
                </select>
                <a href="{{ route('transaction.calculations.index') }}" class="btn btn-outline-secondary d-flex align-items-center justify-content-center" title="Reset Filter" wire:navigate>
                    <i class="bi bi-x-circle"></i>
                </a>
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
                        <th class="text-end pe-3" style="width: 200px;">Aksi</th>
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
                                    <span class="fw-semibold text-dark">
                                        {{ number_format($res->final_score, 2) }}
                                    </span>
                                @else
                                    <span class="text-muted small">-</span>
                                @endif
                            </td>
                            <td class="text-center">
                                @if($res && $res->category)
                                    @php
                                        $catVal = is_object($res->category) ? $res->category->value : (string)$res->category;
                                        $catEnum = \App\Enums\ResultCategory::tryFrom($catVal) ?? \App\Enums\ResultCategory::tryFrom(strtoupper(str_replace(' ', '_', $catVal)));
                                        $catLabel = \App\Enums\ResultCategory::formatLabel($res->category);
                                        $textColor = match($catEnum) {
                                            \App\Enums\ResultCategory::VERY_GOOD => 'text-success',
                                            \App\Enums\ResultCategory::GOOD => 'text-primary',
                                            \App\Enums\ResultCategory::FAIR => 'text-warning',
                                            \App\Enums\ResultCategory::NEEDS_IMPROVEMENT => 'text-danger',
                                            default => 'text-secondary'
                                        };
                                        $style = $catEnum === \App\Enums\ResultCategory::FAIR ? 'style="color: #b58900 !important;"' : '';
                                    @endphp
                                    <span class="fw-semibold {{ $textColor }}" {!! $style !!}>
                                        {{ $catLabel }}
                                    </span>
                                @else
                                    <span class="text-muted small">Belum Dihitung</span>
                                @endif
                            </td>
                            <td class="text-end pe-3">
                                <div class="btn-group btn-group-sm">
                                    @if($res)
                                        <a wire:navigate href="{{ route('transaction.calculations.show', $emp) }}" class="btn btn-outline-info" title="Lihat Detail Penilaian">
                                            <i class="bi bi-eye me-1"></i> Lihat Detail Penilaian
                                        </a>
                                    @else
                                        <button type="button" class="btn btn-outline-secondary" disabled>
                                            <i class="bi bi-eye-slash me-1"></i> Belum Ada Data
                                        </button>
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
            {{ $employees->withQueryString()->links() }}
        </div>
    @endif
</div>
@endsection

<!-- Custom Premium Modal -->
<div class="modal fade" id="confirmKalkulasiModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 420px;">
        <div class="modal-content custom-modal-content">
            <div class="modal-body p-4 text-center">
                <div class="icon-box mb-4 mx-auto">
                    <i class="bi bi-exclamation-triangle-fill"></i>
                </div>
                <h4 class="fw-bold mb-2" style="color: #1e3a5f;">Kalkulasi Ulang?</h4>
                <p class="text-muted mb-4" style="font-size: 0.95rem;">
                    Proses ini akan menghitung ulang skor akhir seluruh pegawai secara masal. Tindakan ini membutuhkan waktu beberapa saat.
                </p>
                
                <form action="{{ route('transaction.calculations.calculateAll') }}" method="POST" id="kalkulasiForm">
                    @csrf
                    <div class="d-flex gap-2 justify-content-center">
                        <button type="button" class="btn btn-light px-4 rounded-pill fw-medium btn-hover-scale border" data-bs-dismiss="modal">
                            Batal
                        </button>
                        <button type="submit" class="btn btn-primary px-4 rounded-pill fw-medium btn-gradient shadow-sm btn-hover-scale" onclick="showLoadingState(this)">
                            <i class="bi bi-calculator me-1"></i> Ya, Hitung!
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .custom-modal-content {
        border-radius: 24px;
        border: none;
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
        background: rgba(255, 255, 255, 0.98);
    }
    .icon-box {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        background: linear-gradient(135deg, #fff3cd 0%, #ffe69c 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 10px 20px rgba(255, 193, 7, 0.2);
        animation: pulse-warning 2s infinite;
    }
    .icon-box i {
        font-size: 2.5rem;
        color: #ffc107;
    }
    .btn-gradient {
        background: linear-gradient(135deg, #198754 0%, #146c43 100%);
        border: none;
        color: white;
    }
    .btn-gradient:hover {
        background: linear-gradient(135deg, #146c43 0%, #0f5132 100%);
        color: white;
    }
    .btn-hover-scale {
        transition: transform 0.2s cubic-bezier(0.34, 1.56, 0.64, 1);
    }
    .btn-hover-scale:hover {
        transform: scale(1.05);
    }
    @keyframes pulse-warning {
        0% { box-shadow: 0 0 0 0 rgba(255, 193, 7, 0.4); }
        70% { box-shadow: 0 0 0 15px rgba(255, 193, 7, 0); }
        100% { box-shadow: 0 0 0 0 rgba(255, 193, 7, 0); }
    }
    /* Custom Backdrop for Glassmorphism effect */
    .modal-backdrop.show {
        backdrop-filter: blur(5px);
        background-color: rgba(15, 23, 42, 0.6) !important;
        opacity: 1 !important;
    }
</style>
@endpush

@push('scripts')
<script>
    function showLoadingState(btn) {
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span> Menghitung...';
        btn.classList.add('disabled');
        
        const cancelBtn = btn.previousElementSibling;
        if (cancelBtn) {
            cancelBtn.classList.add('disabled');
        }
    }
</script>
@endpush
