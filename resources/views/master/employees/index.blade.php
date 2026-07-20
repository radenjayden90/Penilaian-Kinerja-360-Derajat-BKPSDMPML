@extends('layouts.app')
@section('header', 'Master Data Pegawai')
@section('content')
<x-page-header title="Daftar Pegawai" subtitle="Kelola data pegawai instansi.">
    <x-slot:actions><a href="{{ route('master.employees.create') }}"><x-button variant="primary">Tambah Pegawai</x-button></a></x-slot>
</x-page-header>
@if(session('success'))
    <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative">
        <span class="block sm:inline">{{ session('success') }}</span>
    </div>
@endif
<x-card>
    <div class="p-4 sm:p-6" x-data="{ submitForm() { $refs.searchForm.submit(); } }">
        <form x-ref="searchForm" method="GET" action="{{ route('master.employees.index') }}" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
            <div>
                <x-form.input name="search" value="{{ request('search') }}" @input.debounce.500ms="submitForm()" placeholder="Cari NIP, Nama..." />
            </div>
            <div>
                <x-form.select name="department_id" @change="submitForm()">
                    <option value="">Semua Bidang</option>
                    @foreach($departments as $dept)
                        <option value="{{ $dept->id }}" {{ request('department_id') == $dept->id ? 'selected' : '' }}>{{ $dept->name }}</option>
                    @endforeach
                </x-form.select>
            </div>
            <div>
                <x-form.select name="position_id" @change="submitForm()">
                    <option value="">Semua Jabatan</option>
                    @foreach($positions as $pos)
                        <option value="{{ $pos->id }}" {{ request('position_id') == $pos->id ? 'selected' : '' }}>{{ $pos->name }}</option>
                    @endforeach
                </x-form.select>
            </div>
            <div>
                <x-form.select name="status" @change="submitForm()">
                    <option value="">Semua Status</option>
                    <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>Aktif</option>
                    <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>Nonaktif</option>
                </x-form.select>
            </div>
        </form>
        <x-table.index>
            <x-slot:header>
                <x-table.th>Pegawai</x-table.th>
                <x-table.th>Bidang & Jabatan</x-table.th>
                <x-table.th>Role</x-table.th>
                <x-table.th>Status</x-table.th>
                <x-table.th class="text-right">Aksi</x-table.th>
            </x-slot>
            @forelse($employees as $employee)
                <tr>
                    <x-table.td>
                        <div class="flex items-center">
                            <div class="h-10 w-10 flex-shrink-0">
                                <img class="h-10 w-10 rounded-full bg-gray-50 object-cover" src="{{ $employee->avatar ? asset('storage/' . $employee->avatar) : 'https://ui-avatars.com/api/?name='.urlencode($employee->name).'&color=7F9CF5&background=EBF4FF' }}" alt="">
                            </div>
                            <div class="ml-4">
                                <div class="font-medium text-gray-900">{{ $employee->name }}</div>
                                <div class="text-gray-500">{{ $employee->nip }}</div>
                            </div>
                        </div>
                    </x-table.td>
                    <x-table.td>
                        <div class="text-gray-900">{{ $employee->department->name ?? '-' }}</div>
                        <div class="text-gray-500">{{ $employee->position->name ?? '-' }}</div>
                    </x-table.td>
                    <x-table.td>{{ $employee->role->name ?? '-' }}</x-table.td>
                    <x-table.td>
                        @if($employee->is_active)<x-badge color="green">Aktif</x-badge>@else<x-badge color="red">Nonaktif</x-badge>@endif
                    </x-table.td>
                    <x-table.td class="text-right text-sm font-medium">
                        <a href="{{ route('master.employees.edit', $employee) }}" class="text-indigo-600 hover:text-indigo-900 mr-3">Edit</a>
                        <form action="{{ route('master.employees.destroy', $employee) }}" method="POST" class="inline-block" x-data @submit.prevent="if (confirm('Apakah Anda yakin ingin menghapus data ini?')) $el.submit()">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-900">Hapus</button>
                        </form>
                    </x-table.td>
                </tr>
            @empty
                <x-table.empty colspan="5" message="Tidak ada data pegawai." />
            @endforelse
        </x-table.index>
        <div class="mt-4">{{ $employees->withQueryString()->links() }}</div>
    </div>
</x-card>
@endsection
