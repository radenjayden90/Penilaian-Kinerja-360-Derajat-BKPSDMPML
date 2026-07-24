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
    @livewire('master.employee-index')
@endsection
