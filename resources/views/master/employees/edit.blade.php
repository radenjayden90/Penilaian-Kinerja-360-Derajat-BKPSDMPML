@extends('layouts.app')

@section('title', 'Edit Data Pegawai')
@section('header', 'Edit Data Pegawai ASN')
@section('subtitle', 'Pembaruan informasi pegawai, jabatan, dan struktur atasan')

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.bootstrap5.min.css" rel="stylesheet">
<style>
    .ts-control {
        border-color: #e2e8f0;
        padding: 0.5rem 0.75rem;
        border-radius: 0.375rem;
    }
</style>
@endpush

@section('breadcrumb')
    <li class="breadcrumb-item"><a wire:navigate href="{{ route('master.index') }}">Master Data</a></li>
    <li class="breadcrumb-item"><a wire:navigate href="{{ route('master.employees.index') }}">Pegawai</a></li>
    <li class="breadcrumb-item active" aria-current="page">Edit</li>
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-12 col-xl-10">

        <!-- Top Profile Banner -->
        <div class="card border-0 rounded-4 shadow-sm mb-4 overflow-hidden" style="background: linear-gradient(135deg, #0F172A 0%, #1E3A5F 100%);">
            <div class="card-body p-4 text-white">
                <div class="row align-items-center g-3">
                    <div class="col-12 col-md-8">
                        <div class="d-flex align-items-center gap-3">
                            @php
                                $words = explode(' ', trim($employee->name));
                                $initials = '';
                                if (count($words) >= 2) {
                                    $initials = mb_substr($words[0], 0, 1) . mb_substr($words[1], 0, 1);
                                } else {
                                    $initials = mb_substr($words[0], 0, 2);
                                }
                            @endphp
                            <div class="rounded-circle bg-white text-primary d-flex align-items-center justify-content-center fw-extrabold shadow-sm flex-shrink-0" style="width: 54px; height: 54px; font-size: 20px; color: #1E40AF !important; border: 2px solid rgba(255, 255, 255, 0.9);">
                                {{ strtoupper($initials) }}
                            </div>
                            <div>
                                <span class="badge rounded-pill px-3 py-1 mb-1" style="background: rgba(245, 158, 11, 0.2); color: #FBBF24; border: 1px solid rgba(251, 191, 36, 0.4); font-size: 11px; font-weight: 700; letter-spacing: 0.5px;">
                                    <i class="bi bi-pencil-square me-1"></i> MODUL EDIT ASN
                                </span>
                                <h3 class="fw-bold text-white mb-0" style="font-size: 20px;">{{ $employee->name }}</h3>
                                <div class="text-white text-opacity-75 small mt-0.5">
                                    <span>NIP. {{ $employee->nip }}</span>
                                    <span class="mx-1">•</span>
                                    <span>{{ $employee->position->name ?? 'Jabatan Belum Diatur' }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-md-4 text-md-end">
                        <a wire:navigate href="{{ route('master.employees.index') }}" class="btn btn-light text-slate-700 fw-semibold px-3 py-2 rounded-3 shadow-sm" style="font-size: 13px;">
                            <i class="bi bi-arrow-left me-1"></i> Kembali ke Daftar
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Form Card -->
        <form action="{{ route('master.employees.update', $employee) }}" method="POST">
            @csrf
            @method('PUT')

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
                                <input type="text" name="nip" id="nip" class="form-control border-slate-200 bg-white @error('nip') is-invalid @enderror" value="{{ old('nip', $employee->nip) }}" required placeholder="Contoh: 198502142010011004">
                            </div>
                            @error('nip') <div class="text-rose-600 small mt-1">{{ $message }}</div> @enderror
                        </div>

                        <!-- Nama Lengkap -->
                        <div class="col-12 col-md-6">
                            <label for="name" class="form-label fw-semibold text-slate-700" style="font-size: 13px;">Nama Lengkap & Gelar <span class="text-rose-500">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-slate-50 border-slate-200 text-slate-400"><i class="bi bi-person-fill"></i></span>
                                <input type="text" name="name" id="name" class="form-control border-slate-200 bg-white @error('name') is-invalid @enderror" value="{{ old('name', $employee->name) }}" required placeholder="Contoh: Dra. Rini Susanti, M.Si">
                            </div>
                            @error('name') <div class="text-rose-600 small mt-1">{{ $message }}</div> @enderror
                        </div>

                        <!-- Email Official -->
                        <div class="col-12 col-md-6">
                            <label for="email" class="form-label fw-semibold text-slate-700" style="font-size: 13px;">Email Official <span class="text-rose-500">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-slate-50 border-slate-200 text-slate-400"><i class="bi bi-envelope-fill"></i></span>
                                <input type="email" name="email" id="email" class="form-control border-slate-200 bg-white @error('email') is-invalid @enderror" value="{{ old('email', $employee->email) }}" required placeholder="nama@pemalang.go.id">
                            </div>
                            @error('email') <div class="text-rose-600 small mt-1">{{ $message }}</div> @enderror
                        </div>

                        <!-- Password Baru -->
                        <div class="col-12 col-md-6">
                            <label for="password" class="form-label fw-semibold text-slate-700" style="font-size: 13px;">Password Baru <span class="text-slate-400 font-normal">(Opsional)</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-slate-50 border-slate-200 text-slate-400"><i class="bi bi-key-fill"></i></span>
                                <input type="password" name="password" id="password" class="form-control border-slate-200 bg-white @error('password') is-invalid @enderror" placeholder="Kosongkan jika tidak ingin mengubah password">
                            </div>
                            @error('password') <div class="text-rose-600 small mt-1">{{ $message }}</div> @enderror
                        </div>

                        <!-- WhatsApp / Telepon -->
                        <div class="col-12 col-md-6">
                            <label for="phone" class="form-label fw-semibold text-slate-700" style="font-size: 13px;">No. WhatsApp / Telepon</label>
                            <div class="input-group">
                                <span class="input-group-text bg-slate-50 border-slate-200 text-slate-400"><i class="bi bi-whatsapp"></i></span>
                                <input type="text" name="phone" id="phone" class="form-control border-slate-200 bg-white @error('phone') is-invalid @enderror" value="{{ old('phone', $employee->phone) }}" placeholder="Contoh: 081234567890">
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
                                    <option value="L" {{ old('gender', $employee->gender) === 'L' ? 'selected' : '' }}>Laki-laki</option>
                                    <option value="P" {{ old('gender', $employee->gender) === 'P' ? 'selected' : '' }}>Perempuan</option>
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
                                <select name="department_id" id="department_id" class="form-select border-slate-200 bg-white @error('department_id') is-invalid @enderror" required style="text-overflow: ellipsis; padding-right: 2.5rem;">
                                    <option value="">-- Pilih Unit Kerja --</option>
                                    @foreach($departments as $dept)
                                        <option value="{{ $dept->id }}" {{ old('department_id', $employee->department_id) == $dept->id ? 'selected' : '' }}>{{ $dept->name }}</option>
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
                                <select name="position_id" id="position_id" class="form-select border-slate-200 bg-white @error('position_id') is-invalid @enderror" required style="text-overflow: ellipsis; padding-right: 2.5rem;">
                                    <option value="">-- Pilih Jabatan --</option>
                                    @foreach($positions as $pos)
                                        <option value="{{ $pos->id }}" {{ old('position_id', $employee->position_id) == $pos->id ? 'selected' : '' }}>{{ $pos->name }}</option>
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
                                <input type="hidden" name="supervisor_id" id="supervisor_id_hidden" value="{{ old('supervisor_id', $employee->supervisor_id) }}">
                                <select id="supervisor_id" class="form-select border-slate-200 bg-white" disabled style="text-overflow: ellipsis; padding-right: 2.5rem; background-color: #f8fafc !important;">
                                    <option value="">-- Tanpa Atasan (Terisi Otomatis) --</option>
                                    @foreach($supervisors as $sup)
                                        <option value="{{ $sup->id }}" data-department="{{ $sup->department_id }}" data-role="{{ $sup->role?->name }}" data-position="{{ $sup->position?->name }}" {{ old('supervisor_id', $employee->supervisor_id) == $sup->id ? 'selected' : '' }}>{{ $sup->name }} (NIP. {{ $sup->nip }})</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="text-slate-500 small mt-1"><i class="bi bi-info-circle me-1"></i>Atasan otomatis ditentukan oleh sistem berdasarkan unit kerja dan jabatan.</div>
                            @error('supervisor_id') <div class="text-rose-600 small mt-1">{{ $message }}</div> @enderror
                        </div>

                        <!-- Role Akses Sistem -->
                        <div class="col-12 col-md-6">
                            <label for="role_id" class="form-label fw-semibold text-slate-700" style="font-size: 13px;">Role Akses Sistem <span class="text-rose-500">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-slate-50 border-slate-200 text-slate-400"><i class="bi bi-shield-check"></i></span>
                                <select name="role_id" id="role_id" class="form-select border-slate-200 bg-white @error('role_id') is-invalid @enderror" required style="text-overflow: ellipsis; padding-right: 2.5rem;">
                                    <option value="">-- Pilih Role --</option>
                                    @foreach($roles as $role)
                                        <option value="{{ $role->id }}" data-code="{{ $role->name }}" {{ old('role_id', $employee->role_id) == $role->id ? 'selected' : '' }}>{{ $role->name }} - {{ $role->description }}</option>
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
                                    <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', $employee->is_active) ? 'checked' : '' }} style="cursor: pointer;">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Form Action Buttons Footer -->
                <div class="card-footer bg-white py-3.5 px-4 border-top d-flex justify-content-end gap-2">
                    <a wire:navigate href="{{ route('master.employees.index') }}" class="btn btn-light text-slate-700 fw-semibold px-4 py-2 rounded-3 border border-slate-200">
                        Batal
                    </a>
                    <button type="submit" class="btn btn-primary fw-semibold px-4 py-2 rounded-3 shadow-sm" style="background: linear-gradient(135deg, #1E40AF 0%, #2563EB 100%); border: none;">
                        <i class="bi bi-check-circle-fill me-1.5"></i> Perbarui Data Pegawai
                    </button>
                </div>
            </div>
        </form>

    </div>
</div>
<script>
function updateSupervisorAndRole() {
    const departmentSelect = document.getElementById('department_id');
    const positionSelect = document.getElementById('position_id');
    const roleSelect = document.getElementById('role_id');
    const supervisorSelect = document.getElementById('supervisor_id');

    if (!positionSelect || !roleSelect || !supervisorSelect) return;

    const selectedPositionOption = positionSelect.options[positionSelect.selectedIndex];
    const positionText = (selectedPositionOption && selectedPositionOption.value) ? selectedPositionOption.text.toLowerCase() : '';
    const selectedDeptId = departmentSelect ? departmentSelect.value : '';

    // 1. Role Logic
    let targetRoleCode = 'EMPLOYEE';
    if (positionText.includes('administrator')) {
        targetRoleCode = 'ADMIN';
    } else if (positionText.includes('kepala bidang') || positionText.includes('kepala bkpsdm') || positionText.includes('sekretaris')) {
        targetRoleCode = 'HEAD';
    }

    Array.from(roleSelect.options).forEach(option => {
        if (option.getAttribute('data-code') === targetRoleCode) {
            roleSelect.value = option.value;
        }
    });

    // 2. Supervisor Logic
    if (positionText.includes('kepala bkpsdm') || positionText.includes('administrator')) {
        if (supervisorSelect.tomselect) supervisorSelect.tomselect.setValue("");
        else supervisorSelect.value = ""; // Tanpa atasan
        document.getElementById('supervisor_id_hidden').value = "";
    } else if (positionText.includes('kepala bidang') || positionText.includes('sekretaris')) {
        let kepalaBkpsdmId = null;
        Array.from(supervisorSelect.options).forEach(option => {
            const pos = (option.getAttribute('data-position') || '').toLowerCase();
            if (pos.includes('kepala bkpsdm')) {
                kepalaBkpsdmId = option.value;
            }
        });
        if (kepalaBkpsdmId) {
            if (supervisorSelect.tomselect) supervisorSelect.tomselect.setValue(kepalaBkpsdmId);
            else supervisorSelect.value = kepalaBkpsdmId;
            document.getElementById('supervisor_id_hidden').value = kepalaBkpsdmId;
        } else {
            document.getElementById('supervisor_id_hidden').value = "";
        }
    } else {
        // Pegawai Biasa -> Sesuaikan dengan unit kerjanya
        if (!selectedDeptId) {
            if (supervisorSelect.tomselect) supervisorSelect.tomselect.setValue("");
            else supervisorSelect.value = "";
            document.getElementById('supervisor_id_hidden').value = "";
        } else {
            let headId = null;
            Array.from(supervisorSelect.options).forEach(option => {
                const dept = option.getAttribute('data-department');
                const role = option.getAttribute('data-role');
                if (dept === selectedDeptId && role === 'HEAD') {
                    headId = option.value;
                }
            });
            if (headId) {
                if (supervisorSelect.tomselect) supervisorSelect.tomselect.setValue(headId);
                else supervisorSelect.value = headId;
                document.getElementById('supervisor_id_hidden').value = headId;
            } else {
                if (supervisorSelect.tomselect) supervisorSelect.tomselect.setValue("");
                else supervisorSelect.value = "";
                document.getElementById('supervisor_id_hidden').value = "";
            }
        }
    }
}

document.addEventListener('DOMContentLoaded', function() {
    const departmentSelect = document.getElementById('department_id');
    const positionSelect = document.getElementById('position_id');

    if (departmentSelect) {
        departmentSelect.addEventListener('change', updateSupervisorAndRole);
    }
    if (positionSelect) {
        positionSelect.addEventListener('change', updateSupervisorAndRole);
    }
});
</script>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    if (document.getElementById('department_id')) {
        new TomSelect('#department_id', {
            create: false,
            sortField: { field: "text", direction: "asc" }
        });
    }
    if (document.getElementById('position_id')) {
        new TomSelect('#position_id', {
            create: false,
            sortField: { field: "text", direction: "asc" }
        });
    }
    if (document.getElementById('supervisor_id')) {
        new TomSelect('#supervisor_id', {
            create: false,
            sortField: { field: "text", direction: "asc" }
        });
    }
});
</script>
@endpush
@endsection
