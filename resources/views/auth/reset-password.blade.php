<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="Reset Password - Sistem Penilaian Kinerja ASN 360 Derajat BKPSDM Kabupaten Pemalang.">
    <title>Reset Password — 360° Kinerja ASN BKPSDM Pemalang</title>

    <!-- Favicon Logo Pemalang -->
    <link rel="icon" type="image/png" href="{{ asset('images/pemalang-shield.png') }}">
    <link rel="shortcut icon" href="{{ asset('images/pemalang-shield.png') }}">

    <!-- Google Fonts: Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }

        html, body {
            width: 100%;
            height: 100vh;
            max-height: 100vh;
            font-family: 'Plus Jakarta Sans', sans-serif;
            margin: 0;
            padding: 0;
            overflow: hidden !important;
            color: #1e293b;
            background-color: #0284c7;
        }

        .page-wrapper {
            position: fixed;
            top: 0; left: 0; width: 100vw; height: 100vh;
            display: flex;
            overflow: hidden !important;
        }

        .layer-sky {
            position: absolute;
            top: 0; left: 0; width: 100%; height: 100%;
            z-index: 1;
            background-image: url('{{ asset('images/sky-bg.jpg') }}');
            background-size: cover;
            background-position: center top;
            filter: brightness(1.15) contrast(1.1) saturate(1.3);
        }

        .layer-clouds-container {
            position: absolute;
            top: 0; left: 0; width: 100%; height: 75%;
            z-index: 2;
            pointer-events: none;
            overflow: hidden;
        }

        .cloud-layer {
            position: absolute;
            top: 0; left: 0; width: 200%; height: 100%;
            background-repeat: repeat-x;
            background-position: left top;
        }

        .cloud-layer-1 {
            background-image: url('{{ asset('images/cloud-1.png') }}');
            background-size: contain;
            opacity: 1;
            z-index: 21;
            filter: brightness(1.12) contrast(1.22) drop-shadow(0 6px 20px rgba(10, 40, 90, 0.25));
        }

        .cloud-layer-2 {
            background-image: url('{{ asset('images/cloud-2.png') }}');
            background-size: contain;
            opacity: 0.96;
            top: 6%;
            z-index: 22;
            filter: brightness(1.1) contrast(1.18) drop-shadow(0 5px 16px rgba(10, 40, 90, 0.2));
        }

        .layer-building {
            position: absolute;
            bottom: 0; left: 0; width: 100%; height: 100%;
            z-index: 3;
            pointer-events: none;
            background-image: url('{{ asset('images/building-cutout.png') }}');
            background-size: cover;
            background-position: left 45% bottom;
            background-repeat: no-repeat;
            filter: brightness(1.15) contrast(1.02);
            will-change: transform, opacity;
            transform: translate3d(0, 140px, 0);
            opacity: 0;
        }

        .container {
            position: relative;
            z-index: 6;
            display: flex;
            width: 100%;
            height: 100vh;
            max-height: 100vh;
            overflow: hidden !important;
        }

        .left-side {
            flex: 1.15;
            display: flex;
            flex-direction: column;
            justify-content: flex-start;
            padding: 1cm 0 1cm 88px;
            position: relative;
            height: 100vh;
        }

        .brand-header {
            display: flex;
            align-items: center;
            gap: 14px;
            margin-bottom: 22px;
        }

        .brand-logo {
            height: 50px;
            width: auto;
            filter: drop-shadow(0 2px 8px rgba(0,0,0,0.25));
        }

        .brand-text h2 {
            font-size: 20px;
            font-weight: 800;
            color: #ffffff;
            line-height: 1.15;
            text-shadow: 0 2px 6px rgba(0,0,0,0.4);
        }

        .brand-text p {
            font-size: 12px;
            font-weight: 600;
            color: rgba(255, 255, 255, 0.95);
            letter-spacing: 1px;
            text-shadow: 0 1px 4px rgba(0,0,0,0.3);
        }

        .hero-title {
            font-size: 42px;
            font-weight: 800;
            color: #ffffff;
            margin-bottom: 12px;
            line-height: 1.1;
            text-shadow: 0 2px 14px rgba(0,0,0,0.35);
        }

        .hero-line {
            width: 48px;
            height: 4px;
            background: linear-gradient(90deg, #f59e0b, #fbbf24);
            margin-bottom: 16px;
            border-radius: 2px;
        }

        .hero-desc {
            font-size: 14.5px;
            color: rgba(255, 255, 255, 0.95);
            line-height: 1.65;
            max-width: 440px;
            font-weight: 500;
            text-shadow: 0 1px 6px rgba(0,0,0,0.3);
        }

        .right-side {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: flex-end;
            padding: 1cm 88px 1cm 0;
            position: relative;
            z-index: 8;
            height: 100vh;
        }

        .mesh-gradient-container {
            position: absolute;
            top: 90px; bottom: 90px; right: 90px;
            width: 500px;
            max-width: 42vw;
            pointer-events: none;
            z-index: 5;
        }

        .mesh-blob-halo {
            top: 50%; left: 50%;
            transform: translate(-50%, -50%);
            width: 440px; height: 440px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(139, 184, 255, 0.42) 0%, rgba(99, 216, 255, 0.28) 45%, transparent 72%);
            filter: blur(38px);
            position: absolute;
        }

        .login-card {
            background: rgba(255, 255, 255, 0.94);
            backdrop-filter: blur(28px);
            -webkit-backdrop-filter: blur(28px);
            width: 100%;
            max-width: 380px;
            border-radius: 22px;
            padding: 32px 28px;
            box-shadow:
                0 25px 70px rgba(90, 75, 255, 0.16),
                0 10px 25px rgba(0, 0, 0, 0.04),
                inset 0 1px 0 rgba(255, 255, 255, 1);
            display: flex;
            flex-direction: column;
            position: relative;
            z-index: 10;
            border: 1.5px solid rgba(255, 255, 255, 0.95);
        }

        .login-logo {
            width: 180px;
            max-width: 88%;
            height: auto;
            margin: 0 auto 16px auto;
            display: block;
        }

        .card-header-title {
            font-size: 17px;
            font-weight: 800;
            color: #0f172a;
            text-align: center;
            margin-bottom: 6px;
        }

        .card-header-desc {
            font-size: 12px;
            color: #64748b;
            text-align: center;
            line-height: 1.5;
            margin-bottom: 18px;
        }

        .form-group {
            margin-bottom: 14px;
        }

        .form-label {
            display: block;
            font-size: 12px;
            font-weight: 700;
            color: #1e3a5f;
            margin-bottom: 5px;
        }

        .input-group {
            position: relative;
            display: flex;
            align-items: center;
        }

        .input-icon {
            position: absolute;
            left: 13px;
            color: #94a3b8;
            font-size: 15px;
            pointer-events: none;
        }

        .form-input {
            width: 100%;
            height: 42px;
            border: 1.5px solid #cbd5e1;
            border-radius: 10px;
            padding: 0 13px 0 40px;
            font-family: inherit;
            font-size: 13px;
            color: #0f172a;
            outline: none;
            transition: all 0.25s ease;
            background: #ffffff;
        }

        .form-input:focus {
            border-color: #2563eb;
            box-shadow: 0 0 0 3.5px rgba(37, 99, 235, 0.15);
        }

        .btn-eye {
            position: absolute;
            right: 13px;
            background: none;
            border: none;
            color: #94a3b8;
            font-size: 15px;
            cursor: pointer;
            padding: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: color 0.2s ease;
        }

        .btn-eye:hover {
            color: #2563eb;
        }

        .btn-submit {
            width: 100%;
            height: 42px;
            background: linear-gradient(135deg, #1e3a8a 0%, #2563eb 50%, #1d4ed8 100%);
            background-size: 200% 200%;
            color: #ffffff;
            border: none;
            border-radius: 10px;
            font-family: inherit;
            font-size: 13.5px;
            font-weight: 700;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 14px rgba(37, 99, 235, 0.35);
            margin-top: 18px;
        }

        .btn-submit:hover {
            transform: scale(1.015);
            box-shadow: 0 7px 20px rgba(37, 99, 235, 0.45);
        }

        .back-link {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            width: 100%;
            margin-top: 14px;
            font-size: 12.5px;
            color: #2563eb;
            font-weight: 600;
            text-decoration: none;
            transition: color 0.2s ease;
        }

        .back-link:hover {
            color: #1d4ed8;
            text-decoration: underline;
        }

        .footer-text {
            text-align: center;
            font-size: 11px;
            color: #94a3b8;
            margin-top: 18px;
            font-weight: 500;
        }

        /* Alert Boxes */
        .alert-box {
            padding: 10px 14px;
            border-radius: 10px;
            font-size: 12px;
            margin-bottom: 14px;
            font-weight: 500;
            line-height: 1.4;
        }

        .alert-error {
            background-color: #FEF2F2;
            border: 1px solid #FECACA;
            color: #DC2626;
        }

        .alert-success {
            background-color: #F0FDF4;
            border: 1px solid #BBF7D0;
            color: #16A34A;
        }

        .alert-box ul {
            margin-left: 18px;
        }

        @media (max-width: 1024px) {
            html, body { height: auto; overflow-y: auto !important; }
            .page-wrapper { position: relative; height: auto; min-height: 100vh; flex-direction: column; }
            .container { flex-direction: column; height: auto; min-height: 100vh; }
            .left-side { padding: 28px 24px 12px 24px; flex: none; width: 100%; height: auto; text-align: center; align-items: center; }
            .brand-header { justify-content: center; }
            .hero-title { font-size: 30px; }
            .hero-line { margin: 0 auto 10px auto; }
            .hero-desc { font-size: 13px; max-width: 500px; margin: 0 auto; }
            .right-side { flex: none; width: 100%; height: auto; padding: 12px 20px 32px 20px; justify-content: center; }
            .login-card { max-width: 400px; width: 92%; margin: 0 auto; padding: 28px 24px; }
            .mesh-gradient-container { display: none; }
            .layer-sky, .layer-clouds-container, .layer-building { position: fixed; }
        }

        @media (max-width: 480px) {
            .login-card { width: 96%; max-width: 360px; padding: 20px 16px; border-radius: 18px; }
            .login-logo { width: 135px; margin-bottom: 14px; }
        }
    </style>
</head>
<body>

<div class="page-wrapper" id="pageWrapper">
    <div class="layer-sky"></div>
    <div class="layer-clouds-container">
        <div class="cloud-layer cloud-layer-1"></div>
        <div class="cloud-layer cloud-layer-2"></div>
    </div>
    <div class="layer-building"></div>

    <div class="container">
        <div class="left-side">
            <div class="brand-header">
                <img src="{{ asset('images/pemalang-shield.png') }}" alt="Logo Kabupaten Pemalang" class="brand-logo">
                <div class="brand-text">
                    <h2>BKPSDM</h2>
                    <p>KABUPATEN PEMALANG</p>
                </div>
            </div>

            <h1 class="hero-title">360° Kinerja ASN</h1>
            <div class="hero-line"></div>
            <p class="hero-desc">
                Sistem penilaian kinerja ASN secara 360 derajat untuk mewujudkan birokrasi yang profesional, berintegritas dan berorientasi pelayanan.
            </p>
        </div>

        <div class="right-side">
            <div class="mesh-gradient-container">
                <div class="mesh-blob-halo"></div>
            </div>

            <div class="login-card" id="loginCard">
                <img src="{{ asset('images/logo-bkpsdm.png') }}" alt="Logo BKPSDM Pemalang" class="login-logo">

                <h3 class="card-header-title">Atur Ulang Password</h3>
                <p class="card-header-desc">Silakan buat password baru untuk akun Anda.</p>

                @if (session('status'))
                    <div class="alert-box alert-success">
                        {{ session('status') }}
                    </div>
                @endif

                @if ($errors->any())
                    <div class="alert-box alert-error">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('password.store') }}">
                    @csrf

                    <!-- Password Reset Token -->
                    <input type="hidden" name="token" value="{{ $request->route('token') }}">

                    <div class="form-group">
                        <label for="email" class="form-label">Email</label>
                        <div class="input-group">
                            <i class="bi bi-envelope input-icon"></i>
                            <input type="email" id="email" name="email" value="{{ old('email', $request->email) }}" class="form-input" placeholder="Masukkan Email Anda" required autofocus readonly>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="password" class="form-label">Password Baru</label>
                        <div class="input-group">
                            <i class="bi bi-lock input-icon"></i>
                            <input type="password" id="password" name="password" class="form-input" placeholder="Masukkan Password Baru" required autocomplete="new-password">
                            <button type="button" class="btn-eye" onclick="togglePassword('password', 'toggleIcon1')" aria-label="Toggle password visibility">
                                <i class="bi bi-eye-slash" id="toggleIcon1"></i>
                            </button>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="password_confirmation" class="form-label">Konfirmasi Password Baru</label>
                        <div class="input-group">
                            <i class="bi bi-shield-lock input-icon"></i>
                            <input type="password" id="password_confirmation" name="password_confirmation" class="form-input" placeholder="Ulangi Password Baru" required autocomplete="new-password">
                            <button type="button" class="btn-eye" onclick="togglePassword('password_confirmation', 'toggleIcon2')" aria-label="Toggle password visibility">
                                <i class="bi bi-eye-slash" id="toggleIcon2"></i>
                            </button>
                        </div>
                    </div>

                    <button type="submit" class="btn-submit">
                        <i class="bi bi-check-circle"></i>
                        <span>Reset Password</span>
                    </button>

                    <a href="{{ route('login') }}" class="back-link">
                        <i class="bi bi-arrow-left"></i>
                        <span>Kembali ke Halaman Login</span>
                    </a>

                    <div class="footer-text">
                        &copy; 2026 BKPSDM Kabupaten Pemalang
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/gsap.min.js"></script>
<script>
    function togglePassword(inputId, iconId) {
        const input = document.getElementById(inputId);
        const icon = document.getElementById(iconId);
        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.replace('bi-eye-slash', 'bi-eye');
        } else {
            input.type = 'password';
            icon.classList.replace('bi-eye', 'bi-eye-slash');
        }
    }

    document.addEventListener('DOMContentLoaded', () => {
        gsap.fromTo('#layerBuilding', 
            { y: 140, opacity: 0 }, 
            { y: 0, opacity: 1, duration: 1.2, ease: 'power3.out' }
        );
        gsap.fromTo('#loginCard', 
            { opacity: 0, y: 15 },
            { opacity: 1, y: 0, duration: 0.6, ease: 'power3.out', delay: 0.2 }
        );
        gsap.to('.cloud-layer-1', { xPercent: -50, ease: 'none', duration: 90, repeat: -1 });
        gsap.to('.cloud-layer-2', { xPercent: -50, ease: 'none', duration: 70, repeat: -1 });
    });
</script>

</body>
</html>
