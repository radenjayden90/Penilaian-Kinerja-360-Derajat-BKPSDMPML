@extends('layouts.app')
@section('header', 'Tambah Jabatan')
@section('content')
<x-page-header title="Tambah Jabatan" subtitle="Tambahkan data jabatan baru ke dalam sistem.">
    <x-slot:actions><a href="{{ route('master.positions.index') }}" class="text-sm font-semibold text-indigo-600 hover:text-indigo-500">&larr; Kembali</a></x-slot>
</x-page-header>
<x-card class="max-w-2xl">
    <div class="p-4 sm:p-6">
        <form action="{{ route('master.positions.store') }}" method="POST">
            @csrf
            <div class="space-y-6">
                <x-form.select name="department_id" label="Bidang" error="{{ $errors->first('department_id') }}" required>
                    <option value="">-- Pilih Bidang --</option>
                    @foreach($departments as $dept)
                        <option value="{{ $dept->id }}" {{ old('department_id') == $dept->id ? 'selected' : '' }}>{{ $dept->name }}</option>
                    @endforeach
                </x-form.select>
                <x-form.input name="name" label="Nama Jabatan" value="{{ old('name') }}" error="{{ $errors->first('name') }}" required />
                <x-form.input name="level" label="Level Jabatan" type="number" min="1" max="10" value="{{ old('level') }}" error="{{ $errors->first('level') }}" required />
                <x-form.textarea name="description" label="Deskripsi" rows="4" error="{{ $errors->first('description') }}">{{ old('description') }}</x-form.textarea>
                <x-form.select name="is_active" label="Status" error="{{ $errors->first('is_active') }}">
                    <option value="1" {{ old('is_active', '1') == '1' ? 'selected' : '' }}>Aktif</option>
                    <option value="0" {{ old('is_active') == '0' ? 'selected' : '' }}>Nonaktif</option>
                </x-form.select>
            </div>
            <div class="mt-6 flex items-center justify-end gap-x-6">
                <a href="{{ route('master.positions.index') }}" class="text-sm font-semibold text-gray-900">Batal</a>
                <x-button type="submit" variant="primary">Simpan</x-button>
            </div>
        </form>
    </div>
</x-card>
@endsection
