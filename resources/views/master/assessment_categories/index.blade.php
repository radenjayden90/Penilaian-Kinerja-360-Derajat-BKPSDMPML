@extends('layouts.app')
@section('header', 'Master Aspek Penilaian')
@section('content')
<x-page-header title="Daftar Aspek Penilaian" subtitle="Kelola aspek atau kategori penilaian kinerja (BerAKHLAK dll).">
    <x-slot:actions><a href="{{ route('master.assessment-categories.create') }}"><x-button variant="primary">Tambah Aspek</x-button></a></x-slot>
</x-page-header>
@if(session('success'))
    <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative">
        <span class="block sm:inline">{{ session('success') }}</span>
    </div>
@endif
<x-card>
    <div class="p-4 sm:p-6" x-data="{ submitForm() { $refs.searchForm.submit(); } }">
        <form x-ref="searchForm" method="GET" action="{{ route('master.assessment-categories.index') }}" class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6">
            <div>
                <x-form.input name="search" value="{{ request('search') }}" @input.debounce.500ms="submitForm()" placeholder="Cari nama aspek..." />
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
                <x-table.th>Urutan</x-table.th>
                <x-table.th>Nama Aspek</x-table.th>
                <x-table.th>Status</x-table.th>
                <x-table.th class="text-right">Aksi</x-table.th>
            </x-slot>
            @forelse($categories as $category)
                <tr>
                    <x-table.td>{{ $category->display_order }}</x-table.td>
                    <x-table.td>
                        <div class="font-medium text-gray-900">{{ $category->name }}</div>
                        <div class="text-gray-500">{{ Str::limit($category->description, 50) }}</div>
                    </x-table.td>
                    <x-table.td>
                        @if($category->is_active)<x-badge color="green">Aktif</x-badge>@else<x-badge color="red">Nonaktif</x-badge>@endif
                    </x-table.td>
                    <x-table.td class="text-right text-sm font-medium">
                        <a href="{{ route('master.assessment-categories.edit', $category) }}" class="text-indigo-600 hover:text-indigo-900 mr-3">Edit</a>
                        <form action="{{ route('master.assessment-categories.destroy', $category) }}" method="POST" class="inline-block" x-data @submit.prevent="if (confirm('Apakah Anda yakin ingin menghapus data ini?')) $el.submit()">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-900">Hapus</button>
                        </form>
                    </x-table.td>
                </tr>
            @empty
                <x-table.empty colspan="4" message="Tidak ada data aspek penilaian." />
            @endforelse
        </x-table.index>
        <div class="mt-4">{{ $categories->withQueryString()->links() }}</div>
    </div>
</x-card>
@endsection
