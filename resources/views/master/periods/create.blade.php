@extends('layouts.app')
@section('header', 'Tambah Periode')
@section('content')
<x-page-header title="Tambah Periode" subtitle="Tambahkan data periode penilaian baru.">
    <x-slot:actions><a href="{{ route('master.periods.index') }}" class="text-sm font-semibold text-indigo-600 hover:text-indigo-500">&larr; Kembali</a></x-slot>
</x-page-header>
<x-card class="max-w-2xl">
    <div class="p-4 sm:p-6">
        <div class="bg-blue-50 border-l-4 border-blue-400 p-4 mb-6">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-blue-400" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" /></svg>
                </div>
                <div class="ml-3"><p class="text-sm text-blue-700">Jika Anda menyimpan periode ini sebagai <b>Aktif</b>, maka periode aktif sebelumnya akan otomatis di-set menjadi <b>CLOSED</b> dan Tidak Aktif.</p></div>
            </div>
        </div>
        <form action="{{ route('master.periods.store') }}" method="POST">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="md:col-span-2">
                    <x-form.input name="name" label="Nama Periode (Contoh: Penilaian Q1 2026)" value="{{ old('name') }}" error="{{ $errors->first('name') }}" required />
                </div>
                <x-form.select name="month" label="Bulan" error="{{ $errors->first('month') }}" required>
                    <option value="">-- Pilih Bulan --</option>
                    @for($i = 1; $i <= 12; $i++)
                        <option value="{{ $i }}" {{ old('month', date('n')) == $i ? 'selected' : '' }}>{{ date('F', mktime(0, 0, 0, $i, 1)) }}</option>
                    @endfor
                </x-form.select>
                <x-form.input name="year" label="Tahun" type="number" min="2020" max="2100" value="{{ old('year', date('Y')) }}" error="{{ $errors->first('year') }}" required />
                
                <x-form.input name="start_date" label="Tanggal Mulai" type="date" value="{{ old('start_date') }}" error="{{ $errors->first('start_date') }}" required />
                <x-form.input name="end_date" label="Tanggal Selesai" type="date" value="{{ old('end_date') }}" error="{{ $errors->first('end_date') }}" required />
                
                <x-form.select name="status" label="Status Data" error="{{ $errors->first('status') }}" required>
                    <option value="OPEN" {{ old('status', 'OPEN') == 'OPEN' ? 'selected' : '' }}>OPEN</option>
                    <option value="CLOSED" {{ old('status') == 'CLOSED' ? 'selected' : '' }}>CLOSED</option>
                    <option value="ARCHIVED" {{ old('status') == 'ARCHIVED' ? 'selected' : '' }}>ARCHIVED</option>
                </x-form.select>
                <x-form.select name="is_active" label="Status Aktif Berjalan" error="{{ $errors->first('is_active') }}">
                    <option value="1" {{ old('is_active') == '1' ? 'selected' : '' }}>Aktif</option>
                    <option value="0" {{ old('is_active', '0') == '0' ? 'selected' : '' }}>Nonaktif</option>
                </x-form.select>
            </div>
            
            <div class="mt-6 flex items-center justify-end gap-x-6">
                <a href="{{ route('master.periods.index') }}" class="text-sm font-semibold text-gray-900">Batal</a>
                <x-button type="submit" variant="primary">Simpan</x-button>
            </div>
        </form>
    </div>
</x-card>
@endsection
