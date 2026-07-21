@extends('layouts.app')

@section('title', 'Rincian Penilaian')
@section('header', 'Rincian Lembar Evaluasi Penilaian 360°')
@section('subtitle', 'Hasil masukan isian kuesioner yang telah diserahkan')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('transaction.assessments.index') }}">Penilaian Saya</a></li>
    <li class="breadcrumb-item active" aria-current="page">Rincian Evaluasi</li>
@endsection

@section('content')
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body p-4">
        <div class="row align-items-center">
            <div class="col-12 col-md-8">
                <small class="text-uppercase fw-semibold text-muted" style="font-size: 11px;">Target Pegawai Evaluasi</small>
                <h4 class="fw-bold text-dark mb-1">{{ $assessment->employee->name ?? '-' }}</h4>
                <p class="text-muted mb-0">NIP. {{ $assessment->employee->nip ?? '-' }}</p>
            </div>
            <div class="col-12 col-md-4 text-md-end mt-3 mt-md-0">
                <span class="badge bg-success px-3 py-2 fs-6">
                    <i class="bi bi-check-circle me-1"></i>Selesai Diserahkan
                </span>
                <div class="small text-muted mt-1">
                    Tanggal: {{ $assessment->submitted_at ? $assessment->submitted_at->format('d M Y H:i') : '-' }}
                </div>
            </div>
        </div>
    </div>
</div>

@foreach($groupedScores as $categoryName => $scores)
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white py-3 fw-semibold text-primary">
            <i class="bi bi-folder2-open me-2"></i>{{ $categoryName }}
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-3" style="width: 50px;">No</th>
                            <th>Indikator Pertanyaan</th>
                            <th class="text-center" style="width: 120px;">Skor Nilai</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($scores as $score)
                            <tr>
                                <td class="ps-3 fw-semibold text-muted">{{ $loop->iteration }}</td>
                                <td>{{ $score->indicator->name ?? $score->indicator->question }}</td>
                                <td class="text-center">
                                    <span class="badge bg-primary px-3 py-2 fs-6" style="background-color: #1E3A5F !important;">
                                        {{ $score->score }} / 10
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endforeach

@if($assessment->notes)
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white py-3 fw-semibold">
            <i class="bi bi-chat-left-text me-2 text-primary"></i>Catatan Tambahan
        </div>
        <div class="card-body p-4 bg-light">
            <p class="mb-0 text-dark italic">"{{ $assessment->notes }}"</p>
        </div>
    </div>
@endif

<div class="d-flex justify-content-start mb-4">
    <a href="{{ route('transaction.assessments.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i> Kembali ke Daftar Penilaian
    </a>
</div>
@endsection
