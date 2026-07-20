@extends('layouts.app')

@section('header', 'Penilaian Saya')

@section('content')

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
    <x-card>
        <div class="p-6 text-center text-gray-500">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900">Periode Penilaian Ditutup</h3>
            <p class="mt-1 text-sm text-gray-500">Saat ini tidak ada periode penilaian yang berstatus OPEN. Silakan hubungi Administrator.</p>
        </div>
    </x-card>
@else

<div class="mb-6">
    <h2 class="text-lg font-medium text-gray-900">Periode Aktif: <span class="font-bold text-indigo-600">{{ $activePeriod->name }}</span></h2>
    <p class="text-sm text-gray-500">Silakan lakukan penilaian kinerja untuk Atasan, Rekan Kerja, dan Bawahan Anda (jika ada).</p>
</div>

<div class="space-y-6">

    {{-- ATASAN --}}
    <x-card>
        <div class="p-6">
            <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4 border-b pb-2">Penilaian Atasan (SUPERIOR)</h3>
            
            @if($superior)
                <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg border">
                    <div>
                        <div class="font-medium text-gray-900">{{ $superior->name }}</div>
                        <div class="text-sm text-gray-500">{{ $superior->position->name ?? '-' }} | {{ $superior->department->name ?? '-' }}</div>
                        <div class="mt-1">
                            @if($superior->assessment_status)
                                <x-badge color="green">Selesai Dinilai</x-badge>
                                <span class="text-xs text-gray-500 ml-2">disubmit pada {{ $superior->assessment_status->submitted_at->format('d/m/Y H:i') }}</span>
                            @else
                                <x-badge color="yellow">Belum Dinilai</x-badge>
                            @endif
                        </div>
                    </div>
                    <div>
                        @if($superior->assessment_status)
                            <a href="{{ route('transaction.assessments.show', $superior->assessment_status->id) }}" class="text-indigo-600 hover:text-indigo-900 text-sm font-medium">Lihat Hasil</a>
                        @else
                            <a href="{{ route('transaction.assessments.create', ['target_id' => $superior->id, 'type' => 'SUPERIOR']) }}">
                                <x-button variant="primary" size="sm">Nilai Sekarang</x-button>
                            </a>
                        @endif
                    </div>
                </div>
            @else
                <p class="text-sm text-gray-500 italic">Anda tidak memiliki Atasan Langsung yang terdaftar di sistem.</p>
            @endif
        </div>
    </x-card>

    {{-- REKAN --}}
    <x-card>
        <div class="p-6">
            <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4 border-b pb-2">Penilaian Rekan Kerja (PEER)</h3>
            <p class="text-sm text-gray-500 mb-4">Pilih rekan kerja untuk dinilai. Setiap pegawai hanya dapat dinilai maksimal oleh 3 rekan.</p>
            
            @if($peers->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($peers as $peer)
                        <div class="p-4 bg-gray-50 rounded-lg border flex flex-col justify-between h-full">
                            <div class="mb-4">
                                <div class="font-medium text-gray-900 truncate" title="{{ $peer->name }}">{{ $peer->name }}</div>
                                <div class="text-xs text-gray-500 truncate" title="{{ $peer->position->name ?? '-' }}">{{ $peer->position->name ?? '-' }}</div>
                                <div class="mt-2">
                                    @if($peer->assessment_status)
                                        <x-badge color="green">Selesai</x-badge>
                                    @else
                                        <x-badge color="yellow">Belum</x-badge>
                                    @endif
                                </div>
                            </div>
                            <div>
                                @if($peer->assessment_status)
                                    <a href="{{ route('transaction.assessments.show', $peer->assessment_status->id) }}" class="text-indigo-600 hover:text-indigo-900 text-sm font-medium w-full text-center block">Lihat Hasil</a>
                                @else
                                    <a href="{{ route('transaction.assessments.create', ['target_id' => $peer->id, 'type' => 'PEER']) }}" class="block w-full">
                                        <x-button variant="primary" size="sm" class="w-full justify-center">Nilai</x-button>
                                    </a>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-sm text-gray-500 italic">Tidak ada rekan kerja yang memenuhi syarat untuk dinilai saat ini (mungkin karena sudah dinilai penuh oleh rekan lain).</p>
            @endif
        </div>
    </x-card>

    {{-- BAWAHAN --}}
    @if($subordinates->count() > 0)
    <x-card>
        <div class="p-6">
            <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4 border-b pb-2">Penilaian Bawahan (SUBORDINATE)</h3>
            
            <div class="space-y-4">
                @foreach($subordinates as $sub)
                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg border">
                        <div>
                            <div class="font-medium text-gray-900">{{ $sub->name }}</div>
                            <div class="text-sm text-gray-500">{{ $sub->position->name ?? '-' }} | {{ $sub->department->name ?? '-' }}</div>
                            <div class="mt-1">
                                @if($sub->assessment_status)
                                    <x-badge color="green">Selesai Dinilai</x-badge>
                                @else
                                    <x-badge color="yellow">Belum Dinilai</x-badge>
                                @endif
                            </div>
                        </div>
                        <div>
                            @if($sub->assessment_status)
                                <a href="{{ route('transaction.assessments.show', $sub->assessment_status->id) }}" class="text-indigo-600 hover:text-indigo-900 text-sm font-medium">Lihat Hasil</a>
                            @else
                                <a href="{{ route('transaction.assessments.create', ['target_id' => $sub->id, 'type' => 'SUBORDINATE']) }}">
                                    <x-button variant="primary" size="sm">Nilai</x-button>
                                </a>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </x-card>
    @endif

</div>

@endif
@endsection
