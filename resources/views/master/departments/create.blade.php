@extends('layouts.app')

@section('header', 'Tambah Bidang')

@section('content')
<x-page-header title="Tambah Bidang" subtitle="Tambahkan data bidang baru ke dalam sistem.">
    <x-slot:actions>
        <a href="{{ route('master.departments.index') }}" class="text-sm font-semibold leading-6 text-indigo-600 hover:text-indigo-500">
            &larr; Kembali
        </a>
    </x-slot>
</x-page-header>

<x-card class="max-w-2xl">
    <div class="p-4 sm:p-6">
        <form action="{{ route('master.departments.store') }}" method="POST">
            @csrf
            <div class="space-y-6">
                <x-form.input name="code" label="Kode Bidang" value="{{ old('code') }}" error="{{ $errors->first('code') }}" required />
                
                <x-form.input name="name" label="Nama Bidang" value="{{ old('name') }}" error="{{ $errors->first('name') }}" required />
                
                <x-form.textarea name="description" label="Deskripsi" rows="4" error="{{ $errors->first('description') }}">{{ old('description') }}</x-form.textarea>
                
                <x-form.select name="is_active" label="Status" error="{{ $errors->first('is_active') }}">
                    <option value="1" {{ old('is_active', '1') == '1' ? 'selected' : '' }}>Aktif</option>
                    <option value="0" {{ old('is_active') == '0' ? 'selected' : '' }}>Nonaktif</option>
                </x-form.select>
            </div>
            
            <div class="mt-6 flex items-center justify-end gap-x-6">
                <a href="{{ route('master.departments.index') }}" class="text-sm font-semibold leading-6 text-gray-900">Batal</a>
                <x-button type="submit" variant="primary">Simpan</x-button>
            </div>
        </form>
    </div>
</x-card>
@endsection
