<header class="app-navbar">
    <div class="d-flex align-items-center gap-3">
        <button type="button" class="btn-sidebar-toggle" id="sidebarToggleBtn" title="Toggle Sidebar">
            <i class="bi bi-list"></i>
        </button>
        <div class="d-none d-md-block">
            <span class="fw-semibold text-dark" style="font-size: 14px;">Sistem Penilaian Kinerja 360° ASN</span>
            <span class="text-muted ms-2 me-2">|</span>
            <small class="text-secondary">Kabupaten Pemalang</small>
        </div>
    </div>

    <div class="d-flex align-items-center gap-3">
        <div class="dropdown">
            <button class="user-profile-btn dropdown-toggle" type="button" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                <div class="avatar-circle">
                    {{ strtoupper(substr(Auth::user()->name ?? 'U', 0, 1)) }}
                </div>
                <div class="d-none d-sm-block text-start">
                    <div class="fw-semibold lh-1 text-dark" style="font-size: 14px;">{{ Auth::user()->name ?? 'Pengguna' }}</div>
                    <small class="text-muted" style="font-size: 12px;">
                        NIP. {{ Auth::user()->nip ?? '-' }}
                    </small>
                </div>
            </button>
            <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 mt-2" aria-labelledby="userDropdown" style="min-width: 220px;">
                <li class="px-3 py-2 border-bottom bg-light">
                    <div class="fw-semibold text-dark">{{ Auth::user()->name ?? 'Pengguna' }}</div>
                    <div class="text-muted small">Role: <span class="badge badge-role ms-1">{{ Auth::user()->role->name ?? 'Pegawai' }}</span></div>
                </li>
                <li>
                    <a class="dropdown-item py-2" href="{{ route('profile.edit') }}">
                        <i class="bi bi-person me-2 text-primary"></i> Profil Saya
                    </a>
                </li>
                <li><hr class="dropdown-divider my-1"></li>
                <li>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="dropdown-item py-2 text-danger">
                            <i class="bi bi-box-arrow-right me-2"></i> Logout
                        </button>
                    </form>
                </li>
            </ul>
        </div>
    </div>
</header>
