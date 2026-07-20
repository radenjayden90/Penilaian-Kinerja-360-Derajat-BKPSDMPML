@extends('layouts.app')
@section('header', 'Master Data Periode')
@section('content')
<x-page-header title="Daftar Periode" subtitle="Kelola periode penilaian kinerja.">
    <x-slot:actions><a href="{{ route('master.periods.create') }}"><x-button variant="primary">Tambah Periode</x-button></a></x-slot>
</x-page-header>
@if(session('success'))
    <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative">
        <span class="block sm:inline">{{ session('success') }}</span>
    </div>
@endif
<x-card>
    <div class="p-4 sm:p-6" x-data="{ submitForm() { $refs.searchForm.submit(); } }">
        <form x-ref="searchForm" method="GET" action="{{ route('master.periods.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
            <div>
                <x-form.input name="search" value="{{ request('search') }}" @input.debounce.500ms="submitForm()" placeholder="Cari nama periode..." />
            </div>
            <div>
                <x-form.select name="month" @change="submitForm()">
                    <option value="">Semua Bulan</option>
                    @for($i = 1; $i <= 12; $i++)
                        <option value="{{ $i }}" {{ request('month') == $i ? 'selected' : '' }}>{{ date('F', mktime(0, 0, 0, $i, 1)) }}</option>
                    @endfor
                </x-form.select>
            </div>
            <div>
                <x-form.select name="year" @change="submitForm()">
                    <option value="">Semua Tahun</option>
                    @for($i = date('Y') + 1; $i >= 2020; $i--)
                        <option value="{{ $i }}" {{ request('year') == $i ? 'selected' : '' }}>{{ $i }}</option>
                    @endfor
                </x-form.select>
            </div>
            <div>
                <x-form.select name="status" @change="submitForm()">
                    <option value="">Semua Status Data</option>
                    <option value="OPEN" {{ request('status') === 'OPEN' ? 'selected' : '' }}>OPEN</option>
                    <option value="CLOSED" {{ request('status') === 'CLOSED' ? 'selected' : '' }}>CLOSED</option>
                    <option value="ARCHIVED" {{ request('status') === 'ARCHIVED' ? 'selected' : '' }}>ARCHIVED</option>
                </x-form.select>
            </div>
        </form>
        <x-table.index>
            <x-slot:header>
                <x-table.th>Periode</x-table.th>
                <x-table.th>Bulan/Tahun</x-table.th>
                <x-table.th>Rentang Waktu</x-table.th>
                <x-table.th>Status & Aktif</x-table.th>
                <x-table.th class="text-right">Aksi</x-table.th>
            </x-slot>
            @forelse($periods as $period)
                <tr>
                    <x-table.td><div class="font-medium text-gray-900">{{ $period->name }}</div></x-table.td>
                    <x-table.td>{{ date('F', mktime(0, 0, 0, $period->month, 1)) }} {{ $period->year }}</x-table.td>
                    <x-table.td>{{ $period->start_date->format('d M Y') }} - {{ $period->end_date->format('d M Y') }}</x-table.td>
                    <x-table.td>
                        <div class="flex items-center space-x-2">
                            @if($period->status->value === 'OPEN')<x-badge color="green">OPEN</x-badge>
                            @elseif($period->status->value === 'CLOSED')<x-badge color="gray">CLOSED</x-badge>
                            @else<x-badge color="yellow">ARCHIVED</x-badge>@endif
                            
                            @if($period->is_active)<x-badge color="blue">Aktif</x-badge>@endif
                        </div>
                    </x-table.td>
                    <x-table.td class="text-right text-sm font-medium">
                        <a href="{{ route('master.periods.edit', $period) }}" class="text-indigo-600 hover:text-indigo-900 mr-3">Edit</a>
                        <form action="{{ route('master.periods.destroy', $period) }}" method="POST" class="inline-block" x-data @submit.prevent="if (confirm('Apakah Anda yakin ingin menghapus data ini?')) $el.submit()">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-900">Hapus</button>
                        </form>
                    </x-table.td>
                </tr>
            @empty
                <x-table.empty colspan="5" message="Tidak ada data periode." />
            @endforelse
        </x-table.index>
        <div class="mt-4">{{ $periods->withQueryString()->links() }}</div>
    </div>
</x-card>
@endsection
