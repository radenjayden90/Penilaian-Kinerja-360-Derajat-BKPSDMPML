<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') — 360 Kinerja ASN Pemalang</title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <!-- Custom Government App CSS -->
    <link rel="stylesheet" href="{{ asset('css/app-custom.css') }}">

    @stack('styles')
</head>
<body>
    <div class="app-wrapper">
        <!-- Sidebar -->
        @if (Auth::user() && Auth::user()->isKepalaBkpsdm())
            @include('layouts.partials.sidebar-kepala')
        @elseif (Auth::user() && Auth::user()->isAdmin())
            @include('layouts.partials.sidebar-admin')
        @else
            @include('layouts.partials.sidebar-pegawai')
        @endif

        <!-- Main Workspace -->
        <div class="app-main">
            <!-- Navbar Header -->
            @include('layouts.partials.navbar')

            <!-- Main Content Area -->
            <main class="app-content">
                <div class="container-fluid p-0">
                    <!-- Page Header & Title -->
                    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-3 gap-2">
                        <div>
                            <h1 class="page-title">@yield('header', 'Dashboard')</h1>
                            @hasSection('subtitle')
                                <p class="text-muted small mb-0">@yield('subtitle')</p>
                            @endif
                        </div>
                        <div>
                            @yield('action_buttons')
                        </div>
                    </div>

                    <!-- Breadcrumbs -->
                    @include('layouts.partials.breadcrumb')

                    <!-- Flash Alerts -->
                    @include('layouts.partials.alerts')

                    <!-- Page Content -->
                    @yield('content')
                </div>
            </main>

            <!-- Footer -->
            @include('layouts.partials.footer')
        </div>
    </div>

    <!-- Bootstrap 5 Bundle JS (Includes Popper) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>

    <!-- Sidebar Toggle Script -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const toggleBtn = document.getElementById('sidebarToggleBtn');
            const body = document.body;

            // Load saved sidebar state
            if (localStorage.getItem('sidebar-collapsed') === 'true') {
                body.classList.add('sidebar-collapsed');
            }

            if (toggleBtn) {
                toggleBtn.addEventListener('click', function () {
                    if (window.innerWidth < 992) {
                        body.classList.toggle('sidebar-open');
                    } else {
                        body.classList.toggle('sidebar-collapsed');
                        localStorage.setItem('sidebar-collapsed', body.classList.contains('sidebar-collapsed'));
                    }
                });
            }

            // Close mobile sidebar on click outside
            document.addEventListener('click', function (e) {
                if (window.innerWidth < 992 && body.classList.contains('sidebar-open')) {
                    const sidebar = document.querySelector('.app-sidebar');
                    if (sidebar && !sidebar.contains(e.target) && !toggleBtn.contains(e.target)) {
                        body.classList.remove('sidebar-open');
                    }
                }
            });

            // Live Auto-Search as user types (case-insensitive debounced filtering)
            const searchInputs = document.querySelectorAll('input[name="search"]');
            searchInputs.forEach(function(input) {
                // Keep cursor focus position after submission
                if (input.value && document.activeElement !== input) {
                    const queryParam = new URLSearchParams(window.location.search).get('search');
                    if (queryParam) {
                        input.focus();
                        const len = input.value.length;
                        input.setSelectionRange(len, len);
                    }
                }

                let debounceTimer = null;
                input.addEventListener('input', function() {
                    clearTimeout(debounceTimer);
                    debounceTimer = setTimeout(function() {
                        if (input.form) {
                            input.form.submit();
                        }
                    }, 450);
                });
            });
        });
    </script>

    @stack('scripts')
</body>
</html>
