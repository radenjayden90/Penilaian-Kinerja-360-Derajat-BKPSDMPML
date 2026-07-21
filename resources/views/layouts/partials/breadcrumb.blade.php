@hasSection('breadcrumb')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb app-breadcrumb">
            <li class="breadcrumb-item">
                <a href="{{ route('dashboard') }}"><i class="bi bi-house-door me-1"></i>Dashboard</a>
            </li>
            @yield('breadcrumb')
        </ol>
    </nav>
@endif
