<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="Sistem Penilaian Kinerja ASN 360 Derajat BKPSDM Kabupaten Pemalang. Login untuk mengakses dashboard penilaian kinerja.">
    <title>360° Kinerja ASN — BKPSDM Kabupaten Pemalang</title>

    <!-- Google Fonts: Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            min-height: 100vh;
            margin: 0;
            padding: 0;
            overflow: hidden;
            color: #1e293b;
            background-color: #0f1d30;
        }

        /* ===================== FULL-PAGE WRAPPER ===================== */
        .page-wrapper {
            position: relative;
            width: 100%;
            height: 100vh;
            display: flex;
            overflow: hidden;
            will-change: transform;
            transform: translate3d(0,0,0);
        }

        /* Layer 1: Sky Base */
        .layer-sky {
            position: absolute;
            top: 0; left: 0; width: 100%; height: 100%;
            background: linear-gradient(180deg, #1e3a8a 0%, #3b82f6 40%, #93c5fd 80%, #dbeafe 100%);
            z-index: 1;
        }

        /* Layer 2: Moving Clouds (Drifting right to left loop) */
        .layer-clouds {
            position: absolute;
            top: 0; left: 0; width: 200%; height: 65%;
            z-index: 2;
            pointer-events: none;
            opacity: 0.85;
            background-image: 
                radial-gradient(ellipse at 20% 40%, rgba(255, 255, 255, 0.4) 0%, transparent 60%),
                radial-gradient(ellipse at 50% 30%, rgba(255, 255, 255, 0.5) 0%, transparent 55%),
                radial-gradient(ellipse at 80% 45%, rgba(255, 255, 255, 0.35) 0%, transparent 50%);
            animation: moveClouds 35s linear infinite;
            will-change: transform;
            transform: translate3d(0, 0, 0);
        }

        @keyframes moveClouds {
            0% { transform: translate3d(0, 0, 0); }
            100% { transform: translate3d(-50%, 0, 0); }
        }

        /* Layer 3: Building Background Photo (Hero Building) */
        .layer-building {
            position: absolute;
            top: 0; left: 0; width: 100%; height: 100%;
            z-index: 3;
            background-image: url('{{ asset('images/building-bkpsdm.jpeg') }}');
            background-size: cover;
            background-position: left 40%;
            background-repeat: no-repeat;
            will-change: transform, opacity;
            transform: translate3d(0, 0, 0);
        }

        /* Layer 4: Dark Overlay (Left Side Contrast) */
        .layer-dark-overlay {
            position: absolute;
            top: 0; left: 0; right: 0; bottom: 0;
            background: linear-gradient(
                90deg,
                rgba(15, 30, 60, 0.78) 0%,
                rgba(15, 30, 60, 0.62) 25%,
                rgba(15, 30, 60, 0.28) 45%,
                rgba(15, 30, 60, 0.0) 65%
            );
            z-index: 4;
            pointer-events: none;
        }

        /* Layer 5: Ambient Moving Fog (Behind Right Login Card) */
        .layer-fog {
            position: absolute;
            top: 0; right: 0; width: 65%; height: 100%;
            z-index: 5;
            pointer-events: none;
            background: linear-gradient(
                90deg,
                transparent 0%,
                rgba(215, 230, 246, 0.25) 30%,
                rgba(220, 233, 248, 0.65) 50%,
                rgba(226, 237, 250, 0.90) 70%,
                rgba(240, 246, 255, 0.98) 100%
            );
            overflow: hidden;
            will-change: transform;
            transform: translate3d(0,0,0);
        }

        .fog-cloud {
            position: absolute;
            border-radius: 50%;
            filter: blur(60px);
            opacity: 0.45;
            will-change: transform, opacity;
        }

        .fog-cloud-1 {
            width: 500px; height: 500px;
            background: rgba(191, 219, 254, 0.7);
            top: 10%; right: 5%;
        }

        .fog-cloud-2 {
            width: 450px; height: 450px;
            background: rgba(224, 242, 254, 0.6);
            bottom: 5%; right: 15%;
        }

        /* ===================== LAYOUT CONTAINER ===================== */
        .container {
            position: relative;
            z-index: 6;
            display: flex;
            width: 100%;
            height: 100vh;
        }

        /* ===================== LEFT SIDE ===================== */
        .left-side {
            flex: 1.15;
            display: flex;
            flex-direction: column;
            justify-content: flex-start;
            padding: 48px 56px;
            position: relative;
            background: transparent;
            will-change: transform;
        }

        .left-content {
            position: relative;
            z-index: 7;
            will-change: transform, opacity;
        }

        /* Brand Header */
        .brand-header {
            display: flex;
            align-items: center;
            gap: 14px;
            margin-bottom: 60px;
            will-change: transform, opacity;
        }

        .brand-logo {
            height: 52px;
            width: auto;
            filter: drop-shadow(0 2px 8px rgba(0,0,0,0.2));
        }

        .brand-text h2 {
            font-size: 20px;
            font-weight: 800;
            color: #ffffff;
            line-height: 1.15;
            letter-spacing: 0.5px;
            text-shadow: 0 1px 4px rgba(0,0,0,0.3);
        }

        .brand-text p {
            font-size: 12px;
            font-weight: 600;
            color: rgba(255, 255, 255, 0.9);
            letter-spacing: 1px;
            text-shadow: 0 1px 4px rgba(0,0,0,0.3);
        }

        /* Hero Title & Text */
        .hero-title {
            font-size: 48px;
            font-weight: 800;
            color: #ffffff;
            margin-bottom: 18px;
            letter-spacing: -0.5px;
            line-height: 1.1;
            text-shadow: 0 2px 12px rgba(0,0,0,0.3);
            will-change: transform, opacity, filter;
        }

        .hero-line {
            width: 48px;
            height: 4px;
            background: linear-gradient(90deg, #f59e0b, #fbbf24);
            margin-bottom: 22px;
            border-radius: 2px;
            will-change: transform;
        }

        .hero-desc {
            font-size: 15px;
            color: rgba(255, 255, 255, 0.92);
            line-height: 1.75;
            max-width: 420px;
            font-weight: 500;
            text-shadow: 0 1px 6px rgba(0,0,0,0.25);
        }

        .hero-desc-line {
            display: block;
            will-change: transform, opacity, filter;
        }

        /* ===================== RIGHT SIDE & LOGIN CARD ===================== */
        .right-side {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px;
            position: relative;
            background: transparent;
            z-index: 8;
            will-change: transform;
        }

        .login-card {
            background: rgba(255, 255, 255, 0.96);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            width: 100%;
            max-width: 390px;
            border-radius: 22px;
            padding: 34px 32px;
            box-shadow:
                0 25px 50px rgba(0, 0, 0, 0.08),
                0 4px 12px rgba(0, 0, 0, 0.04),
                0 1px 3px rgba(0, 0, 0, 0.02);
            display: flex;
            flex-direction: column;
            position: relative;
            z-index: 10;
            border: 1px solid rgba(255, 255, 255, 0.8);
            will-change: transform, opacity, box-shadow;
            transition: box-shadow 0.4s ease;
        }

        .login-logo {
            width: 190px;
            height: auto;
            margin: 2px auto 30px auto;
            display: block;
        }

        .stagger-item {
            will-change: transform, opacity;
        }

        /* Form Controls */
        .form-group {
            margin-bottom: 16px;
        }

        .form-label {
            display: block;
            font-size: 12.5px;
            font-weight: 700;
            color: #1e3a5f;
            margin-bottom: 7px;
        }

        .input-group {
            position: relative;
            display: flex;
            align-items: center;
        }

        .input-icon {
            position: absolute;
            left: 14px;
            color: #94a3b8;
            font-size: 17px;
            pointer-events: none;
            transition: color 0.2s ease;
        }

        .form-input {
            width: 100%;
            height: 44px;
            border: 1.5px solid #e2e8f0;
            border-radius: 11px;
            padding: 0 14px 0 42px;
            font-family: inherit;
            font-size: 13.5px;
            color: #0f172a;
            outline: none;
            transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
            background: #ffffff;
        }

        .form-input::placeholder {
            color: #94a3b8;
            font-weight: 400;
        }

        .form-input:focus {
            border-color: #3b82f6;
            box-shadow: 0 0 0 3.5px rgba(59, 130, 246, 0.15);
        }

        .form-input:focus + .input-icon,
        .input-group:focus-within .input-icon {
            color: #3b82f6;
        }

        .btn-eye {
            position: absolute;
            right: 14px;
            background: none;
            border: none;
            color: #94a3b8;
            font-size: 17px;
            cursor: pointer;
            padding: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: color 0.2s ease;
        }

        .btn-eye:hover {
            color: #3b82f6;
        }

        /* Options (Checkbox & Forgot Password) */
        .form-options {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-top: 14px;
            margin-bottom: 22px;
        }

        .checkbox-label {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 12.5px;
            color: #475569;
            font-weight: 500;
            cursor: pointer;
        }

        .checkbox-input {
            width: 16px;
            height: 16px;
            border-radius: 4px;
            border: 1.5px solid #cbd5e1;
            accent-color: #2563eb;
            cursor: pointer;
        }

        .forgot-link {
            font-size: 12.5px;
            color: #2563eb;
            font-weight: 600;
            text-decoration: none;
            transition: color 0.2s ease;
        }

        .forgot-link:hover {
            color: #1d4ed8;
            text-decoration: underline;
        }

        /* Buttons */
        .btn-submit {
            width: 100%;
            height: 46px;
            background: linear-gradient(135deg, #1e3a8a 0%, #2563eb 50%, #1d4ed8 100%);
            background-size: 200% 200%;
            color: #ffffff;
            border: none;
            border-radius: 11px;
            font-family: inherit;
            font-size: 14px;
            font-weight: 700;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
            box-shadow: 0 4px 14px rgba(37, 99, 235, 0.35);
            will-change: transform, box-shadow;
        }

        .btn-submit:hover {
            background-position: right center;
            transform: scale(1.02);
            box-shadow: 0 8px 22px rgba(37, 99, 235, 0.45);
        }

        .btn-submit:active {
            transform: scale(0.98);
        }

        .arrow-icon {
            font-size: 15px;
            transition: transform 0.2s ease;
        }

        .btn-submit:hover .arrow-icon {
            transform: translateX(4px);
        }

        .divider {
            display: flex;
            align-items: center;
            text-align: center;
            margin: 20px 0;
            color: #94a3b8;
            font-size: 12px;
            font-weight: 500;
        }

        .divider::before,
        .divider::after {
            content: '';
            flex: 1;
            border-bottom: 1px solid #e2e8f0;
        }

        .divider::before { margin-right: 12px; }
        .divider::after { margin-left: 12px; }

        .btn-sso {
            width: 100%;
            height: 44px;
            background: #f8fafc;
            color: #334155;
            border: 1.5px solid #e2e8f0;
            border-radius: 11px;
            font-family: inherit;
            font-size: 13.5px;
            font-weight: 600;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            transition: all 0.2s ease;
        }

        .btn-sso:hover {
            background: #ffffff;
            border-color: #cbd5e1;
            color: #1e293b;
            box-shadow: 0 2px 8px rgba(0,0,0,0.04);
        }

        .footer-text {
            text-align: center;
            font-size: 11.5px;
            color: #94a3b8;
            margin-top: 24px;
            font-weight: 500;
        }

        /* Alert Boxes */
        .alert-box {
            padding: 10px 14px;
            border-radius: 10px;
            font-size: 12.5px;
            margin-bottom: 16px;
            font-weight: 500;
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
            margin-left: 20px;
        }

        /* ===================== RESPONSIVE OPTIMIZATIONS ===================== */
        @media (max-width: 1024px) {
            .container {
                flex-direction: column;
                height: auto;
                min-height: 100vh;
            }
            body { overflow: auto; }
            .left-side {
                padding: 36px 32px;
                min-height: 40vh;
            }
            .brand-header {
                margin-bottom: 32px;
            }
            .hero-title {
                font-size: 36px;
            }
            .right-side {
                padding: 32px 20px;
            }
            .layer-fog {
                width: 100%;
            }
        }

        @media (max-width: 600px) {
            .left-side {
                padding: 28px 24px;
                min-height: 35vh;
            }
            .brand-header {
                margin-bottom: 24px;
            }
            .brand-logo {
                height: 40px;
            }
            .brand-text h2 {
                font-size: 16px;
            }
            .hero-title {
                font-size: 28px;
            }
            .hero-desc {
                font-size: 13px;
            }
            .login-card {
                padding: 24px 20px;
                border-radius: 16px;
                max-width: 360px;
            }
            .login-logo {
                width: 160px;
            }
        }
    </style>
</head>
<body>

<!-- FULL-PAGE PARALLAX & MOTION WRAPPER -->
<div class="page-wrapper" id="pageWrapper">

    <!-- Layer 1: Sky Base -->
    <div class="layer-sky"></div>

    <!-- Layer 2: Moving Clouds -->
    <div class="layer-clouds"></div>

    <!-- Layer 3: Building Background Photo -->
    <div class="layer-building" id="layerBuilding"></div>

    <!-- Layer 4: Dark Gradient Overlay -->
    <div class="layer-dark-overlay"></div>

    <!-- Layer 5: Ambient Moving Fog -->
    <div class="layer-fog" id="layerFog">
        <div class="fog-cloud fog-cloud-1"></div>
        <div class="fog-cloud fog-cloud-2"></div>
    </div>

    <!-- Layer 6 & 7: Main Container Content -->
    <div class="container">
        <!-- Left Side: Hero Brand & Text -->
        <div class="left-side" id="leftSide">
            <div class="left-content" id="leftContent">
                <div class="brand-header stagger-brand">
                    <img src="{{ asset('images/pemalang-shield.png') }}" alt="Logo Kabupaten Pemalang" class="brand-logo">
                    <div class="brand-text">
                        <h2>BKPSDM</h2>
                        <p>KABUPATEN PEMALANG</p>
                    </div>
                </div>

                <h1 class="hero-title">360° Kinerja ASN</h1>
                <div class="hero-line"></div>
                <p class="hero-desc">
                    <span class="hero-desc-line">Sistem penilaian kinerja ASN secara 360 derajat</span>
                    <span class="hero-desc-line">untuk mewujudkan birokrasi yang profesional,</span>
                    <span class="hero-desc-line">berintegritas dan berorientasi pelayanan.</span>
                </p>
            </div>
        </div>

        <!-- Right Side: Login Card -->
        <div class="right-side" id="rightSide">
            <div class="login-card" id="loginCard">
                <img src="{{ asset('images/logo-exact.png') }}" alt="Logo BKPSDM Pemalang" class="login-logo stagger-item">

                @if (session('status'))
                    <div class="alert-box alert-success stagger-item">
                        {{ session('status') }}
                    </div>
                @endif

                @if ($errors->any())
                    <div class="alert-box alert-error stagger-item">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('login') }}">
                    @csrf

                    <div class="form-group stagger-item">
                        <label for="login" class="form-label">NIP / Email</label>
                        <div class="input-group">
                            <i class="bi bi-person input-icon"></i>
                            <input type="text" id="login" name="login" class="form-input" value="{{ old('login', old('email')) }}" placeholder="Masukkan NIP atau Email" required autofocus>
                        </div>
                    </div>

                    <div class="form-group stagger-item">
                        <label for="password" class="form-label">Password</label>
                        <div class="input-group">
                            <i class="bi bi-lock input-icon"></i>
                            <input type="password" id="password" name="password" class="form-input" placeholder="Masukkan Password" required>
                            <button type="button" class="btn-eye" onclick="togglePassword()" aria-label="Toggle password visibility">
                                <i class="bi bi-eye-slash" id="toggleIcon"></i>
                            </button>
                        </div>
                    </div>

                    <div class="form-options stagger-item">
                        <label class="checkbox-label">
                            <input type="checkbox" id="remember_me" name="remember" class="checkbox-input">
                            Ingat sesi saya
                        </label>
                        <a href="{{ route('password.request') }}" class="forgot-link">Lupa password?</a>
                    </div>

                    <button type="submit" class="btn-submit stagger-item" id="btn-masuk">
                        Masuk <span class="arrow-icon">→</span>
                    </button>

                    <div class="divider stagger-item">atau</div>

                    <button type="button" class="btn-sso stagger-item" id="btn-sso">
                        <i class="bi bi-bank"></i> Masuk dengan SSO
                    </button>

                    <div class="footer-text stagger-item">
                        &copy; 2026 BKPSDM Kabupaten Pemalang
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Password Visibility Toggle Script -->
<script>
    function togglePassword() {
        const passwordInput = document.getElementById('password');
        const toggleIcon = document.getElementById('toggleIcon');
        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            toggleIcon.classList.replace('bi-eye-slash', 'bi-eye');
        } else {
            passwordInput.type = 'password';
            toggleIcon.classList.replace('bi-eye', 'bi-eye-slash');
        }
    }
