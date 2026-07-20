@extends('layouts.app')

@section('header', 'Monitoring Penilaian')

@section('content')
<x-page-header title="Monitoring Penilaian 360" subtitle="Pantau kemajuan penilaian kinerja masing-masing pegawai di instansi.">
</x-page-header>

<x-card>
    <div class="p-4 sm:p-6" x-data="{ submitForm() { $refs.searchForm.submit(); } }">
        
        @if(!$activePeriod)
            <div class="mb-6 bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded relative">
                <strong>Perhatian:</strong> Saat ini tidak ada periode penilaian yang berstatus OPEN.
            </div>
        @else
            <div class="mb-6 bg-indigo-50 border border-indigo-200 text-indigo-700 px-4 py-3 rounded">
                Menampilkan data pada Periode Aktif: <strong>{{ $activePeriod->name }}</strong>
            </div>
        @endif

        <form x-ref="searchForm" method="GET" action="{{ route('transaction.monitoring.index') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
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
                    <option value="100" {{ request('per_page') == '100' ? 'selected' : '' }}>100 Per Halaman</option>
                </x-form.select>
            </div>
        </form>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pegawai</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jabatan / Bidang</th>
                        <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Nilai Atasan</th>
                        <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Nilai Rekan</th>
                        <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Nilai Bawahan</th>
                        <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Total Evaluasi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($employees as $employee)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="font-medium text-gray-900">{{ $employee->name }}</div>
                                <div class="text-sm text-gray-500">{{ $employee->nip }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $employee->position->name ?? '-' }}</div>
                                <div class="text-xs text-gray-500">{{ $employee->department->name ?? '-' }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                @if(str_contains($employee->monitoring_superior, 'Sudah'))
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">{{ $employee->monitoring_superior }}</span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">{{ $employee->monitoring_superior }}</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                @if(str_contains($employee->monitoring_peer, 'Sudah'))
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">{{ $employee->monitoring_peer }}</span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">{{ $employee->monitoring_peer }}</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                @if(str_contains($employee->monitoring_subordinate, 'Sudah'))
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">{{ $employee->monitoring_subordinate }}</span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">{{ $employee->monitoring_subordinate }}</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center font-bold text-gray-700">
                                {{ $employee->total_assessed }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-10 text-center text-gray-500">
                                Tidak ada data yang ditemukan.
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
