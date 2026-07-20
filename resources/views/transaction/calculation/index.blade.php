@extends('layouts.app')

@section('header', 'Perhitungan Nilai Akhir')

@section('content')
<x-page-header title="Perhitungan Nilai 360" subtitle="Manajemen proses kalkulasi hasil penilaian kinerja masing-masing pegawai.">
    <x-slot:actions>
        @if($activePeriod)
            <form action="{{ route('transaction.calculations.calculateAll') }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin memproses perhitungan masal untuk semua pegawai pada periode aktif ini?');">
                @csrf
                <x-button type="submit" variant="primary">Hitung Semua</x-button>
            </form>
        @endif
    </x-slot>
</x-page-header>

<x-card>
    <div class="p-4 sm:p-6" x-data="{ submitForm() { $refs.searchForm.submit(); } }">
        
        @if(session('success'))
            <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative">
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
        @endif
        @if(session('error'))
            <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative">
                <span class="block sm:inline">{{ session('error') }}</span>
            </div>
        @endif

        @if(!$activePeriod)
            <div class="mb-6 bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded relative">
                <strong>Perhatian:</strong> Saat ini tidak ada periode penilaian yang berstatus OPEN. Perhitungan hanya bisa dilakukan pada periode aktif.
            </div>
        @else
            <div class="mb-6 bg-indigo-50 border border-indigo-200 text-indigo-700 px-4 py-3 rounded">
                Menampilkan data pada Periode Aktif: <strong>{{ $activePeriod->name }}</strong>
            </div>
        @endif

        <form x-ref="searchForm" method="GET" action="{{ route('transaction.calculations.index') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
            <div>
                <x-form.input name="search" value="{{ request('search') }}" @input.debounce.500ms="submitForm()" placeholder="Cari NIP atau Nama..." />
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
                <x-form.select name="per_page" @change="submitForm()">
                    <option value="10" {{ request('per_page') == '10' ? 'selected' : '' }}>10 Per Halaman</option>
                    <option value="25" {{ request('per_page') == '25' ? 'selected' : '' }}>25 Per Halaman</option>
                    <option value="50" {{ request('per_page') == '50' ? 'selected' : '' }}>50 Per Halaman</option>
                </x-form.select>
            </div>
        </form>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pegawai</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Final Score</th>
                        <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Kategori</th>
                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($employees as $employee)
                        @php
                            $result = $employee->assessmentResult;
                        @endphp
                        <tr>
                            <td class="px-6 py-4">
                                <div class="font-medium text-gray-900">{{ $employee->name }}</div>
                                <div class="text-sm text-gray-500">{{ $employee->nip }} | {{ $employee->position->name ?? '-' }}</div>
                            </td>
                            <td class="px-6 py-4">
                                @if($result)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $result->status->badgeColor() }}">
                                        {{ $result->status->label() }}
                                    </span>
                                    @if($result->status === \App\Enums\CalculationStatus::PENDING)
                                        <div class="text-xs text-red-500 mt-1 max-w-xs truncate" title="{{ $result->pending_reason }}">
                                            {{ Str::limit($result->pending_reason, 40) }}
                                        </div>
                                    @endif
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                        Belum Dihitung
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-center font-bold text-gray-700">
                                {{ $result?->final_score ? number_format($result->final_score, 2) : '-' }}
                            </td>
                            <td class="px-6 py-4 text-center whitespace-nowrap">
                                @if($result?->category)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $result->category->badgeColor() }}">
                                        {{ $result->category->label() }}
                                    </span>
                                @else
                                    -
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex justify-end space-x-2">
                                    @if($activePeriod)
                                        <form action="{{ route('transaction.calculations.calculate', $employee->id) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="text-indigo-600 hover:text-indigo-900" title="Hitung / Hitung Ulang">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                                            </button>
                                        </form>
                                    @endif

                                    @if($result)
                                        <a href="{{ route('transaction.calculations.show', $employee->id) }}" class="text-blue-600 hover:text-blue-900" title="Lihat Detail">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-10 text-center text-gray-500">
                                Tidak ada data pegawai yang ditemukan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div class="mt-4">
            {{ $employees->withQueryString()->links() }}
        </div>
    </div>
</x-card>
@endsection
