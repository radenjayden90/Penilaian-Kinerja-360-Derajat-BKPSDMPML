@extends('layouts.app')
@section('header', 'Tambah Aspek Penilaian')
@section('content')
<x-page-header title="Tambah Aspek Penilaian" subtitle="Tambahkan data aspek penilaian baru.">
    <x-slot:actions><a href="{{ route('master.assessment-categories.index') }}" class="text-sm font-semibold text-indigo-600 hover:text-indigo-500">&larr; Kembali</a></x-slot>
</x-page-header>
<x-card class="max-w-2xl">
    <div class="p-4 sm:p-6">
        <form action="{{ route('master.assessment-categories.store') }}" method="POST">
            @csrf
            <div class="space-y-6">
                <x-form.input name="name" label="Nama Aspek" value="{{ old('name') }}" error="{{ $errors->first('name') }}" required />
                <x-form.textarea name="description" label="Deskripsi" rows="4" error="{{ $errors->first('description') }}">{{ old('description') }}</x-form.textarea>
                <x-form.input name="display_order" label="Urutan Tampil" type="number" min="0" value="{{ old('display_order', 0) }}" error="{{ $errors->first('display_order') }}" required />
                <x-form.select name="is_active" label="Status" error="{{ $errors->first('is_active') }}">
                    <option value="1" {{ old('is_active', '1') == '1' ? 'selected' : '' }}>Aktif</option>
                    <option value="0" {{ old('is_active') == '0' ? 'selected' : '' }}>Nonaktif</option>
                </x-form.select>
            </div>
            
            <div class="mt-6 flex items-center justify-end gap-x-6">
                <a href="{{ route('master.assessment-categories.index') }}" class="text-sm font-semibold text-gray-900">Batal</a>
                <x-button type="submit" variant="primary">Simpan</x-button>
            </div>
        </form>
    </div>
</x-card>
@endsection
