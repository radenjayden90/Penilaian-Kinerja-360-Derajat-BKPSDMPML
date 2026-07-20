<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', '360 Kinerja') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-gray-100">
    <div class="min-h-screen flex">
        <!-- Sidebar -->
        <aside class="w-64 bg-white shadow-md hidden md:block">
            <div class="p-4 text-center border-b">
                <h2 class="text-xl font-bold text-gray-800">360 Kinerja</h2>
                <p class="text-sm text-gray-500">BKPSDM Pemalang</p>
            </div>
            <nav class="mt-4 px-2 space-y-1" x-data="{ masterOpen: {{ request()->routeIs('master.*') ? 'true' : 'false' }} }">
                <a href="{{ route('dashboard') }}" class="flex items-center px-4 py-2 rounded-md {{ request()->routeIs('dashboard') ? 'bg-indigo-50 text-indigo-700 font-semibold' : 'text-gray-600 hover:bg-gray-50' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                    Dashboard
                </a>

                <div>
                    <button @click="masterOpen = !masterOpen" class="w-full flex items-center justify-between px-4 py-2 rounded-md {{ request()->routeIs('master.*') ? 'bg-indigo-50 text-indigo-700 font-semibold' : 'text-gray-600 hover:bg-gray-50' }}">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4"></path></svg>
                            Master Data
                        </div>
                        <svg :class="{'rotate-180': masterOpen}" class="w-4 h-4 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </button>
                    
                    <div x-show="masterOpen" x-collapse class="pl-11 pr-2 mt-1 space-y-1">
                        <a href="{{ route('master.employees.index') }}" class="block px-3 py-2 text-sm rounded-md {{ request()->routeIs('master.employees.*') ? 'text-indigo-700 bg-indigo-50 font-medium' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">Pegawai</a>
                        <a href="{{ route('master.departments.index') }}" class="block px-3 py-2 text-sm rounded-md {{ request()->routeIs('master.departments.*') ? 'text-indigo-700 bg-indigo-50 font-medium' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">Bidang</a>
                        <a href="{{ route('master.positions.index') }}" class="block px-3 py-2 text-sm rounded-md {{ request()->routeIs('master.positions.*') ? 'text-indigo-700 bg-indigo-50 font-medium' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">Jabatan</a>
                        <a href="{{ route('master.periods.index') }}" class="block px-3 py-2 text-sm rounded-md {{ request()->routeIs('master.periods.*') ? 'text-indigo-700 bg-indigo-50 font-medium' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">Periode</a>
                        <a href="{{ route('master.assessment-categories.index') }}" class="block px-3 py-2 text-sm rounded-md {{ request()->routeIs('master.assessment-categories.*') ? 'text-indigo-700 bg-indigo-50 font-medium' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">Aspek Penilaian</a>
                        <a href="{{ route('master.assessment-indicators.index') }}" class="block px-3 py-2 text-sm rounded-md {{ request()->routeIs('master.assessment-indicators.*') ? 'text-indigo-700 bg-indigo-50 font-medium' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">Indikator Penilaian</a>
                    </div>
                </div>

                <div x-data="{ transOpen: {{ request()->routeIs('transaction.*') ? 'true' : 'false' }} }">
                    <button @click="transOpen = !transOpen" class="w-full flex items-center justify-between px-4 py-2 rounded-md {{ request()->routeIs('transaction.*') ? 'bg-indigo-50 text-indigo-700 font-semibold' : 'text-gray-600 hover:bg-gray-50' }}">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                            Transaksi
                        </div>
                        <svg :class="{'rotate-180': transOpen}" class="w-4 h-4 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </button>
                    
                    <div x-show="transOpen" x-collapse class="pl-11 pr-2 mt-1 space-y-1">
                        <a href="{{ route('transaction.assessments.index') }}" class="block px-3 py-2 text-sm rounded-md {{ request()->routeIs('transaction.assessments.*') ? 'text-indigo-700 bg-indigo-50 font-medium' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">Penilaian Saya</a>
                        <a href="{{ route('transaction.monitoring.index') }}" class="block px-3 py-2 text-sm rounded-md {{ request()->routeIs('transaction.monitoring.*') ? 'text-indigo-700 bg-indigo-50 font-medium' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">Monitoring Admin</a>
                        <a href="{{ route('transaction.calculations.index') }}" class="block px-3 py-2 text-sm rounded-md {{ request()->routeIs('transaction.calculations.*') ? 'text-indigo-700 bg-indigo-50 font-medium' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">Perhitungan Nilai</a>
                    </div>
                </div>
            </nav>
        </aside>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col">
            <!-- Topbar -->
            <header class="bg-white shadow-sm flex items-center justify-between px-6 py-4">
                <h1 class="text-xl font-semibold text-gray-800">
                    @yield('header')
                </h1>
                <div class="flex items-center">
                    <span class="text-gray-700 mr-4">{{ Auth::user()->name ?? 'Guest' }}</span>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="text-sm text-red-600 hover:text-red-800">Log Out</button>
                    </form>
                </div>
            </header>

            <!-- Content -->
            <main class="flex-1 p-6">
                @yield('content')
            </main>

            <!-- Footer -->
            <footer class="bg-white p-4 border-t text-center text-sm text-gray-500">
                &copy; {{ date('Y') }} Sistem Penilaian Kinerja 360 Derajat ASN - BKPSDM Kabupaten Pemalang.
            </footer>
        </div>
    </div>
</body>
</html>
