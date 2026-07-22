<aside class="app-sidebar">
    <div class="sidebar-header">
        <a href="{{ route('dashboard') }}" class="sidebar-brand">
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
        <div class="nav-label">Menu Pegawai</div>
        <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
            <i class="bi bi-speedometer2"></i>
            <span class="nav-link-text">Dashboard</span>
        </a>

        <a href="{{ route('transaction.assessments.index') }}" class="nav-link {{ request()->routeIs('transaction.assessments.*') ? 'active' : '' }}">
            <i class="bi bi-pencil-square"></i>
            <span class="nav-link-text">Penilaian Saya</span>
        </a>

        @php
            $posName = strtolower(Auth::user()->position?->name ?? '');
            $isKepalaBkpsdm = (Auth::user()->position?->level == '1' || str_contains($posName, 'kepala bkpsdm'));
        @endphp
        @if(!$isKepalaBkpsdm)
        <a href="{{ route('assessment.index') }}" class="nav-link {{ request()->routeIs('assessment.*') ? 'active' : '' }}">
            <i class="bi bi-clock-history"></i>
            <span class="nav-link-text">Riwayat Penilaian</span>
        </a>
        @endif

        <div class="nav-label">Pengaturan</div>
        <a href="{{ route('profile.edit') }}" class="nav-link {{ request()->routeIs('profile.*') ? 'active' : '' }}">
            <i class="bi bi-person-gear"></i>
            <span class="nav-link-text">Profil</span>
        </a>
    </nav>

    <div class="sidebar-footer">
        <div class="sidebar-version-info">
            Versi Sistem v1.0.0
        </div>
        <form method="POST" action="{{ route('logout') }}" id="logout-form-pegawai">
            @csrf
            <button type="submit" class="btn btn-logout" title="Keluar Aplikasi">
                <i class="bi bi-box-arrow-right"></i>
                <span class="nav-link-text">Logout</span>
            </button>
        </form>
    </div>
</aside>
