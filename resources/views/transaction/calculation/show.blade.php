@extends('layouts.app')

@section('header', 'Detail Hasil Perhitungan')

@section('content')
<x-page-header title="Detail Perhitungan Nilai: {{ $employee->name }}" subtitle="Periode: {{ $activePeriod->name }}">
    <x-slot:actions>
        <a href="{{ route('transaction.calculations.index') }}" class="text-sm font-semibold text-indigo-600 hover:text-indigo-500">&larr; Kembali</a>
    </x-slot>
</x-page-header>

<div class="grid grid-cols-1 md:grid-cols-3 gap-6">
    <!-- Kolom Kiri: Info & Status -->
    <div class="md:col-span-1 space-y-6">
        <x-card>
            <div class="p-6">
                <div class="flex items-center justify-center mb-4">
                    <div class="h-20 w-20 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-700 text-2xl font-bold">
                        {{ substr($employee->name, 0, 2) }}
                    </div>
                </div>
                <h3 class="text-center text-lg font-bold text-gray-900">{{ $employee->name }}</h3>
                <p class="text-center text-sm text-gray-500 mb-6">{{ $employee->nip }}</p>

                <div class="space-y-3 text-sm">
                    <div class="flex justify-between border-b pb-2">
                        <span class="text-gray-500">Jabatan</span>
                        <span class="font-medium text-gray-900">{{ $employee->position->name ?? '-' }}</span>
                    </div>
                    <div class="flex justify-between border-b pb-2">
                        <span class="text-gray-500">Bidang</span>
                        <span class="font-medium text-gray-900">{{ $employee->department->name ?? '-' }}</span>
                    </div>
                    <div class="flex justify-between border-b pb-2">
                        <span class="text-gray-500">Status Hitung</span>
                        <span class="font-medium">
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ $result->status->badgeColor() }}">
                                {{ $result->status->label() }}
                            </span>
                        </span>
                    </div>
                    <div class="flex justify-between pb-2">
                        <span class="text-gray-500">Waktu Hitung</span>
                        <span class="font-medium text-gray-900">{{ $result->calculated_at ? $result->calculated_at->format('d/m/Y H:i') : '-' }}</span>
                    </div>
                </div>

                @if($result->status === \App\Enums\CalculationStatus::PENDING)
                    <div class="mt-4 p-3 bg-red-50 border border-red-200 rounded-md">
                        <h4 class="text-sm font-semibold text-red-800 mb-1">Alasan Pending:</h4>
                        <p class="text-xs text-red-700">{{ $result->pending_reason }}</p>
                    </div>
                @endif
            </div>
        </x-card>
    </div>

    <!-- Kolom Kanan: Rincian Nilai -->
    <div class="md:col-span-2 space-y-6">
        @if($result->status === \App\Enums\CalculationStatus::COMPLETE)
            <x-card>
                <div class="p-6 border-b border-gray-100 flex justify-between items-center bg-gray-50">
                    <div>
                        <h3 class="text-lg font-bold text-gray-900">Final Score</h3>
                        <p class="text-sm text-gray-500">Berdasarkan pembobotan 360 derajat</p>
                    </div>
                    <div class="text-right">
                        <div class="text-3xl font-black text-indigo-600">{{ number_format($result->final_score, 2) }}</div>
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold mt-1 {{ $result->category->badgeColor() }}">
                            {{ $result->category->label() }}
                        </span>
                    </div>
                </div>
                
                <div class="p-6">
                    <h4 class="text-md font-semibold text-gray-800 mb-4">Rincian Komponen Penilaian</h4>
                    
                    <div class="space-y-4">
                        <!-- Superior -->
                        <div class="bg-white border rounded-lg p-4 flex items-center justify-between">
                            <div class="flex items-center">
                                <div class="h-10 w-10 rounded-full bg-blue-100 flex items-center justify-center text-blue-600">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"></path></svg>
                                </div>
                                <div class="ml-4">
                                    <h5 class="text-sm font-bold text-gray-900">Nilai Atasan</h5>
                                    <p class="text-xs text-gray-500">Bobot: {{ $result->superior_weight * 100 }}%</p>
                                </div>
                            </div>
                            <div class="text-xl font-bold text-gray-900">{{ number_format($result->superior_average, 2) }}</div>
                        </div>

                        <!-- Peer -->
                        <div class="bg-white border rounded-lg p-4 flex items-center justify-between">
                            <div class="flex items-center">
                                <div class="h-10 w-10 rounded-full bg-green-100 flex items-center justify-center text-green-600">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                                </div>
                                <div class="ml-4">
                                    <h5 class="text-sm font-bold text-gray-900">Nilai Rekan (Peer)</h5>
                                    <p class="text-xs text-gray-500">Bobot: {{ $result->peer_weight * 100 }}%</p>
                                </div>
                            </div>
                            <div class="text-xl font-bold text-gray-900">{{ number_format($result->peer_average, 2) }}</div>
                        </div>

                        <!-- Subordinate -->
                        <div class="bg-white border rounded-lg p-4 flex items-center justify-between {{ $result->subordinate_weight == 0 ? 'opacity-50' : '' }}">
                            <div class="flex items-center">
                                <div class="h-10 w-10 rounded-full bg-purple-100 flex items-center justify-center text-purple-600">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"></path></svg>
                                </div>
                                <div class="ml-4">
                                    <h5 class="text-sm font-bold text-gray-900">Nilai Bawahan</h5>
                                    <p class="text-xs text-gray-500">Bobot: {{ $result->subordinate_weight * 100 }}%</p>
                                </div>
                            </div>
                            <div class="text-xl font-bold text-gray-900">
                                {{ $result->subordinate_weight > 0 ? number_format($result->subordinate_average, 2) : 'N/A' }}
                            </div>
                        </div>

                    </div>
                </div>
            </x-card>
        @else
            <x-card>
                <div class="p-10 text-center">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">Perhitungan Belum Selesai</h3>
                    <p class="mt-1 text-sm text-gray-500">Sistem tidak dapat menampilkan rincian nilai karena status perhitungan masih PENDING.</p>
                </div>
            </x-card>
        @endif
    </div>
</div>
@endsection
