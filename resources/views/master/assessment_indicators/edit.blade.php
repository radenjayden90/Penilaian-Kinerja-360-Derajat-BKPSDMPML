@extends('layouts.app')

@section('title', 'Edit Pertanyaan Penilaian')
@section('header', 'Edit Pertanyaan / Indikator Penilaian')
@section('subtitle', 'Pembaruan isi dan bobot butir pertanyaan evaluasi')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('master.index') }}">Master Data</a></li>
    <li class="breadcrumb-item"><a href="{{ route('master.assessment-indicators.index') }}">Pertanyaan</a></li>
    <li class="breadcrumb-item active" aria-current="page">Edit</li>
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-12 col-md-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3">
                <span class="fw-semibold"><i class="bi bi-pencil-square me-2 text-primary"></i>Edit Pertanyaan</span>
            </div>
            <div class="card-body p-4">
                <form action="{{ route('master.assessment-indicators.update', $assessment_indicator) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label for="category_id" class="form-label fw-semibold">Aspek Kategori Penilaian <span class="text-danger">*</span></label>
                        <select name="category_id" id="category_id" class="form-select @error('category_id') is-invalid @enderror" required>
                            <option value="">-- Pilih Aspek Kategori --</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}" {{ old('category_id', $assessment_indicator->category_id) == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                            @endforeach
                        </select>
                        @error('category_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-3">
                        <label for="name" class="form-label fw-semibold">Butir Pertanyaan / Indikator <span class="text-danger">*</span></label>
                        <textarea name="name" id="name" rows="3" class="form-control @error('name') is-invalid @enderror" required>{{ old('name', $assessment_indicator->name ?? $assessment_indicator->question) }}</textarea>
                        @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label fw-semibold">Panduan / Deskripsi Penilaian</label>
                        <textarea name="description" id="description" rows="2" class="form-control @error('description') is-invalid @enderror">{{ old('description', $assessment_indicator->description) }}</textarea>
                        @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-12 col-sm-6">
                            <label for="weight" class="form-label fw-semibold">Bobot (%)</label>
                            <input type="number" step="0.1" name="weight" id="weight" class="form-control @error('weight') is-invalid @enderror" value="{{ old('weight', $assessment_indicator->weight) }}">
                            @error('weight') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-12 col-sm-6">
                            <label for="display_order" class="form-label fw-semibold">Urutan Tampilan</label>
                            <input type="number" name="display_order" id="display_order" class="form-control @error('display_order') is-invalid @enderror" value="{{ old('display_order', $assessment_indicator->display_order) }}">
                            @error('display_order') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <div class="mb-4">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', $assessment_indicator->is_active) ? 'checked' : '' }}>
                            <label class="form-check-label fw-semibold" for="is_active">Aktifkan Pertanyaan Ini</label>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2 border-top pt-3">
                        <a href="{{ route('master.assessment-indicators.index') }}" class="btn btn-outline-secondary">Batal</a>
                        <button type="submit" class="btn btn-primary"><i class="bi bi-save me-1"></i> Perbarui Pertanyaan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
