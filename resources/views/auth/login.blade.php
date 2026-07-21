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
            background: linear-gradient(135deg, #1E3A5F 0%, #0F1D30 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Inter', sans-serif;
            position: relative;
            overflow: hidden;
        }
        
        /* Decorative Background Elements */
        .bg-shape {
            position: absolute;
            border-radius: 50%;
            filter: blur(80px);
            z-index: 0;
            animation: float 15s infinite ease-in-out alternate;
        }
        .shape-1 {
            width: 500px; height: 500px;
            background: rgba(59, 130, 246, 0.4); /* Blue */
            top: -150px; left: -150px;
        }
        .shape-2 {
            width: 600px; height: 600px;
            background: rgba(14, 165, 233, 0.3); /* Sky */
            bottom: -200px; right: -100px;
            animation-delay: -5s;
        }
        .shape-3 {
            width: 400px; height: 400px;
            background: rgba(16, 185, 129, 0.2); /* Emerald */
            bottom: 20%; left: 10%;
            animation-delay: -10s;
        }

        @keyframes float {
            0% { transform: translate(0, 0) scale(1); }
            100% { transform: translate(30px, 50px) scale(1.1); }
        }

        .login-card {
            position: relative;
            z-index: 10;
            width: 100%;
            max-width: 460px;
            border-radius: 20px;
            border: 1px solid rgba(255, 255, 255, 0.5);
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
            overflow: hidden;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            margin: 0 20px;
        }
        .login-header {
            background: transparent;
            color: #1E3A5F;
            padding: 2.5rem 2rem 1rem;
            text-align: center;
        }
        .btn-primary-gov {
            background: linear-gradient(to right, #1E3A5F, #2563EB);
            border: none;
            color: #FFFFFF;
            font-weight: 600;
            padding: 0.75rem 1.5rem;
            border-radius: 10px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 6px -1px rgba(37, 99, 235, 0.4);
        }
        .btn-primary-gov:hover {
            background: linear-gradient(to right, #142843, #1D4ED8);
            transform: translateY(-2px);
            box-shadow: 0 10px 15px -3px rgba(37, 99, 235, 0.5);
        }
        .input-group-text {
            border-top-left-radius: 10px;
            border-bottom-left-radius: 10px;
            border-right: none;
            background-color: #F8FAFC;
        }
        .form-control {
            border-top-right-radius: 10px;
            border-bottom-right-radius: 10px;
            border-left: none;
            background-color: #F8FAFC;
            padding: 0.65rem 1rem;
        }
        .form-control:focus {
            box-shadow: none;
            border-color: #dee2e6;
            background-color: #FFFFFF;
        }
        .form-control:focus + .input-group-text,
        .input-group:focus-within .input-group-text {
            background-color: #FFFFFF;
            border-color: #dee2e6;
        }
    </style>
</head>
<body>
    <!-- Decorative Background -->
    <div class="bg-shape shape-1"></div>
    <div class="bg-shape shape-2"></div>
    <div class="bg-shape shape-3"></div>

    <div class="login-card">
        <div class="login-header">
            <div class="mb-3">
                <div class="d-inline-flex align-items-center justify-content-center bg-white rounded-circle shadow-sm" style="width: 80px; height: 80px; padding: 10px;">
                    <img src="{{ asset('images/logo-pemalang.png') }}" alt="Logo Pemalang" class="img-fluid" style="max-height: 100%;">
                </div>
            </div>
            <h4 class="fw-bold mb-1 text-dark">360° Kinerja ASN</h4>
            <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-2 mt-1 rounded-pill fw-medium">BKPSDM Kabupaten Pemalang</span>
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
