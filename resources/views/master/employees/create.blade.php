@extends('layouts.app')

@section('title', 'Tambah Pegawai Baru')
@section('header', 'Pendaftaran Pegawai ASN Baru')
@section('subtitle', 'Form isian data identitas, jabatan, dan akun login pegawai')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('master.index') }}">Master Data</a></li>
    <li class="breadcrumb-item"><a href="{{ route('master.employees.index') }}">Pegawai</a></li>
    <li class="breadcrumb-item active" aria-current="page">Tambah</li>
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-12 col-lg-10">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3">
                <span class="fw-semibold"><i class="bi bi-person-plus me-2 text-primary"></i>Form Registrasi Pegawai</span>
            </div>
            <div class="card-body p-4">
                <form action="{{ route('master.employees.store') }}" method="POST">
                    @csrf

                    <h6 class="fw-bold text-primary mb-3"><i class="bi bi-card-heading me-2"></i>Identitas Pegawai</h6>
                    <div class="row g-3 mb-4">
                        <div class="col-12 col-md-6">
                            <label for="nip" class="form-label fw-semibold">NIP <span class="text-danger">*</span></label>
                            <input type="text" name="nip" id="nip" class="form-control @error('nip') is-invalid @enderror" value="{{ old('nip') }}" placeholder="Contoh: 198501012010011001" required>
                            @error('nip') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-12 col-md-6">
                            <label for="name" class="form-label fw-semibold">Nama Lengkap & Gelar <span class="text-danger">*</span></label>
                            <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" placeholder="Contoh: Dr. Budi Santoso, S.STP, M.Si" required>
                            @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-12 col-md-6">
                            <label for="email" class="form-label fw-semibold">Email Official <span class="text-danger">*</span></label>
                            <input type="email" name="email" id="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" placeholder="pegawai@pemalang.go.id" required>
                            @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-12 col-md-6">
                            <label for="password" class="form-label fw-semibold">Password Akun <span class="text-danger">*</span></label>
                            <input type="password" name="password" id="password" class="form-control @error('password') is-invalid @enderror" placeholder="Minimal 8 karakter" required>
                            @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-12 col-md-6">
                            <label for="phone" class="form-label fw-semibold">No. WhatsApp / Telepon</label>
                            <input type="text" name="phone" id="phone" class="form-control @error('phone') is-invalid @enderror" value="{{ old('phone') }}" placeholder="081234567890">
                            @error('phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-12 col-md-6">
                            <label for="gender" class="form-label fw-semibold">Jenis Kelamin</label>
                            <select name="gender" id="gender" class="form-select @error('gender') is-invalid @enderror">
                                <option value="">-- Pilih Jenis Kelamin --</option>
                                <option value="L" {{ old('gender') === 'L' ? 'selected' : '' }}>Laki-laki</option>
                                <option value="P" {{ old('gender') === 'P' ? 'selected' : '' }}>Perempuan</option>
                            </select>
                            @error('gender') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <h6 class="fw-bold text-primary mb-3"><i class="bi bi-building me-2"></i>Jabatan & Struktur Organisasi</h6>
                    <div class="row g-3 mb-4">
                        <div class="col-12 col-md-6">
                            <label for="department_id" class="form-label fw-semibold">Unit Kerja / Bidang <span class="text-danger">*</span></label>
                            <select name="department_id" id="department_id" class="form-select @error('department_id') is-invalid @enderror" required>
                                <option value="">-- Pilih Unit Kerja --</option>
                                @foreach($departments as $dept)
                                    <option value="{{ $dept->id }}" {{ old('department_id') == $dept->id ? 'selected' : '' }}>{{ $dept->name }}</option>
                                @endforeach
                            </select>
                            @error('department_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-12 col-md-6">
                            <label for="position_id" class="form-label fw-semibold">Jabatan <span class="text-danger">*</span></label>
                            <select name="position_id" id="position_id" class="form-select @error('position_id') is-invalid @enderror" required>
                                <option value="">-- Pilih Jabatan --</option>
                                @foreach($positions as $pos)
                                    <option value="{{ $pos->id }}" {{ old('position_id') == $pos->id ? 'selected' : '' }}>{{ $pos->name }}</option>
                                @endforeach
                            </select>
                            @error('position_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-12 col-md-6">
                            <label for="supervisor_id" class="form-label fw-semibold">Atasan Langsung</label>
                            <select name="supervisor_id" id="supervisor_id" class="form-select @error('supervisor_id') is-invalid @enderror">
                                <option value="">-- Tanpa Atasan (Kepala Instansi) --</option>
                                @foreach($supervisors as $sup)
                                    <option value="{{ $sup->id }}" {{ old('supervisor_id') == $sup->id ? 'selected' : '' }}>{{ $sup->name }} (NIP. {{ $sup->nip }})</option>
                                @endforeach
                            </select>
                            @error('supervisor_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-12 col-md-6">
                            <label for="role_id" class="form-label fw-semibold">Role Akses Sistem <span class="text-danger">*</span></label>
                            <select name="role_id" id="role_id" class="form-select @error('role_id') is-invalid @enderror" required>
                                <option value="">-- Pilih Role --</option>
                                @foreach($roles as $role)
                                    <option value="{{ $role->id }}" {{ old('role_id') == $role->id ? 'selected' : '' }}>{{ $role->name }} - {{ $role->description }}</option>
                                @endforeach
                            </select>
                            @error('role_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-12">
                            <div class="form-check form-switch mt-2">
                                <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', '1') == '1' ? 'checked' : '' }}>
                                <label class="form-check-label fw-semibold" for="is_active">Status Akun Pegawai Aktif</label>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2 border-top pt-3">
                        <a href="{{ route('master.employees.index') }}" class="btn btn-outline-secondary">Batal</a>
                        <button type="submit" class="btn btn-primary"><i class="bi bi-save me-1"></i> Simpan Pegawai</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
