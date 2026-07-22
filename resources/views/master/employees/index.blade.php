@extends('layouts.app')

@section('title', 'Master Data Pegawai')
@section('header', 'Master Data Pegawai ASN')
@section('subtitle', 'Pusat pengelolaan identitas, unit kerja, jabatan, dan atasan langsung ASN BKPSDM Pemalang')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('master.index') }}">Master Data</a></li>
    <li class="breadcrumb-item active" aria-current="page">Pegawai</li>
@endsection

@section('action_buttons')
    <a href="{{ route('master.employees.create') }}" class="btn btn-primary fw-semibold px-3 py-2 rounded-3 shadow-sm" style="background: linear-gradient(135deg, #1E40AF 0%, #2563EB 100%); border: none;">
        <i class="bi bi-person-plus-fill me-1.5"></i> Tambah Pegawai ASN
    </a>
@endsection

@section('content')
<!-- Filter & Search Toolbar Container -->
<div class="card border-0 rounded-4 shadow-sm mb-4">
    <div class="card-body p-3.5" style="background: #F8FAFC; border-radius: 16px;">
        <form method="GET" action="{{ route('master.employees.index') }}" class="row g-2.5 align-items-center">
            <!-- Search input -->
            <div class="col-12 col-lg-4">
                <div class="input-group input-group-solid">
                    <span class="input-group-text bg-white border-end-0 rounded-start-3 text-slate-400 ps-3">
                        <i class="bi bi-search"></i>
                    </span>
                    <input type="text" name="search" class="form-control bg-white border-start-0 rounded-end-3 fs-6" placeholder="Cari Nama, NIP, atau Email..." value="{{ request('search') }}" style="box-shadow: none;">
                </div>
            </div>

            <!-- Filter Unit Kerja -->
            <div class="col-12 col-sm-6 col-lg-3">
                <select name="department_id" class="form-select bg-white rounded-3 border-slate-200" onchange="this.form.submit()" style="font-size: 13.5px;">
                    <option value="">-- Semua Unit Kerja --</option>
                    @foreach($departments as $dept)
                        <option value="{{ $dept->id }}" {{ request('department_id') == $dept->id ? 'selected' : '' }}>{{ $dept->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Filter Jabatan -->
            <div class="col-12 col-sm-6 col-lg-3">
                <select name="position_id" class="form-select bg-white rounded-3 border-slate-200" onchange="this.form.submit()" style="font-size: 13.5px;">
                    <option value="">-- Semua Jabatan --</option>
                    @foreach($positions as $pos)
                        <option value="{{ $pos->id }}" {{ request('position_id') == $pos->id ? 'selected' : '' }}>{{ $pos->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Filter Status & Reset -->
            <div class="col-12 col-lg-2 d-flex gap-2">
                <select name="status" class="form-select bg-white rounded-3 border-slate-200" onchange="this.form.submit()" style="font-size: 13.5px;">
                    <option value="">-- Status --</option>
                    <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>Aktif</option>
                    <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>Nonaktif</option>
                </select>
                @if(request('search') || request('department_id') || request('position_id') || request('status') !== null)
                    <a href="{{ route('master.employees.index') }}" class="btn btn-white border text-slate-600 rounded-3 px-3 d-flex align-items-center" title="Reset Filter">
                        <i class="bi bi-x-lg"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>
</div>

<!-- Employee Table Card -->
<div class="card border-0 rounded-4 shadow-sm overflow-hidden">
    <div class="card-header bg-white py-3 px-4 border-bottom d-flex align-items-center justify-content-between">
        <div class="d-flex align-items-center gap-2">
            <span class="fw-bold text-dark fs-6"><i class="bi bi-people-fill text-primary me-2"></i>Daftar Pegawai ASN</span>
            <span class="badge bg-slate-100 text-slate-700 rounded-pill px-3 py-1 fw-semibold" style="font-size: 11px;">
                Total: {{ $employees->total() }} Pegawai
            </span>
        </div>
    </div>

    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-slate-50 text-slate-600" style="font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px;">
                    <tr>
                        <th class="ps-4 py-3" style="width: 50px;">No</th>
                        <th class="py-3">Pegawai ASN</th>
                        <th class="py-3">Jabatan & Unit Kerja</th>
                        <th class="py-3">Atasan Langsung</th>
                        <th class="py-3">Role Akses</th>
                        <th class="py-3">Status</th>
                        <th class="text-end pe-4 py-3" style="width: 140px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($employees as $index => $emp)
                        @php
                            $words = explode(' ', trim($emp->name));
                            $initials = '';
                            if (count($words) >= 2) {
                                $initials = mb_substr($words[0], 0, 1) . mb_substr($words[1], 0, 1);
                            } else {
                                $initials = mb_substr($words[0], 0, 2);
                            }
                        @endphp
                        <tr>
                            <td class="ps-4 fw-bold text-slate-400" style="font-size: 13px;">
                                {{ $employees->firstItem() + $index }}
                            </td>
                            <td class="py-3">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="rounded-circle d-flex align-items-center justify-content-center fw-bold text-primary flex-shrink-0" style="width: 40px; height: 40px; background: #EFF6FF; border: 1px solid #BFDBFE; font-size: 13px;">
                                        {{ strtoupper($initials) }}
                                    </div>
                                    <div>
                                        <div class="fw-bold text-dark mb-0" style="font-size: 14px;">{{ $emp->name }}</div>
                                        <div class="text-slate-500" style="font-size: 12px;">
                                            <span class="fw-medium">NIP. {{ $emp->nip }}</span>
                                            <span class="text-slate-300 mx-1">•</span>
                                            <span class="text-slate-400">{{ $emp->email }}</span>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="py-3">
                                <div class="fw-semibold text-slate-800 mb-0.5" style="font-size: 13.5px;">{{ $emp->position->name ?? '-' }}</div>
                                <span class="badge bg-slate-100 text-slate-600 border border-slate-200 fw-normal px-2.5 py-0.5 rounded-2" style="font-size: 11.5px;">
                                    <i class="bi bi-building me-1 opacity-75"></i>{{ $emp->department->name ?? '-' }}
                                </span>
                            </td>
                            <td class="py-3">
                                @if($emp->supervisor)
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="rounded-circle bg-slate-100 text-slate-600 d-flex align-items-center justify-content-center fw-semibold" style="width: 26px; height: 26px; font-size: 10px;">
                                            <i class="bi bi-person-badge"></i>
                                        </div>
                                        <div>
                                            <div class="fw-medium text-slate-700 mb-0" style="font-size: 12.5px;">{{ $emp->supervisor->name }}</div>
                                            <div class="text-slate-400" style="font-size: 11px;">NIP. {{ $emp->supervisor->nip }}</div>
                                        </div>
                                    </div>
                                @else
                                    <span class="text-slate-400 small fs-7">-</span>
                                @endif
                            </td>
                            <td class="py-3">
                                <span class="badge bg-blue-50 text-blue-700 border border-blue-200 fw-semibold px-2.5 py-1 rounded-2" style="font-size: 11.5px;">
                                    {{ $emp->role->name ?? 'EMPLOYEE' }}
                                </span>
                            </td>
                            <td class="py-3">
                                @if($emp->is_active)
                                    <span class="badge bg-emerald-50 text-emerald-700 border border-emerald-200 fw-semibold px-2.5 py-1 rounded-pill" style="font-size: 11.5px;">
                                        <i class="bi bi-check-circle-fill me-1"></i> Aktif
                                    </span>
                                @else
                                    <span class="badge bg-rose-50 text-rose-700 border border-rose-200 fw-semibold px-2.5 py-1 rounded-pill" style="font-size: 11.5px;">
                                        <i class="bi bi-x-circle-fill me-1"></i> Nonaktif
                                    </span>
                                @endif
                            </td>
                            <td class="text-end pe-4 py-3">
                                <div class="d-inline-flex gap-1.5">
                                    <a href="{{ route('master.employees.show', $emp) }}" class="btn btn-sm btn-icon rounded-3" style="background: #E0F2FE; color: #0284C7; width: 32px; height: 32px;" title="Detail Profile">
                                        <i class="bi bi-eye-fill"></i>
                                    </a>
                                    <a href="{{ route('master.employees.edit', $emp) }}" class="btn btn-sm btn-icon rounded-3" style="background: #EFF6FF; color: #2563EB; width: 32px; height: 32px;" title="Edit Data">
                                        <i class="bi bi-pencil-fill"></i>
                                    </a>
                                    <form action="{{ route('master.employees.destroy', $emp) }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data pegawai {{ $emp->name }}?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-icon rounded-3" style="background: #FFF1F2; color: #E11D48; width: 32px; height: 32px;" title="Hapus Data">
                                            <i class="bi bi-trash-fill"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-5">
                                <i class="bi bi-inbox text-slate-300 display-6 d-block mb-2"></i>
                                Belum ada data pegawai ASN ditemukan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if($employees->hasPages())
        <div class="card-footer bg-white py-3 border-top">
            {{ $employees->withQueryString()->links() }}
        </div>
    @endif
</div>
@endsection
