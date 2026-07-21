<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login — Sistem Penilaian Kinerja 360° ASN Pemalang</title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="{{ asset('css/app-custom.css') }}">
    <style>
        body {
            background: #F8F9FA;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Inter', sans-serif;
        }
        .login-card {
            width: 100%;
            max-width: 440px;
            border-radius: 12px;
            border: 1px solid #E2E8F0;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.05);
            overflow: hidden;
            background: #FFFFFF;
        }
        .login-header {
            background-color: #1E3A5F;
            color: #FFFFFF;
            padding: 2rem 1.5rem;
            text-align: center;
        }
        .btn-primary-gov {
            background-color: #1E3A5F;
            border-color: #1E3A5F;
            color: #FFFFFF;
            font-weight: 600;
            padding: 0.65rem 1rem;
            border-radius: 6px;
        }
        .btn-primary-gov:hover {
            background-color: #142843;
            border-color: #142843;
            color: #FFFFFF;
        }
    </style>
</head>
<body>
    <div class="login-card">
        <div class="login-header">
            <div class="mb-2">
                <i class="bi bi-shield-check text-warning fs-1"></i>
            </div>
            <h4 class="fw-bold mb-1">360° Kinerja ASN</h4>
            <small class="text-white-50">BKPSDM Kabupaten Pemalang</small>
        </div>

        <div class="card-body p-4">
            <!-- Flash Status Alert -->
            @if (session('status'))
                <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-3" role="alert">
                    {{ session('status') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if ($errors->any())
                <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm mb-3" role="alert">
                    <ul class="mb-0 ps-3">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}">
                @csrf

                <!-- NIP or Email Address -->
                <div class="mb-3">
                    <label for="login" class="form-label fw-semibold">NIP / Email <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text bg-light"><i class="bi bi-person text-muted"></i></span>
                        <input type="text" id="login" name="login" class="form-control bg-light @error('login') is-invalid @enderror" value="{{ old('login', old('email')) }}" placeholder="Masukkan NIP atau Email" required autofocus>
                    </div>
                </div>

                <!-- Password -->
                <div class="mb-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <label for="password" class="form-label fw-semibold mb-0">Password <span class="text-danger">*</span></label>
                    </div>
                    <div class="input-group mt-1">
                        <span class="input-group-text bg-light"><i class="bi bi-lock text-muted"></i></span>
                        <input type="password" id="password" name="password" class="form-control bg-light @error('password') is-invalid @enderror" placeholder="Masukkan Password (Default: NIP)" required>
                    </div>
                    <div class="form-text text-muted" style="font-size: 12px;">
                        <i class="bi bi-info-circle me-1"></i>Password default pegawai adalah <strong>NIP masing-masing</strong>.
                    </div>
                </div>

                <!-- Remember Me -->
                <div class="form-check mb-4">
                    <input type="checkbox" class="form-check-input" id="remember_me" name="remember">
                    <label class="form-check-label text-secondary small" for="remember_me">Ingat Sesi Saya</label>
                </div>

                <!-- Submit Button -->
                <button type="submit" class="btn btn-primary-gov w-100">
                    <i class="bi bi-box-arrow-in-right me-2"></i>Masuk Aplikasi
                </button>
            </form>
        </div>

        <div class="card-footer bg-light p-3 text-center border-top">
            <small class="text-muted" style="font-size: 12px;">
                &copy; {{ date('Y') }} BKPSDM Pemalang — Sistem Penilaian Kinerja 360°
            </small>
        </div>
    </div>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
