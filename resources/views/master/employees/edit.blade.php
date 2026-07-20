@extends('layouts.app')
@section('header', 'Edit Pegawai')
@section('content')
<x-page-header title="Edit Pegawai" subtitle="Ubah data pegawai.">
    <x-slot:actions><a href="{{ route('master.employees.index') }}" class="text-sm font-semibold text-indigo-600 hover:text-indigo-500">&larr; Kembali</a></x-slot>
</x-page-header>
<x-card class="max-w-4xl">
    <div class="p-4 sm:p-6">
        <form action="{{ route('master.employees.update', $employee) }}" method="POST">
            @csrf @method('PUT')
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <x-form.input name="nip" label="NIP" value="{{ old('nip', $employee->nip) }}" error="{{ $errors->first('nip') }}" required />
                <x-form.input name="name" label="Nama Lengkap" value="{{ old('name', $employee->name) }}" error="{{ $errors->first('name') }}" required />
                <x-form.input name="email" label="Email" type="email" value="{{ old('email', $employee->email) }}" error="{{ $errors->first('email') }}" required />
                <x-form.input name="password" label="Password (Opsional, kosongkan jika tidak diubah)" type="password" error="{{ $errors->first('password') }}" />
                <x-form.input name="phone" label="Nomor HP" value="{{ old('phone', $employee->phone) }}" error="{{ $errors->first('phone') }}" />
                <x-form.select name="gender" label="Jenis Kelamin" error="{{ $errors->first('gender') }}">
                    <option value="">-- Pilih --</option>
                    <option value="L" {{ old('gender', $employee->gender) == 'L' ? 'selected' : '' }}>Laki-laki</option>
                    <option value="P" {{ old('gender', $employee->gender) == 'P' ? 'selected' : '' }}>Perempuan</option>
                </x-form.select>
                
                <x-form.select name="department_id" label="Bidang" error="{{ $errors->first('department_id') }}" required>
                    <option value="">-- Pilih Bidang --</option>
                    @foreach($departments as $dept)
                        <option value="{{ $dept->id }}" {{ old('department_id', $employee->department_id) == $dept->id ? 'selected' : '' }}>{{ $dept->name }}</option>
                    @endforeach
                </x-form.select>
                <x-form.select name="position_id" label="Jabatan" error="{{ $errors->first('position_id') }}" required>
                    <option value="">-- Pilih Jabatan --</option>
                    @foreach($positions as $pos)
                        <option value="{{ $pos->id }}" {{ old('position_id', $employee->position_id) == $pos->id ? 'selected' : '' }}>{{ $pos->name }}</option>
                    @endforeach
                </x-form.select>
                
                <x-form.select name="role_id" label="Role" error="{{ $errors->first('role_id') }}" required>
                    <option value="">-- Pilih Role --</option>
                    @foreach($roles as $role)
                        <option value="{{ $role->id }}" {{ old('role_id', $employee->role_id) == $role->id ? 'selected' : '' }}>{{ $role->name }}</option>
                    @endforeach
                </x-form.select>
                
                <x-form.select name="supervisor_id" label="Atasan (Boleh Kosong untuk Kepala BKPSDM)" error="{{ $errors->first('supervisor_id') }}">
                    <option value="">-- Tidak Ada Atasan --</option>
                    @foreach($supervisors as $spv)
                        <option value="{{ $spv->id }}" {{ old('supervisor_id', $employee->supervisor_id) == $spv->id ? 'selected' : '' }}>{{ $spv->name }}</option>
                    @endforeach
                </x-form.select>
                
                <x-form.select name="is_active" label="Status" error="{{ $errors->first('is_active') }}">
                    <option value="1" {{ old('is_active', $employee->is_active) == '1' ? 'selected' : '' }}>Aktif</option>
                    <option value="0" {{ old('is_active', $employee->is_active) == '0' ? 'selected' : '' }}>Nonaktif</option>
                </x-form.select>
            </div>
            
            <div class="mt-6 flex items-center justify-end gap-x-6">
                <a href="{{ route('master.employees.index') }}" class="text-sm font-semibold text-gray-900">Batal</a>
                <x-button type="submit" variant="primary">Simpan Perubahan</x-button>
            </div>
        </form>
    </div>
</x-card>
@endsection
