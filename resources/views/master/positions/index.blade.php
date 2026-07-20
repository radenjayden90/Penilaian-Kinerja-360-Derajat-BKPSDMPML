@extends('layouts.app')
@section('header', 'Master Data Jabatan')
@section('content')
<x-page-header title="Daftar Jabatan" subtitle="Kelola seluruh data jabatan dalam instansi.">
    <x-slot:actions>
        <a href="{{ route('master.positions.create') }}"><x-button variant="primary">Tambah Jabatan</x-button></a>
    </x-slot>
</x-page-header>
@if(session('success'))
    <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative">
        <span class="block sm:inline">{{ session('success') }}</span>
    </div>
@endif
<x-card>
    <div class="p-4 sm:p-6" x-data="{ submitForm() { $refs.searchForm.submit(); } }">
        <form x-ref="searchForm" method="GET" action="{{ route('master.positions.index') }}" class="flex flex-col sm:flex-row gap-4 mb-6">
            <div class="flex-1">
                <x-form.input name="search" value="{{ request('search') }}" @input.debounce.500ms="submitForm()" placeholder="Cari nama jabatan..." />
            </div>
            <div class="w-full sm:w-48">
                <x-form.select name="department_id" @change="submitForm()">
                    <option value="">Semua Bidang</option>
                    @foreach($departments as $dept)
                        <option value="{{ $dept->id }}" {{ request('department_id') == $dept->id ? 'selected' : '' }}>{{ $dept->name }}</option>
                    @endforeach
                </x-form.select>
            </div>
            <div class="w-full sm:w-48">
                <x-form.select name="status" @change="submitForm()">
                    <option value="">Semua Status</option>
                    <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>Aktif</option>
                    <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>Nonaktif</option>
                </x-form.select>
            </div>
        </form>
        <x-table.index>
            <x-slot:header>
                <x-table.th>Jabatan</x-table.th>
                <x-table.th>Bidang</x-table.th>
                <x-table.th>Level</x-table.th>
                <x-table.th>Status</x-table.th>
                <x-table.th class="text-right">Aksi</x-table.th>
            </x-slot>
            @forelse($positions as $position)
                <tr>
                    <x-table.td><div class="font-medium text-gray-900">{{ $position->name }}</div></x-table.td>
                    <x-table.td>{{ $position->department->name ?? '-' }}</x-table.td>
                    <x-table.td>{{ $position->level }}</x-table.td>
                    <x-table.td>
                        @if($position->is_active)<x-badge color="green">Aktif</x-badge>@else<x-badge color="red">Nonaktif</x-badge>@endif
                    </x-table.td>
                    <x-table.td class="text-right text-sm font-medium">
                        <a href="{{ route('master.positions.edit', $position) }}" class="text-indigo-600 hover:text-indigo-900 mr-3">Edit</a>
                        <form action="{{ route('master.positions.destroy', $position) }}" method="POST" class="inline-block" x-data @submit.prevent="if (confirm('Apakah Anda yakin ingin menghapus data ini?')) $el.submit()">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-900">Hapus</button>
                        </form>
                    </x-table.td>
                </tr>
            @empty
                <x-table.empty colspan="5" message="Tidak ada data jabatan." />
            @endforelse
        </x-table.index>
        <div class="mt-4">{{ $positions->withQueryString()->links() }}</div>
    </div>
</x-card>
@endsection
