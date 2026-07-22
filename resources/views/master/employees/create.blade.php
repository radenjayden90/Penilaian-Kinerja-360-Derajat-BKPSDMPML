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
    <div class="col-12 col-xl-10">

        <!-- Top Header Banner -->
        <div class="card border-0 rounded-4 shadow-sm mb-4 overflow-hidden" style="background: linear-gradient(135deg, #0F172A 0%, #1E3A5F 100%);">
            <div class="card-body p-4 text-white">
                <div class="row align-items-center g-3">
                    <div class="col-12 col-md-8">
                        <div class="d-flex align-items-center gap-3">
                            <div class="rounded-circle bg-white bg-opacity-15 text-white d-flex align-items-center justify-content-center fw-bold border border-white border-opacity-20 flex-shrink-0" style="width: 52px; height: 52px; font-size: 22px; backdrop-filter: blur(10px);">
                                <i class="bi bi-person-plus-fill text-warning"></i>
                            </div>
                            <div>
                                <span class="badge bg-white bg-opacity-15 text-white border border-white border-opacity-20 rounded-pill px-3 py-1 mb-1" style="font-size: 11px; letter-spacing: 0.5px;">
                                    REGISTRASI PEGAWAI ASN
                                </span>
                                <h3 class="fw-bold text-white mb-0" style="font-size: 20px;">Pendaftaran Pegawai ASN Baru</h3>
                                <p class="text-white text-opacity-75 small mb-0 mt-0.5">Isi seluruh data identitas, unit kerja, jabatan, dan atasan langsung pegawai</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-md-4 text-md-end">
                        <a href="{{ route('master.employees.index') }}" class="btn btn-light text-slate-700 fw-semibold px-3 py-2 rounded-3 shadow-sm" style="font-size: 13px;">
                            <i class="bi bi-arrow-left me-1"></i> Kembali ke Daftar
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Form Card -->
        <form action="{{ route('master.employees.store') }}" method="POST">
            @csrf

            <!-- Section 1: Identitas Pegawai -->
            <div class="card border-0 rounded-4 shadow-sm mb-4">
                <div class="card-header bg-white py-3.5 px-4 border-bottom d-flex align-items-center gap-2">
                    <div class="rounded-3 p-2 d-flex align-items-center justify-content-center" style="background: #EFF6FF; color: #2563EB; width: 34px; height: 34px;">
                        <i class="bi bi-person-vcard-fill fs-6"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold text-dark mb-0" style="font-size: 15px;">Identitas Pegawai ASN</h6>
                        <small class="text-slate-500" style="font-size: 12px;">Informasi dasar NIP, Nama, Email, dan kontak pegawai</small>
                    </div>
                </div>
                <div class="card-body p-4">
                    <div class="row g-3">
                        <!-- NIP -->
                        <div class="col-12 col-md-6">
                            <label for="nip" class="form-label fw-semibold text-slate-700" style="font-size: 13px;">NIP (Nomor Induk Pegawai) <span class="text-rose-500">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-slate-50 border-slate-200 text-slate-400"><i class="bi bi-card-text"></i></span>
                                <input type="text" name="nip" id="nip" class="form-control border-slate-200 bg-white @error('nip') is-invalid @enderror" value="{{ old('nip') }}" required placeholder="Contoh: 198502142010011004">
                            </div>
                            @error('nip') <div class="text-rose-600 small mt-1">{{ $message }}</div> @enderror
                        </div>

                        <!-- Nama Lengkap -->
                        <div class="col-12 col-md-6">
                            <label for="name" class="form-label fw-semibold text-slate-700" style="font-size: 13px;">Nama Lengkap & Gelar <span class="text-rose-500">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-slate-50 border-slate-200 text-slate-400"><i class="bi bi-person-fill"></i></span>
                                <input type="text" name="name" id="name" class="form-control border-slate-200 bg-white @error('name') is-invalid @enderror" value="{{ old('name') }}" required placeholder="Contoh: Dr. Budi Santoso, S.STP, M.Si">
                            </div>
                            @error('name') <div class="text-rose-600 small mt-1">{{ $message }}</div> @enderror
                        </div>

                        <!-- Email Official -->
                        <div class="col-12 col-md-6">
                            <label for="email" class="form-label fw-semibold text-slate-700" style="font-size: 13px;">Email Official <span class="text-rose-500">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-slate-50 border-slate-200 text-slate-400"><i class="bi bi-envelope-fill"></i></span>
                                <input type="email" name="email" id="email" class="form-control border-slate-200 bg-white @error('email') is-invalid @enderror" value="{{ old('email') }}" required placeholder="pegawai@pemalang.go.id">
                            </div>
                            @error('email') <div class="text-rose-600 small mt-1">{{ $message }}</div> @enderror
                        </div>

                        <!-- Password Akun -->
                        <div class="col-12 col-md-6">
                            <label for="password" class="form-label fw-semibold text-slate-700" style="font-size: 13px;">Password Akun <span class="text-rose-500">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-slate-50 border-slate-200 text-slate-400"><i class="bi bi-key-fill"></i></span>
                                <input type="password" name="password" id="password" class="form-control border-slate-200 bg-white @error('password') is-invalid @enderror" placeholder="Minimal 8 karakter" required>
                            </div>
                            @error('password') <div class="text-rose-600 small mt-1">{{ $message }}</div> @enderror
                        </div>

                        <!-- WhatsApp / Telepon -->
                        <div class="col-12 col-md-6">
                            <label for="phone" class="form-label fw-semibold text-slate-700" style="font-size: 13px;">No. WhatsApp / Telepon</label>
                            <div class="input-group">
                                <span class="input-group-text bg-slate-50 border-slate-200 text-slate-400"><i class="bi bi-whatsapp"></i></span>
                                <input type="text" name="phone" id="phone" class="form-control border-slate-200 bg-white @error('phone') is-invalid @enderror" value="{{ old('phone') }}" placeholder="Contoh: 081234567890">
                            </div>
                            @error('phone') <div class="text-rose-600 small mt-1">{{ $message }}</div> @enderror
                        </div>

                        <!-- Jenis Kelamin -->
                        <div class="col-12 col-md-6">
                            <label for="gender" class="form-label fw-semibold text-slate-700" style="font-size: 13px;">Jenis Kelamin</label>
                            <div class="input-group">
                                <span class="input-group-text bg-slate-50 border-slate-200 text-slate-400"><i class="bi bi-gender-ambiguous"></i></span>
                                <select name="gender" id="gender" class="form-select border-slate-200 bg-white @error('gender') is-invalid @enderror">
                                    <option value="">-- Pilih Jenis Kelamin --</option>
                                    <option value="L" {{ old('gender') === 'L' ? 'selected' : '' }}>Laki-laki</option>
                                    <option value="P" {{ old('gender') === 'P' ? 'selected' : '' }}>Perempuan</option>
                                </select>
                            </div>
                            @error('gender') <div class="text-rose-600 small mt-1">{{ $message }}</div> @enderror
                        </div>
                    </div>
                </div>
            </div>

            <!-- Section 2: Jabatan & Struktur Organisasi -->
            <div class="card border-0 rounded-4 shadow-sm mb-4">
                <div class="card-header bg-white py-3.5 px-4 border-bottom d-flex align-items-center gap-2">
                    <div class="rounded-3 p-2 d-flex align-items-center justify-content-center" style="background: #F3E8FF; color: #8B5CF6; width: 34px; height: 34px;">
                        <i class="bi bi-diagram-3-fill fs-6"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold text-dark mb-0" style="font-size: 15px;">Jabatan & Struktur Organisasi</h6>
                        <small class="text-slate-500" style="font-size: 12px;">Penempatan unit kerja, jenjang jabatan, atasan langsung, dan role akun</small>
                    </div>
                </div>
                <div class="card-body p-4">
                    <div class="row g-3">
                        <!-- Unit Kerja / Bidang -->
                        <div class="col-12 col-md-6">
                            <label for="department_id" class="form-label fw-semibold text-slate-700" style="font-size: 13px;">Unit Kerja / Bidang <span class="text-rose-500">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-slate-50 border-slate-200 text-slate-400"><i class="bi bi-building"></i></span>
                                <select name="department_id" id="department_id" class="form-select border-slate-200 bg-white @error('department_id') is-invalid @enderror" required>
                                    <option value="">-- Pilih Unit Kerja --</option>
                                    @foreach($departments as $dept)
                                        <option value="{{ $dept->id }}" {{ old('department_id') == $dept->id ? 'selected' : '' }}>{{ $dept->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            @error('department_id') <div class="text-rose-600 small mt-1">{{ $message }}</div> @enderror
                        </div>

                        <!-- Jabatan -->
                        <div class="col-12 col-md-6">
                            <label for="position_id" class="form-label fw-semibold text-slate-700" style="font-size: 13px;">Jabatan <span class="text-rose-500">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-slate-50 border-slate-200 text-slate-400"><i class="bi bi-person-badge"></i></span>
                                <select name="position_id" id="position_id" class="form-select border-slate-200 bg-white @error('position_id') is-invalid @enderror" required>
                                    <option value="">-- Pilih Jabatan --</option>
                                    @foreach($positions as $pos)
                                        <option value="{{ $pos->id }}" {{ old('position_id') == $pos->id ? 'selected' : '' }}>{{ $pos->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            @error('position_id') <div class="text-rose-600 small mt-1">{{ $message }}</div> @enderror
                        </div>

                        <!-- Atasan Langsung -->
                        <div class="col-12 col-md-6">
                            <label for="supervisor_id" class="form-label fw-semibold text-slate-700" style="font-size: 13px;">Atasan Langsung</label>
                            <div class="input-group">
                                <span class="input-group-text bg-slate-50 border-slate-200 text-slate-400"><i class="bi bi-person-up"></i></span>
                                <select name="supervisor_id" id="supervisor_id" class="form-select border-slate-200 bg-white @error('supervisor_id') is-invalid @enderror">
                                    <option value="">-- Tanpa Atasan (Kepala Instansi) --</option>
                                    @foreach($supervisors as $sup)
                                        <option value="{{ $sup->id }}" {{ old('supervisor_id') == $sup->id ? 'selected' : '' }}>{{ $sup->name }} (NIP. {{ $sup->nip }})</option>
                                    @endforeach
                                </select>
                            </div>
                            @error('supervisor_id') <div class="text-rose-600 small mt-1">{{ $message }}</div> @enderror
                        </div>

                        <!-- Role Akses Sistem -->
                        <div class="col-12 col-md-6">
                            <label for="role_id" class="form-label fw-semibold text-slate-700" style="font-size: 13px;">Role Akses Sistem <span class="text-rose-500">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-slate-50 border-slate-200 text-slate-400"><i class="bi bi-shield-check"></i></span>
                                <select name="role_id" id="role_id" class="form-select border-slate-200 bg-white @error('role_id') is-invalid @enderror" required>
                                    <option value="">-- Pilih Role --</option>
                                    @foreach($roles as $role)
                                        <option value="{{ $role->id }}" {{ old('role_id') == $role->id ? 'selected' : '' }}>{{ $role->name }} - {{ $role->description }}</option>
                                    @endforeach
                                </select>
                            </div>
                            @error('role_id') <div class="text-rose-600 small mt-1">{{ $message }}</div> @enderror
                        </div>

                        <!-- Status Aktif Checkbox Toggle -->
                        <div class="col-12 mt-4">
                            <div class="p-3.5 rounded-3 bg-slate-50 border border-slate-200 d-flex align-items-center justify-content-between">
                                <div>
                                    <label class="form-check-label fw-bold text-dark mb-0" for="is_active" style="cursor: pointer; font-size: 14px;">
                                        Status Akun Pegawai Aktif
                                    </label>
                                    <div class="text-slate-500" style="font-size: 12px;">Aktifkan untuk memberikan akses pengisian kuesioner dan partisipasi penilaian 360°</div>
                                </div>
                                <div class="form-check form-switch fs-4 mb-0">
                                    <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', '1') == '1' ? 'checked' : '' }} style="cursor: pointer;">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Form Action Buttons Footer -->
                <div class="card-footer bg-white py-3.5 px-4 border-top d-flex justify-content-end gap-2">
                    <a href="{{ route('master.employees.index') }}" class="btn btn-light text-slate-700 fw-semibold px-4 py-2 rounded-3 border border-slate-200">
                        Batal
                    </a>
                    <button type="submit" class="btn btn-primary fw-semibold px-4 py-2 rounded-3 shadow-sm" style="background: linear-gradient(135deg, #1E40AF 0%, #2563EB 100%); border: none;">
                        <i class="bi bi-check-circle-fill me-1.5"></i> Simpan Data Pegawai
                    </button>
                </div>
            </div>
        </form>

    </div>
</div>
@endsection
