@extends('layouts.app')

@section('header', 'Master Data Bidang')

@section('content')
<x-page-header title="Daftar Bidang" subtitle="Kelola seluruh data bidang dalam instansi.">
    <x-slot:actions>
        <a href="{{ route('master.departments.create') }}">
            <x-button variant="primary">
                <svg class="-ml-1 mr-2 h-5 w-5 inline-block" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Tambah Bidang
            </x-button>
        </a>
    </x-slot>
</x-page-header>

@if(session('success'))
    <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
        <span class="block sm:inline">{{ session('success') }}</span>
    </div>
@endif

<x-card>
    <div class="p-4 sm:p-6" x-data="{ submitForm() { $refs.searchForm.submit(); } }">
        <form x-ref="searchForm" method="GET" action="{{ route('master.departments.index') }}" class="flex flex-col sm:flex-row gap-4 mb-6">
            <div class="flex-1">
                <div class="relative rounded-md shadow-sm">
                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                        <svg class="h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path fill-rule="evenodd" d="M9 3.5a5.5 5.5 0 100 11 5.5 5.5 0 000-11zM2 9a7 7 0 1112.452 4.391l3.328 3.329a.75.75 0 11-1.06 1.06l-3.329-3.328A7 7 0 012 9z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <input type="text" name="search" value="{{ request('search') }}" @input.debounce.500ms="submitForm()" class="block w-full rounded-md border-0 py-1.5 pl-10 text-gray-900 ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6" placeholder="Cari nama atau kode...">
                </div>
            </div>
            
            <div class="w-full sm:w-48">
                <select name="status" @change="submitForm()" class="block w-full rounded-md border-0 py-1.5 text-gray-900 ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                    <option value="">Semua Status</option>
                    <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>Aktif</option>
                    <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>Nonaktif</option>
                </select>
            </div>
        </form>

        <x-table.index>
            <x-slot:header>
                <x-table.th>Kode</x-table.th>
                <x-table.th>Nama Bidang</x-table.th>
                <x-table.th>Status</x-table.th>
                <x-table.th class="text-right">Aksi</x-table.th>
            </x-slot>
            
            @forelse($departments as $dept)
                <tr>
                    <x-table.td>{{ $dept->code }}</x-table.td>
                    <x-table.td>
                        <div class="font-medium text-gray-900">{{ $dept->name }}</div>
                        <div class="text-gray-500">{{ Str::limit($dept->description, 50) }}</div>
                    </x-table.td>
                    <x-table.td>
                        @if($dept->is_active)
                            <x-badge color="green">Aktif</x-badge>
                        @else
                            <x-badge color="red">Nonaktif</x-badge>
                        @endif
                    </x-table.td>
                    <x-table.td class="text-right text-sm font-medium">
                        <a href="{{ route('master.departments.edit', $dept) }}" class="text-indigo-600 hover:text-indigo-900 mr-3">Edit</a>
                        <form action="{{ route('master.departments.destroy', $dept) }}" method="POST" class="inline-block" x-data @submit.prevent="if (confirm('Apakah Anda yakin ingin menghapus data ini?')) $el.submit()">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-900">Hapus</button>
                        </form>
                    </x-table.td>
                </tr>
            @empty
                <x-table.empty colspan="4" message="Tidak ada data bidang." />
            @endforelse
        </x-table.index>

        <div class="mt-4">
            {{ $departments->withQueryString()->links() }}
        </div>
    </div>
</x-card>
@endsection