</script>

<!-- GSAP 3 & ScrollTrigger Engine -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/gsap.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/ScrollTrigger.min.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        gsap.registerPlugin(ScrollTrigger);

        const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

        if (prefersReducedMotion) {
            gsap.set(['.layer-building', '.stagger-brand', '.hero-title', '.hero-line', '.hero-desc-line', '.login-card', '.stagger-item'], {
                opacity: 1, y: 0, x: 0, scale: 1, filter: 'blur(0px)'
            });
            return;
        }

        // ================= 1. GSAP STAGGERED ENTRANCE TIMELINE =================
        const tl = gsap.timeline({ defaults: { ease: 'power4.out' } });

        // Gedung BKPSDM: translateY(120px) -> 0, duration 2.2s, power4.out (no bounce)
        tl.fromTo('#layerBuilding', 
            { y: 120, opacity: 0 }, 
            { y: 0, opacity: 1, duration: 2.2 }
        );

        // Logo BKPSDM (top-left): translateY(-30px) -> 0, duration 0.8s
        tl.fromTo('.stagger-brand',
            { y: -30, opacity: 0 },
            { y: 0, opacity: 1, duration: 0.8, ease: 'power3.out' },
            "-=1.8"
        );

        // Hero Title ("360° Kinerja ASN"): translateX(-80px), blur(8px) -> 0, duration 1.0s
        tl.fromTo('.hero-title',
            { x: -80, opacity: 0, filter: 'blur(8px)' },
            { x: 0, opacity: 1, filter: 'blur(0px)', duration: 1.0 },
            "-=1.4"
        );

        // Garis Kuning: scaleX 0 -> 1, origin: left, duration 0.7s
        tl.fromTo('.hero-line',
            { scaleX: 0, transformOrigin: 'left center' },
            { scaleX: 1, duration: 0.7, ease: 'power3.inOut' },
            "-=0.9"
        );

        // Deskripsi (Staggered lines): translateX(-40px), blur(6px) -> 0
        tl.fromTo('.hero-desc-line',
            { x: -40, opacity: 0, filter: 'blur(6px)' },
            { x: 0, opacity: 1, filter: 'blur(0px)', duration: 0.8, stagger: 0.14, ease: 'power3.out' },
            "-=0.6"
        );

        // Login Card: translateX(120px) -> 0, duration 1.2s, power4.out
        tl.fromTo('#loginCard',
            { x: 120, opacity: 0, boxShadow: '0 10px 25px rgba(0,0,0,0.02)' },
            { x: 0, opacity: 1, boxShadow: '0 25px 50px rgba(0,0,0,0.08)', duration: 1.2 },
            "-=1.8"
        );

        // Form Input Fields (Staggered translateY(18px) -> 0)
        tl.fromTo('.stagger-item',
            { y: 18, opacity: 0 },
            { y: 0, opacity: 1, duration: 0.6, stagger: 0.10, ease: 'power3.out' },
            "-=0.8"
        );

        // ================= 2. IDLE FLOATING CARD MOTION =================
        // Floating motion: translateY 0 -> -4px -> 0 (6s loop, sine.inOut)
        gsap.to('#loginCard', {
            y: -4,
            duration: 3,
            repeat: -1,
            yoyo: true,
            ease: 'sine.inOut',
            delay: 2.2
        });

        // ================= 3. AMBIENT FOG NOISE MOTION =================
        // Kabut bergerak seperti asap (opacity, scale, translate acak, 14-18s loop)
        gsap.to('.fog-cloud-1', {
            x: 25,
            y: -15,
            scale: 1.08,
            opacity: 0.65,
            duration: 14,
            repeat: -1,
            yoyo: true,
            ease: 'sine.inOut'
        });

        gsap.to('.fog-cloud-2', {
            x: -20,
            y: 10,
            scale: 1.05,
            opacity: 0.5,
            duration: 18,
            repeat: -1,
            yoyo: true,
            ease: 'sine.inOut',
            delay: 2
        });

        // ================= 4. 60 FPS MOUSE PARALLAX DEPTH =================
        if (window.innerWidth > 1024) {
            const titleQuickX = gsap.quickTo('#leftContent', 'x', { duration: 0.6, ease: 'power2.out' });
            const titleQuickY = gsap.quickTo('#leftContent', 'y', { duration: 0.6, ease: 'power2.out' });

            const cardQuickX = gsap.quickTo('#loginCard', 'x', { duration: 0.6, ease: 'power2.out' });
            const cardQuickY = gsap.quickTo('#loginCard', 'y', { duration: 0.6, ease: 'power2.out' });

            const fogQuickX = gsap.quickTo('#layerFog', 'x', { duration: 0.8, ease: 'power2.out' });
            const fogQuickY = gsap.quickTo('#layerFog', 'y', { duration: 0.8, ease: 'power2.out' });

            const bldgQuickX = gsap.quickTo('#layerBuilding', 'x', { duration: 0.7, ease: 'power2.out' });
            const bldgQuickY = gsap.quickTo('#layerBuilding', 'y', { duration: 0.7, ease: 'power2.out' });

            window.addEventListener('mousemove', (e) => {
                const { innerWidth, innerHeight } = window;
                const mouseX = (e.clientX / innerWidth - 0.5) * 2;
                const mouseY = (e.clientY / innerHeight - 0.5) * 2;

                // Judul & Teks: 3-5px
                titleQuickX(mouseX * 4);
                titleQuickY(mouseY * 4);

                // Login Card: 2px
                cardQuickX(mouseX * 2);
                cardQuickY(mouseY * 2);

                // Kabut: 8px
                fogQuickX(mouseX * 8);
                fogQuickY(mouseY * 8);

                // Gedung: 4px
                bldgQuickX(mouseX * 4);
                bldgQuickY(mouseY * 4);
            });
        }

        // ================= 5. SCROLL STORYTELLING PARALLAX =================
        if (window.innerWidth > 768) {
            ScrollTrigger.create({
                trigger: '#pageWrapper',
                start: 'top top',
                end: 'bottom top',
                scrub: 1,
                onUpdate: (self) => {
                    const progress = self.progress;

                    // Scene 1: Gedung bergerak ~25% lebih lambat
                    gsap.set('#layerBuilding', { y: progress * -55 });

                    // Scene 2: Card login sedikit bergerak ke atas (~20px)
                    gsap.set('#rightSide', { y: progress * -20 });

                    // Scene 3: Title & text mengecil & fade halus
                    gsap.set('#leftContent', { opacity: 1 - progress * 0.45, scale: 1 - progress * 0.03 });
                }
            });
        }
    });
</script>

</body>
</html>
