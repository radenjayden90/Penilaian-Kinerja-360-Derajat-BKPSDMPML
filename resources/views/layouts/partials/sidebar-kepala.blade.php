<aside class="app-sidebar">
    <div class="sidebar-header">
        <a href="{{ route('dashboard') }}" class="sidebar-brand" wire:navigate>
            <div class="sidebar-logo-box">
                <img src="{{ asset('images/logo-pemalang.png') }}" alt="Logo Pemalang" class="sidebar-logo-img">
            </div>
            <div class="sidebar-brand-text">
                <h1 class="sidebar-brand-title">360° Kinerja</h1>
                <span class="sidebar-brand-subtitle">BKPSDM Kabupaten Pemalang</span>
            </div>
        </a>
    </div>

    <nav class="sidebar-menu">
        <div class="nav-label">Utama</div>
        <a wire:navigate href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" title="Beranda" wire:navigate>
            <i class="bi bi-house-door nav-main-icon"></i>
            <span class="nav-link-text">Beranda</span>
        </a>

        <div class="nav-label">Evaluasi Kinerja</div>
        <a wire:navigate href="{{ route('transaction.assessments.index') }}" class="nav-link {{ request()->routeIs('transaction.assessments.*') ? 'active' : '' }}" title="Penilaian Kepala Bidang" wire:navigate>
            <i class="bi bi-pencil-square nav-main-icon"></i>
            <span class="nav-link-text">Penilaian Kepala Bidang</span>
        </a>

        <div class="nav-label">Laporan Eksekutif</div>
        @php
            $isReportActive = request()->routeIs('report.*');
            $currentTab = request()->input('tab', 'department');
        @endphp

        <a wire:navigate href="{{ route('report.index', ['tab' => 'department']) }}" class="nav-link {{ $isReportActive && $currentTab === 'department' ? 'active' : '' }}" title="Laporan Per Bidang" wire:navigate>
            <i class="bi bi-diagram-3 nav-main-icon"></i>
            <span class="nav-link-text">Laporan Per Bidang</span>
        </a>
        <a wire:navigate href="{{ route('report.index', ['tab' => 'employee']) }}" class="nav-link {{ $isReportActive && $currentTab === 'employee' ? 'active' : '' }}" title="Laporan Individu Pegawai" wire:navigate>
            <i class="bi bi-person-lines-fill nav-main-icon"></i>
            <span class="nav-link-text">Laporan Individu Pegawai</span>
        </a>
        <a wire:navigate href="{{ route('report.index', ['tab' => 'analytics']) }}" class="nav-link {{ $isReportActive && $currentTab === 'analytics' ? 'active' : '' }}" title="Statistik & Tren Kinerja" wire:navigate>
            <i class="bi bi-graph-up-arrow nav-main-icon"></i>
            <span class="nav-link-text">Statistik & Tren Kinerja</span>
        </a>

        <div class="nav-label">Akun</div>
        <a wire:navigate href="{{ route('profile.edit') }}" class="nav-link {{ request()->routeIs('profile.*') ? 'active' : '' }}" title="Profil" wire:navigate>
            <i class="bi bi-person-gear"></i>
            <span class="nav-link-text">Profil</span>
        </a>
    </nav>

    <div class="sidebar-footer">
        <div class="sidebar-version-info">
            Versi Sistem v1.0.0
        </div>
        <form method="POST" action="{{ route('logout') }}" id="logout-form-kepala">
            @csrf
            <button type="submit" class="btn btn-logout" title="Keluar Aplikasi">
                <i class="bi bi-box-arrow-right"></i>
                <span class="nav-link-text">Logout</span>
            </button>
        </form>
    </div>
</aside>
