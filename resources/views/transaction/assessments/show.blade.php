@extends('layouts.app')

@section('header', 'Detail Penilaian')

@section('content')
<x-page-header title="Detail Penilaian ({{ $assessment->assessment_type }})" subtitle="Periode: {{ $assessment->period->name }}">
    <x-slot:actions>
        <a href="{{ route('transaction.assessments.index') }}" class="text-sm font-semibold text-indigo-600 hover:text-indigo-500">&larr; Kembali</a>
    </x-slot>
</x-page-header>

<div class="mb-6 bg-white rounded-lg shadow p-4 sm:p-6 border-l-4 border-green-500 flex flex-col md:flex-row md:items-center md:justify-between">
    <div>
        <h3 class="text-lg font-bold text-gray-900">Target: {{ $assessment->employee->name }}</h3>
        <p class="text-sm text-gray-500">{{ $assessment->employee->nip }} | {{ $assessment->employee->position->name ?? '-' }} | {{ $assessment->employee->department->name ?? '-' }}</p>
    </div>
    <div class="mt-4 md:mt-0 text-right">
        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
            Disubmit: {{ $assessment->submitted_at->format('d/m/Y H:i') }}
        </span>
        <div class="text-xs text-gray-500 mt-1">Oleh: {{ $assessment->assessor->name }}</div>
    </div>
</div>

<div class="space-y-8">
    @foreach($groupedScores as $categoryName => $scores)
        <x-card>
            <div class="bg-gray-50 px-6 py-4 border-b">
                <h4 class="text-lg font-bold text-gray-900">{{ $categoryName }}</h4>
            </div>
            
            <div class="p-6 space-y-4">
                @foreach($scores as $score)
                    <div class="border-b pb-4 last:border-b-0 last:pb-0">
                        <div class="flex justify-between items-start">
                            <div class="w-3/4">
                                <label class="block text-sm font-medium text-gray-900">{{ $loop->iteration }}. {{ $score->indicator->name }}</label>
                                @if($score->comment)
                                    <div class="mt-2 bg-gray-50 rounded p-3 text-sm text-gray-700 italic border-l-2 border-gray-300">
                                        "{{ $score->comment }}"
                                    </div>
                                @endif
                            </div>
                            <div class="w-1/4 text-right">
                                <span class="inline-flex items-center justify-center h-10 w-10 rounded-full bg-indigo-100 text-indigo-700 font-bold text-lg">
                                    {{ $score->score }}
                                </span>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </x-card>
    @endforeach

    @if($assessment->notes)
    <x-card>
        <div class="p-6">
            <h4 class="text-lg font-bold text-gray-900 mb-2">Catatan Keseluruhan</h4>
            <div class="bg-yellow-50 rounded p-4 text-sm text-gray-800 border border-yellow-200">
                {!! nl2br(e($assessment->notes)) !!}
            </div>
        </div>
    </x-card>
    @endif
</div>
@endsection
