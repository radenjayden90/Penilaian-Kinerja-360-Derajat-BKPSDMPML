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
        @php
            $authEmployee = \App\Models\Employee::where('email', Auth::user()->email)->orWhere('nip', Auth::user()->nip)->first() ?? Auth::user();
            $assessmentCount = 0;
            $lastUpdated = null;
            if ($authEmployee && $authEmployee instanceof \App\Models\Employee) {
                $query = \App\Models\Assessment::where('employee_id', $authEmployee->id)
                    ->whereIn('status', ['SUBMITTED', 'COMPLETED']);
                
                $activePeriod = \App\Models\Period::where('is_active', true)->orWhere('status', 'OPEN')->first();
                if ($activePeriod) {
                    $query->where('period_id', $activePeriod->id);
                }
                
                $assessmentCount = $query->count();
                if ($assessmentCount > 0) {
                    $latest = $query->latest('updated_at')->first();
                    if ($latest) {
                        \Carbon\Carbon::setLocale('id');
                        $lastUpdated = $latest->updated_at->diffForHumans();
                    }
                }
            }
        @endphp

        <!-- Notifications -->
        <div class="dropdown">
            <button class="btn btn-link text-secondary p-0 position-relative" id="notificationDropdown" data-bs-toggle="dropdown" aria-expanded="false" style="text-decoration: none; border: none; background: transparent; padding: 4px 8px !important;">
                <i class="bi bi-bell fs-5 text-dark"></i>
                <span id="notificationBadge" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger d-none" style="font-size: 0.65rem; margin-left: -5px; margin-top: 5px;"></span>
            </button>
            <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 mt-2" aria-labelledby="notificationDropdown" id="notificationMenu" style="min-width: 280px; box-shadow: 0 4px 12px rgba(0,0,0,0.1) !important;">
                <li class="px-3 py-2 border-bottom bg-light">
                    <div class="fw-semibold text-dark">Notifikasi</div>
                </li>
                
                <li class="px-3 py-3 {{ $assessmentCount > 0 ? '' : 'd-none' }}" id="notificationHasData">
                    <div class="d-flex align-items-start gap-3">
                        <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                            <i class="bi bi-info-circle fs-5"></i>
                        </div>
                        <div>
                            <p class="mb-0 text-dark" style="font-size: 13px;">
                                <strong id="notificationTextCount">{{ $assessmentCount }} orang</strong> sudah memberikan penilaian kepada Anda.
                            </p>
                            @if($lastUpdated)
                                <small class="text-muted mt-1 d-block" style="font-size: 11px;" id="notificationTimeContainer">
                                    <i class="bi bi-clock me-1"></i> Terakhir: <span id="notificationTimeText">{{ $lastUpdated }}</span>
                                </small>
                            @else
                                <small class="text-muted mt-1 d-none" style="font-size: 11px;" id="notificationTimeContainer">
                                    <i class="bi bi-clock me-1"></i> Terakhir: <span id="notificationTimeText"></span>
                                </small>
                            @endif
                        </div>
                    </div>
                </li>
                
                <li class="px-3 py-4 text-center text-muted {{ $assessmentCount > 0 ? 'd-none' : '' }}" id="notificationEmpty">
                    <i class="bi bi-bell-slash fs-3 d-block mb-2 text-black-50"></i>
                    <small>Belum ada notifikasi.</small>
                </li>
            </ul>
        </div>

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
                @if(Auth::user()->role?->name !== 'ADMIN')
                <li>
                    <a wire:navigate class="dropdown-item py-2" href="{{ route('profile.edit') }}">
                        <i class="bi bi-person me-2 text-primary"></i> Profil Saya
                    </a>
                </li>
                @endif
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

<script>
    document.addEventListener('livewire:navigated', function() {
        const authUserId = "{{ Auth::id() }}";
        const storageKey = 'seenAssessmentCount_' + authUserId;
        let currentAssessmentCount = {{ $assessmentCount }};
        const badge = document.getElementById('notificationBadge');
        const hasData = document.getElementById('notificationHasData');
        const empty = document.getElementById('notificationEmpty');
        const textCount = document.getElementById('notificationTextCount');
        const notificationBtn = document.getElementById('notificationDropdown');

        function updateBadgeVisibility(count, lastUpdated) {
            const seenCount = parseInt(localStorage.getItem(storageKey)) || 0;
            
            // Show popup notification if count increased
            if (count > currentAssessmentCount && count > seenCount) {
                if (typeof Swal !== 'undefined') {
                    const Toast = Swal.mixin({
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 5000,
                        timerProgressBar: true,
                        didOpen: (toast) => {
                            toast.onmouseenter = Swal.stopTimer;
                            toast.onmouseleave = Swal.resumeTimer;
                        }
                    });

                    Toast.fire({
                        icon: 'info',
                        title: 'Ada Penilaian Baru',
                        text: 'Seseorang baru saja memberikan penilaian untuk Anda.'
                    });
                }
            }

            currentAssessmentCount = count;
            
            // Check if there are unseen notifications
            if (count > 0 && count > seenCount) {
                badge.textContent = count;
                badge.classList.remove('d-none');
            } else {
                badge.classList.add('d-none');
            }

            // Update dropdown content texts
            if (count > 0) {
                hasData.classList.remove('d-none');
                empty.classList.add('d-none');
                textCount.textContent = count + ' orang';
                
                if (lastUpdated) {
                    const timeContainer = document.getElementById('notificationTimeContainer');
                    const timeText = document.getElementById('notificationTimeText');
                    if (timeContainer && timeText) {
                        timeContainer.classList.remove('d-none');
                        timeContainer.classList.add('d-block');
                        timeText.textContent = lastUpdated;
                    }
                }
            } else {
                hasData.classList.add('d-none');
                empty.classList.remove('d-none');
            }
        }

        // Initialize visibility on page load
        updateBadgeVisibility(currentAssessmentCount, "{{ $lastUpdated ?? '' }}");

        // Hide badge when button is clicked (mark as seen)
        if (notificationBtn) {
            notificationBtn.addEventListener('click', function() {
                if (currentAssessmentCount > 0) {
                    localStorage.setItem(storageKey, currentAssessmentCount);
                    badge.classList.add('d-none');
                }
            });
        }

        // Real-time polling
        function fetchNotifications() {
            fetch('{{ route('notifications.count') }}', {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.count !== undefined) {
                    updateBadgeVisibility(data.count, data.last_updated);
                }
            })
            .catch(error => console.error('Error fetching notifications:', error));
        }
        
        // Polling interval (setiap 15 detik)
        setInterval(fetchNotifications, 15000);
    });
</script>
