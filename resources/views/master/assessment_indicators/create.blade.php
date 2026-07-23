@extends('layouts.app')

@section('title', 'Tambah Pertanyaan Penilaian')
@section('header', 'Tambah Pertanyaan / Indikator Penilaian')
@section('subtitle', 'Form pembuatan butir pertanyaan evaluasi 360 derajat')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('master.index') }}">Master Data</a></li>
    <li class="breadcrumb-item"><a href="{{ route('master.assessment-indicators.index') }}">Pertanyaan</a></li>
    <li class="breadcrumb-item active" aria-current="page">Tambah</li>
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-12 col-md-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3">
                <span class="fw-semibold"><i class="bi bi-question-circle me-2 text-primary"></i>Form Pertanyaan Penilaian</span>
            </div>
            <div class="card-body p-4">
                <form action="{{ route('master.assessment-indicators.store') }}" method="POST">
                    @csrf

                    <div class="mb-3">
                        <label for="category_id" class="form-label fw-semibold">Aspek Kategori Penilaian <span class="text-danger">*</span></label>
                        <select name="category_id" id="category_id" class="form-select @error('category_id') is-invalid @enderror" required>
                            <option value="">-- Pilih Aspek Kategori --</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                            @endforeach
                        </select>
                        @error('category_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-3">
                        <label for="indicator" class="form-label fw-semibold">Butir Pertanyaan / Indikator <span class="text-danger">*</span></label>
                        <textarea name="indicator" id="indicator" rows="3" class="form-control @error('indicator') is-invalid @enderror" placeholder="Contoh: Mampu menyelesaikan tugas pelayanan publik dengan tepat waktu dan responsif..." required>{{ old('indicator') }}</textarea>
                        @error('indicator') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label fw-semibold">Panduan / Deskripsi Penilaian</label>
                        <textarea name="description" id="description" rows="2" class="form-control @error('description') is-invalid @enderror" placeholder="Penjelasan atau kriteria penilaian tambahan...">{{ old('description') }}</textarea>
                        @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-12 col-sm-6">
                            <label for="display_order" class="form-label fw-semibold">Urutan Tampilan</label>
                            <input type="number" name="display_order" id="display_order" class="form-control @error('display_order') is-invalid @enderror" value="{{ old('display_order', 1) }}" placeholder="1">
                            @error('display_order') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <div class="mb-4">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', '1') == '1' ? 'checked' : '' }}>
                            <label class="form-check-label fw-semibold" for="is_active">Aktifkan Pertanyaan Ini</label>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2 border-top pt-3">
                        <a href="{{ route('master.assessment-indicators.index') }}" class="btn btn-outline-secondary">Batal</a>
                        <button type="submit" class="btn btn-primary"><i class="bi bi-save me-1"></i> Simpan Pertanyaan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
