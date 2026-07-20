@extends('layouts.app')
@section('header', 'Edit Periode')
@section('content')
<x-page-header title="Edit Periode" subtitle="Ubah data periode penilaian.">
    <x-slot:actions><a href="{{ route('master.periods.index') }}" class="text-sm font-semibold text-indigo-600 hover:text-indigo-500">&larr; Kembali</a></x-slot>
</x-page-header>
<x-card class="max-w-2xl">
    <div class="p-4 sm:p-6">
        <form action="{{ route('master.periods.update', $period) }}" method="POST">
            @csrf @method('PUT')
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="md:col-span-2">
                    <x-form.input name="name" label="Nama Periode" value="{{ old('name', $period->name) }}" error="{{ $errors->first('name') }}" required />
                </div>
                <x-form.select name="month" label="Bulan" error="{{ $errors->first('month') }}" required>
                    <option value="">-- Pilih Bulan --</option>
                    @for($i = 1; $i <= 12; $i++)
                        <option value="{{ $i }}" {{ old('month', $period->month) == $i ? 'selected' : '' }}>{{ date('F', mktime(0, 0, 0, $i, 1)) }}</option>
                    @endfor
                </x-form.select>
                <x-form.input name="year" label="Tahun" type="number" min="2020" max="2100" value="{{ old('year', $period->year) }}" error="{{ $errors->first('year') }}" required />
                
                <x-form.input name="start_date" label="Tanggal Mulai" type="date" value="{{ old('start_date', $period->start_date->format('Y-m-d')) }}" error="{{ $errors->first('start_date') }}" required />
                <x-form.input name="end_date" label="Tanggal Selesai" type="date" value="{{ old('end_date', $period->end_date->format('Y-m-d')) }}" error="{{ $errors->first('end_date') }}" required />
                
                <x-form.select name="status" label="Status Data" error="{{ $errors->first('status') }}" required>
                    <option value="OPEN" {{ old('status', $period->status->value ?? $period->status) == 'OPEN' ? 'selected' : '' }}>OPEN</option>
                    <option value="CLOSED" {{ old('status', $period->status->value ?? $period->status) == 'CLOSED' ? 'selected' : '' }}>CLOSED</option>
                    <option value="ARCHIVED" {{ old('status', $period->status->value ?? $period->status) == 'ARCHIVED' ? 'selected' : '' }}>ARCHIVED</option>
                </x-form.select>
                <x-form.select name="is_active" label="Status Aktif Berjalan" error="{{ $errors->first('is_active') }}">
                    <option value="1" {{ old('is_active', $period->is_active) == '1' ? 'selected' : '' }}>Aktif</option>
                    <option value="0" {{ old('is_active', $period->is_active) == '0' ? 'selected' : '' }}>Nonaktif</option>
                </x-form.select>
            </div>
            
            <div class="mt-6 flex items-center justify-end gap-x-6">
                <a href="{{ route('master.periods.index') }}" class="text-sm font-semibold text-gray-900">Batal</a>
                <x-button type="submit" variant="primary">Simpan Perubahan</x-button>
            </div>
        </form>
    </div>
</x-card>
@endsection
