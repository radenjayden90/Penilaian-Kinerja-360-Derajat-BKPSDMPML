@extends('layouts.app')

@section('title', 'Form Penilaian Kinerja')
@section('header', 'Form Kuesioner Penilaian 360°')
@section('subtitle', 'Berikan penilaian secara jujur dan objektif untuk meningkatkan kualitas kinerja ASN')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('transaction.assessments.index') }}">Penilaian Saya</a></li>
    <li class="breadcrumb-item active" aria-current="page">Form Evaluasi</li>
@endsection

@section('content')
<!-- Target Employee Summary Header -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body p-4">
        <div class="row align-items-center">
            <div class="col-12 col-md-8">
                <small class="text-uppercase fw-semibold text-primary" style="font-size: 11px;">Target Evaluasi Pegawai</small>
                <h4 class="fw-bold text-dark mb-1">{{ $target->name }}</h4>
                <p class="text-muted mb-0" style="font-size: 14px;">
                    NIP. {{ $target->nip }} | {{ $target->position->name ?? '-' }} | {{ $target->department->name ?? '-' }}
                </p>
            </div>
            <div class="col-12 col-md-4 text-md-end mt-3 mt-md-0">
                <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-2 fs-6" style="color: #1E3A5F !important;">
                    Tipe Evaluasi: {{ $type }}
                </span>
            </div>
        </div>
    </div>
</div>

<form action="{{ route('transaction.assessments.store') }}" method="POST">
    @csrf
    <input type="hidden" name="target_id" value="{{ $target->id }}">
    <input type="hidden" name="type" value="{{ $type }}">

    <!-- Scale Rating Guide Legend -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-light py-2 fw-semibold" style="font-size: 13px;">
            <i class="bi bi-info-circle me-1 text-primary"></i> Panduan Skala Penilaian Likert (1 - 5)
        </div>
        <div class="card-body p-3" style="font-size: 13px;">
            <div class="row text-center g-2">
                <div class="col"><span class="badge bg-danger w-100 py-2">1: Sangat Kurang</span></div>
                <div class="col"><span class="badge bg-warning text-dark w-100 py-2">2: Kurang</span></div>
                <div class="col"><span class="badge bg-secondary w-100 py-2">3: Cukup</span></div>
                <div class="col"><span class="badge bg-info text-white w-100 py-2">4: Baik</span></div>
                <div class="col"><span class="badge bg-success w-100 py-2">5: Sangat Baik</span></div>
            </div>
        </div>
    </div>

    <!-- Questions Grouped by Category -->
    @foreach($categories as $category)
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white py-3 border-bottom">
                <h6 class="fw-bold mb-0 text-primary" style="color: #1E3A5F !important;">
                    <i class="bi bi-folder2-open me-2"></i>{{ $category->name }}
                </h6>
                @if($category->description)
                    <small class="text-muted d-block mt-1">{{ $category->description }}</small>
                @endif
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-3" style="width: 50px;">No</th>
                                <th>Indikator Pertanyaan</th>
                                <th class="text-center" style="width: 320px;">Pilihan Nilai (1 - 5)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($category->indicators as $indIndex => $indicator)
                                <tr>
                                    <td class="ps-3 fw-semibold text-muted">{{ $loop->iteration }}</td>
                                    <td>
                                        <div class="fw-medium text-dark">{{ $indicator->name ?? $indicator->question }}</div>
                                        @if($indicator->description)
                                            <small class="text-muted d-block">{{ $indicator->description }}</small>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="d-flex justify-content-center gap-2">
                                            @for($score = 1; $score <= 5; $score++)
                                                <input type="radio" class="btn-check" name="scores[{{ $indicator->id }}]" id="score_{{ $indicator->id }}_{{ $score }}" value="{{ $score }}" required {{ old("scores.{$indicator->id}") == $score ? 'checked' : '' }}>
                                                <label class="btn btn-outline-primary btn-sm px-3 py-1 fw-semibold" for="score_{{ $indicator->id }}_{{ $score }}">
                                                    {{ $score }}
                                                </label>
                                            @endfor
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endforeach

    <!-- General Notes -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white py-3 fw-semibold">
            <i class="bi bi-chat-left-text me-2 text-primary"></i>Catatan & Saran Konstruktif (Opsional)
        </div>
        <div class="card-body p-4">
            <textarea name="general_notes" rows="4" class="form-control" placeholder="Tuliskan apresiasi, masukan, atau saran perbaikan untuk pengembangan kinerja pegawai ini..."></textarea>
        </div>
    </div>

    <!-- Submit Action -->
    <div class="card border-0 shadow-sm p-3 mb-5 bg-white">
        <div class="d-flex justify-content-between align-items-center">
            <a href="{{ route('transaction.assessments.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i> Batal / Kembali
            </a>
            <button type="submit" class="btn btn-success px-4 py-2 fw-semibold" onclick="return confirm('Apakah Anda yakin jawaban kuesioner ini sudah sesuai? Penilaian yang diserahkan tidak dapat diubah.')">
                <i class="bi bi-send-check me-2"></i>Kirim Evaluasi Penilaian
            </button>
        </div>
    </div>
</form>
@endsection
