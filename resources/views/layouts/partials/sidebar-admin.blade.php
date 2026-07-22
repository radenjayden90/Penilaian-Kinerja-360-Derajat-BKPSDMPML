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
        <div class="nav-label">Utama</div>
        <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" title="Dashboard">
            <i class="bi bi-speedometer2 nav-main-icon"></i>
            <span class="nav-link-text">Dashboard</span>
        </a>

        <div class="nav-label">Master Data</div>
        <a href="#masterSubmenu" class="nav-link nav-link-dropdown {{ request()->routeIs('master.employees.*') || request()->routeIs('master.positions.*') || request()->routeIs('master.departments.*') ? 'active' : '' }}" data-bs-toggle="collapse" role="button" aria-expanded="{{ request()->routeIs('master.employees.*') || request()->routeIs('master.positions.*') || request()->routeIs('master.departments.*') ? 'true' : 'false' }}" title="Master Data">
            <i class="bi bi-database nav-main-icon"></i>
            <span class="nav-link-text">Master Data</span>
            <i class="bi bi-chevron-down dropdown-arrow small ms-auto"></i>
        </a>
        <div class="collapse {{ request()->routeIs('master.employees.*') || request()->routeIs('master.positions.*') || request()->routeIs('master.departments.*') ? 'show' : '' }}" id="masterSubmenu">
            <ul class="submenu">
                <li>
                    <a href="{{ route('master.employees.index') }}" class="nav-link {{ request()->routeIs('master.employees.*') ? 'active' : '' }}" title="Pegawai">
                        <i class="bi bi-people"></i>
                        <span class="nav-link-text">Pegawai</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('master.positions.index') }}" class="nav-link {{ request()->routeIs('master.positions.*') ? 'active' : '' }}" title="Jabatan">
                        <i class="bi bi-person-badge"></i>
                        <span class="nav-link-text">Jabatan</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('master.departments.index') }}" class="nav-link {{ request()->routeIs('master.departments.*') ? 'active' : '' }}" title="Unit Kerja">
                        <i class="bi bi-building"></i>
                        <span class="nav-link-text">Unit Kerja</span>
                    </a>
                </li>
            </ul>
        </div>

        <div class="nav-label">Penilaian</div>
        <a href="#penilaianSubmenu" class="nav-link nav-link-dropdown {{ request()->routeIs('master.periods.*') || request()->routeIs('master.assessment-indicators.*') || request()->routeIs('transaction.calculations.*') ? 'active' : '' }}" data-bs-toggle="collapse" role="button" aria-expanded="{{ request()->routeIs('master.periods.*') || request()->routeIs('master.assessment-indicators.*') || request()->routeIs('transaction.calculations.*') ? 'true' : 'false' }}" title="Penilaian">
            <i class="bi bi-clipboard-check nav-main-icon"></i>
            <span class="nav-link-text">Penilaian</span>
            <i class="bi bi-chevron-down dropdown-arrow small ms-auto"></i>
        </a>
        <div class="collapse {{ request()->routeIs('master.periods.*') || request()->routeIs('master.assessment-indicators.*') || request()->routeIs('transaction.calculations.*') ? 'show' : '' }}" id="penilaianSubmenu">
            <ul class="submenu">
                <li>
                    <a href="{{ route('master.periods.index') }}" class="nav-link {{ request()->routeIs('master.periods.*') ? 'active' : '' }}" title="Periode Penilaian">
                        <i class="bi bi-calendar-range"></i>
                        <span class="nav-link-text">Periode Penilaian</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('master.assessment-indicators.index') }}" class="nav-link {{ request()->routeIs('master.assessment-indicators.*') ? 'active' : '' }}" title="Pertanyaan">
                        <i class="bi bi-question-circle"></i>
                        <span class="nav-link-text">Pertanyaan</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('transaction.calculations.index') }}" class="nav-link {{ request()->routeIs('transaction.calculations.*') ? 'active' : '' }}" title="Hasil Penilaian">
                        <i class="bi bi-bar-chart-line"></i>
                        <span class="nav-link-text">Hasil Penilaian</span>
                    </a>
                </li>

            </ul>
        </div>

        <div class="nav-label">Laporan & Pengaturan</div>
        <a href="{{ route('report.index') }}" class="nav-link {{ request()->routeIs('report.*') ? 'active' : '' }}" title="Laporan">
            <i class="bi bi-file-earmark-text"></i>
            <span class="nav-link-text">Laporan</span>
        </a>
        <a href="{{ route('profile.edit') }}" class="nav-link {{ request()->routeIs('profile.*') ? 'active' : '' }}" title="Profil">
            <i class="bi bi-person-gear"></i>
            <span class="nav-link-text">Profil</span>
        </a>
    </nav>

    <div class="sidebar-footer">
        <div class="sidebar-version-info">
            Versi Sistem v1.0.0
        </div>
        <form method="POST" action="{{ route('logout') }}" id="logout-form-admin">
            @csrf
            <button type="submit" class="btn btn-logout" title="Keluar Aplikasi">
                <i class="bi bi-box-arrow-right"></i>
                <span class="nav-link-text">Logout</span>
            </button>
        </form>
    </div>
</aside>
