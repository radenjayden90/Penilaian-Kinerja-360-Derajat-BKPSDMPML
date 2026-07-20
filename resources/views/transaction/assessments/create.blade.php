@extends('layouts.app')

@section('header', 'Form Penilaian')

@section('content')
<x-page-header title="Form Penilaian ({{ $type }})" subtitle="Periode: {{ $activePeriod->name }} (Batas Akhir: {{ $activePeriod->end_date->format('d/m/Y') }})">
    <x-slot:actions>
        <a href="{{ route('transaction.assessments.index') }}" class="text-sm font-semibold text-indigo-600 hover:text-indigo-500">&larr; Kembali</a>
    </x-slot>
</x-page-header>

<div class="mb-6 bg-white rounded-lg shadow p-4 sm:p-6 border-l-4 border-indigo-500 flex flex-col md:flex-row md:items-center md:justify-between">
    <div>
        <h3 class="text-lg font-bold text-gray-900">{{ $target->name }}</h3>
        <p class="text-sm text-gray-500">{{ $target->nip }} | {{ $target->position->name ?? '-' }} | {{ $target->department->name ?? '-' }}</p>
    </div>
    <div class="mt-4 md:mt-0">
        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-indigo-100 text-indigo-800">
            Penilaian: {{ $type }}
        </span>
    </div>
</div>

@if($errors->any())
    <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative">
        <strong>Terjadi Kesalahan:</strong>
        <ul class="list-disc pl-5 mt-1 text-sm">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form action="{{ route('transaction.assessments.store') }}" method="POST" id="assessmentForm">
    @csrf
    <input type="hidden" name="target_id" value="{{ $target->id }}">
    <input type="hidden" name="type" value="{{ $type }}">

    <div class="space-y-8">
        @foreach($categories as $category)
            <x-card>
                <div class="bg-gray-50 px-6 py-4 border-b">
                    <h4 class="text-lg font-bold text-gray-900">{{ $category->name }}</h4>
                    @if($category->description)
                        <p class="text-sm text-gray-600 mt-1">{{ $category->description }}</p>
                    @endif
                </div>
                
                <div class="p-6 space-y-6">
                    @foreach($category->indicators as $indicator)
                        <div class="border-b pb-6 last:border-b-0 last:pb-0">
                            <div class="mb-3">
                                <label class="block text-sm font-medium text-gray-900">{{ $loop->iteration }}. {{ $indicator->name }}</label>
                                @if($indicator->description)
                                    <p class="text-xs text-gray-500 mt-1">{{ $indicator->description }}</p>
                                @endif
                            </div>
                            
                            <div class="mt-2">
                                <div class="flex flex-wrap gap-2">
                                    @for($i = 1; $i <= 10; $i++)
                                        <label class="cursor-pointer">
                                            <input type="radio" name="scores[{{ $indicator->id }}][score]" value="{{ $i }}" class="peer sr-only" required {{ old("scores.{$indicator->id}.score") == $i ? 'checked' : '' }}>
                                            <div class="w-10 h-10 rounded-md border flex items-center justify-center font-medium text-gray-700 bg-white peer-checked:bg-indigo-600 peer-checked:text-white peer-checked:border-indigo-600 hover:bg-gray-50 transition-colors">
                                                {{ $i }}
                                            </div>
                                        </label>
                                    @endfor
                                </div>
                                @error("scores.{$indicator->id}.score")
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <div class="mt-3">
                                <x-form.textarea name="scores[{{ $indicator->id }}][comment]" placeholder="Komentar (Opsional)" rows="2">{{ old("scores.{$indicator->id}.comment") }}</x-form.textarea>
                            </div>
                        </div>
                    @endforeach
                </div>
            </x-card>
        @endforeach

        <x-card>
            <div class="p-6">
                <h4 class="text-lg font-bold text-gray-900 mb-4">Catatan Keseluruhan (Opsional)</h4>
                <x-form.textarea name="general_notes" rows="4" placeholder="Berikan catatan keseluruhan atas kinerja pegawai yang dinilai...">{{ old('general_notes') }}</x-form.textarea>
            </div>
        </x-card>
    </div>

    <div class="mt-8 mb-12 bg-white p-4 shadow rounded-lg flex items-center justify-between border-t-4 border-indigo-500 sticky bottom-4 z-10">
        <p class="text-sm text-gray-600">Pastikan semua indikator telah dinilai sebelum menyimpan. Data yang sudah disubmit tidak dapat diubah lagi.</p>
        <x-button type="submit" variant="primary" onclick="return confirm('Apakah Anda yakin ingin submit penilaian ini? Data tidak dapat diubah setelah disubmit.')">Submit Penilaian</x-button>
    </div>
</form>

@endsection
