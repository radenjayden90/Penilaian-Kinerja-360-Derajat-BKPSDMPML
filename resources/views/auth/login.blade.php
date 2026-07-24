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

        /* ===================== FULL-PAGE LAYERED WRAPPER ===================== */
        .page-wrapper {
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            max-height: 100vh;
            display: flex;
            overflow: hidden !important;
            will-change: transform;
            transform: translate3d(0,0,0);
        }

        /* Layer 1: Sky Layer (Langit di paling belakang - Bright & Vivid Sunny Sky) */
        .layer-sky {
            position: absolute;
            top: 0; left: 0; width: 100%; height: 100%;
            z-index: 1;
            background-image: url('{{ asset('images/sky-bg.jpg') }}');
            background-size: cover;
            background-position: center top;
            background-repeat: no-repeat;
            filter: brightness(1.15) contrast(1.1) saturate(1.3);
            will-change: transform;
            transform: translate3d(0,0,0);
        }

        /* Layer 2: Cloud Layer Container (Fluffy White Cumulus Clouds Floating) */
        .layer-clouds-container {
            position: absolute;
            top: 0; left: 0; width: 100%; height: 75%;
            z-index: 2;
            pointer-events: none;
            overflow: hidden;
            will-change: transform;
            transform: translate3d(0,0,0);
        }

        .cloud-layer {
            position: absolute;
            top: 0; left: 0; width: 200%; height: 100%;
            background-repeat: repeat-x;
            background-position: left top;
            will-change: transform;
            transform: translate3d(0,0,0);
        }

        .cloud-layer-1 {
            background-image: url('{{ asset('images/cloud-1.png') }}');
            background-size: contain;
            opacity: 1;
            top: 0%;
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

        .cloud-layer-3 {
            background-image: url('{{ asset('images/cloud-3.png') }}');
            background-size: contain;
            opacity: 0.94;
            top: 2%;
            transform: scale(0.95);
            z-index: 23;
            filter: brightness(1.14) contrast(1.2) drop-shadow(0 5px 18px rgba(10, 40, 90, 0.22));
        }

        /* Layer 3: Building Layer (Gedung BKPSDM PNG Transparan) */
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
            transform: translate3d(0, 160px, 0);
            opacity: 0;
        }

        /* Layer 4: Original Minimal Overlay */
        .layer-dark-overlay {
            position: absolute;
            top: 0; left: 0; right: 0; bottom: 0;
            background: transparent;
            z-index: 4;
            pointer-events: none;
        }

        /* Layer 5: Ambient Moving Cloud Fog Layer (Disabled - Fog removed per user request) */
        .layer-fog {
            display: none !important;
        }

        /* ===================== LAYOUT CONTAINER ===================== */
        .container {
            position: relative;
            z-index: 6;
            display: flex;
            width: 100%;
            height: 100vh;
            max-height: 100vh;
            overflow: hidden !important;
        }

        /* ===================== LEFT SIDE HERO CONTENT ===================== */
        .left-side {
            flex: 1.15;
            display: flex;
            flex-direction: column;
            justify-content: flex-start;
            padding: 1cm 0 1cm 88px;
            position: relative;
            background: transparent;
            will-change: transform;
            height: 100vh;
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
            margin-bottom: 22px;
            will-change: transform, opacity;
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
            letter-spacing: 0.5px;
            text-shadow: 0 2px 6px rgba(0,0,0,0.4);
        }

        .brand-text p {
            font-size: 12px;
            font-weight: 600;
            color: rgba(255, 255, 255, 0.95);
            letter-spacing: 1px;
            text-shadow: 0 1px 4px rgba(0,0,0,0.3);
        }

        /* Hero Title & Text */
        .hero-title {
            font-size: 42px;
            font-weight: 800;
            color: #ffffff;
            margin-bottom: 12px;
            letter-spacing: -0.5px;
            line-height: 1.1;
            text-shadow: 0 2px 14px rgba(0,0,0,0.35);
            will-change: transform, opacity, filter;
        }

        .hero-line {
            width: 48px;
            height: 4px;
            background: linear-gradient(90deg, #f59e0b, #fbbf24);
            margin-bottom: 16px;
            border-radius: 2px;
            will-change: transform;
        }

        .hero-desc {
            font-size: 14.5px;
            color: rgba(255, 255, 255, 0.95);
            line-height: 1.65;
            max-width: 440px;
            font-weight: 500;
            text-shadow: 0 1px 6px rgba(0,0,0,0.3);
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
            justify-content: flex-end;
            padding: 1cm 88px 1cm 0;
            position: relative;
            background: transparent;
            z-index: 8;
            will-change: transform;
            height: 100vh;
        }

        /* ===================== FLOATING LUXURY MESH GRADIENT LAYER ===================== */
        /* Placed strictly in the right 35–45% area, 90px–100px empty space from top, right, and bottom frame edges */
        .mesh-gradient-container {
            position: absolute;
            top: 90px;
            bottom: 90px;
            right: 90px;
            width: 500px;
            max-width: 42vw;
            pointer-events: none;
            z-index: 5;
            overflow: visible;
        }

        .mesh-blob {
            position: absolute;
            border-radius: 45% 55% 65% 35% / 50% 60% 40% 50%;
            filter: blur(48px);
            will-change: transform, opacity;
            pointer-events: none;
        }

        /* Luminous Core Halo - Directly centered behind the login card (#8BB8FF & #63D8FF) */
        .mesh-blob-halo {
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 440px;
            height: 440px;
            border-radius: 50%;
            background: radial-gradient(
                circle,
                rgba(139, 184, 255, 0.42) 0%,
                rgba(99, 216, 255, 0.28) 45%,
                transparent 72%
            );
            filter: blur(38px);
        }

        /* Top-Right Saturation Cloud - Indigo (#5A5CFF) & Royal Blue (#4F7BFF) +35% Saturation */
        .mesh-blob-top-right {
            top: 0;
            right: 0;
            width: 380px;
            height: 340px;
            border-radius: 35% 65% 55% 45% / 45% 55% 45% 55%;
            background: radial-gradient(
                circle,
                rgba(90, 92, 255, 0.38) 0%,
                rgba(79, 123, 255, 0.28) 50%,
                transparent 75%
            );
            filter: blur(48px);
        }

        /* Bottom-Right Saturation Cloud - Soft Violet (#7868FF) & Royal Blue (#4F7BFF) +35% Saturation */
        .mesh-blob-bottom-right {
            bottom: 0;
            right: 0;
            width: 400px;
            height: 360px;
            border-radius: 55% 45% 65% 35% / 50% 40% 60% 50%;
            background: radial-gradient(
                circle,
                rgba(120, 104, 255, 0.36) 0%,
                rgba(79, 123, 255, 0.25) 55%,
                transparent 75%
            );
            filter: blur(48px);
        }

        /* Left-Flowing Ambient Accent Cloud - Fades softly into negative space towards center/left */
        .mesh-blob-left-flow {
            top: 28%;
            left: -20px;
            width: 400px;
            height: 380px;
            border-radius: 60% 40% 50% 50% / 40% 50% 50% 60%;
            background: radial-gradient(
                circle,
                rgba(99, 216, 255, 0.28) 0%,
                rgba(139, 184, 255, 0.16) 50%,
                transparent 76%
            );
            filter: blur(50px);
        }

        /* Clean 100% Line-Free & Borderless Right Side */
        .right-side::before {
            display: none !important;
        }

        .login-card {
            background: rgba(255, 255, 255, 0.94);
            backdrop-filter: blur(28px);
            -webkit-backdrop-filter: blur(28px);
            width: 100%;
            max-width: 370px;
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
            will-change: transform, opacity, box-shadow;
            transition: box-shadow 0.4s ease;
        }

        .login-logo {
            width: 180px;
            max-width: 88%;
            height: auto;
            margin: 0 auto 18px auto;
            display: block;
        }

        .stagger-item {
            will-change: transform, opacity;
        }

        /* Form Controls */
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
            transition: color 0.2s ease;
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
            transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
            background: #ffffff;
        }

        .form-input::placeholder {
            color: #94a3b8;
            font-weight: 400;
        }

        .form-input:focus {
            border-color: #2563eb;
            box-shadow: 0 0 0 3.5px rgba(37, 99, 235, 0.15);
        }

        .form-input:focus + .input-icon,
        .input-group:focus-within .input-icon {
            color: #2563eb;
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

        /* Options (Checkbox & Forgot Password) */
        .form-options {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-top: 10px;
            margin-bottom: 18px;
        }

        .checkbox-label {
            display: flex;
            align-items: center;
            gap: 7px;
            font-size: 12px;
            color: #475569;
            font-weight: 500;
            cursor: pointer;
        }

        .checkbox-input {
            width: 15px;
            height: 15px;
            border-radius: 4px;
            border: 1.5px solid #cbd5e1;
            accent-color: #2563eb;
            cursor: pointer;
        }

        .forgot-link {
            font-size: 12px;
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
            transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
            box-shadow: 0 4px 14px rgba(37, 99, 235, 0.35);
            will-change: transform, box-shadow;
        }

        .btn-submit:hover {
            background-position: right center;
            transform: scale(1.015);
            box-shadow: 0 7px 20px rgba(37, 99, 235, 0.45);
        }

        .btn-submit:active {
            transform: scale(0.985);
        }

        .arrow-icon {
            font-size: 15px;
            transition: transform 0.2s ease;
        }

        .btn-submit:hover .arrow-icon {
            transform: translateX(4px);
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

        /* ===================== COMPREHENSIVE RESPONSIVE DESIGN ===================== */

        /* --- Smaller desktop --- */
        @media (max-width: 1280px) {
            .hero-title { font-size: 40px; }
            .left-side { padding: 1cm 40px; }
        }

        /* --- Tablet (≤1024px): Switch to column layout, allow scrolling --- */
        @media (max-width: 1024px) {
            html, body {
                height: auto;
                max-height: none;
                overflow-y: auto !important;
                overflow-x: hidden !important;
            }
            .page-wrapper {
                position: relative;
                height: auto;
                min-height: 100vh;
                min-height: 100dvh;
                max-height: none;
                overflow: visible !important;
                flex-direction: column;
            }
            .container {
                flex-direction: column;
                height: auto;
                min-height: 100vh;
                min-height: 100dvh;
                max-height: none;
                overflow: visible !important;
                justify-content: center;
                gap: 32px;
            }
            .left-side {
                padding: 28px 24px 12px 24px;
                flex: none;
                width: 100%;
                height: auto;
                text-align: center;
                align-items: center;
            }
            .brand-header {
                justify-content: center;
                margin-bottom: 14px;
            }
            .hero-title {
                font-size: 30px;
                margin-bottom: 8px;
            }
            .hero-line {
                margin: 0 auto 10px auto;
            }
            .hero-desc {
                font-size: 13px;
                max-width: 500px;
                margin: 0 auto;
            }
            .right-side {
                flex: none;
                width: 100%;
                height: auto;
                padding: 12px 20px 32px 20px;
                justify-content: center;
                align-items: center;
            }
            .login-card {
                max-width: 400px;
                width: 92%;
                margin: 0 auto;
                padding: 28px 24px;
            }
            .right-side::before {
                display: none !important;
            }
            .mesh-gradient-container {
                display: none;
            }
            .layer-building {
                background-position: center 60% bottom;
                height: 50vh;
            }
            .layer-sky,
            .layer-clouds-container,
            .layer-building,
            .layer-dark-overlay {
                position: fixed;
            }
        }

        /* --- Mobile (≤768px) --- */
        @media (max-width: 768px) {
            .left-side {
                padding: 20px 20px 8px 20px;
            }
            .brand-header {
                margin-bottom: 10px;
            }
            .brand-logo {
                height: 38px;
            }
            .brand-text h2 {
                font-size: 16px;
            }
            .brand-text p {
                font-size: 11px;
            }
            .hero-title {
                font-size: 26px;
                margin-bottom: 6px;
            }
            .hero-line {
                width: 36px;
                height: 3px;
                margin-bottom: 8px;
            }
            .hero-desc {
                font-size: 12.5px;
                line-height: 1.5;
            }
            .right-side {
                padding: 8px 16px 28px 16px;
            }
            .login-card {
                width: 94%;
                max-width: 380px;
                padding: 24px 20px;
                border-radius: 22px;
            }
            .login-logo {
                width: 160px;
                margin-bottom: 18px;
            }
            .form-input {
                height: 42px;
                font-size: 13px;
            }
            .btn-submit {
                height: 42px;
                font-size: 13.5px;
            }
            .layer-building {
                background-position: center bottom;
                height: 40vh;
            }
        }

        /* --- Small mobile (≤480px) --- */
        @media (max-width: 480px) {
            .left-side {
                padding: 16px 16px 6px 16px;
            }
            .brand-logo {
                height: 34px;
            }
            .brand-text h2 {
                font-size: 15px;
            }
            .brand-text p {
                font-size: 10.5px;
            }
            .hero-title {
                font-size: 22px;
            }
            .hero-desc {
                font-size: 11.5px;
                line-height: 1.45;
            }
            .login-card {
                width: 96%;
                max-width: 360px;
                padding: 20px 16px;
                border-radius: 18px;
            }
            .login-logo {
                width: 135px;
                margin-bottom: 14px;
            }
            .form-label {
                font-size: 11.5px;
            }
            .form-input {
                height: 40px;
                font-size: 12.5px;
                padding: 0 12px 0 38px;
            }
            .input-icon {
                font-size: 15px;
                left: 12px;
            }
            .btn-submit {
                height: 40px;
                font-size: 13px;
            }
            .footer-text {
                margin-top: 14px;
                font-size: 10px;
            }
        }

        /* --- Extra small (≤360px) --- */
        @media (max-width: 360px) {
            .left-side { padding: 12px 12px 4px 12px; }
            .brand-header { gap: 10px; }
            .brand-logo { height: 30px; }
            .brand-text h2 { font-size: 14px; }
            .hero-title { font-size: 20px; }
            .hero-desc { font-size: 11px; }
            .login-card { width: 100%; padding: 18px 14px; border-radius: 16px; }
            .login-logo { width: 120px; margin-bottom: 12px; }
            .form-input { height: 38px; font-size: 12px; }
            .btn-submit { height: 38px; font-size: 12.5px; }
        }

        /* --- Landscape mobile (short height + narrow) --- */
        @media (max-height: 500px) and (max-width: 1024px) {
            .page-wrapper {
                min-height: auto;
            }
            .container {
                flex-direction: row;
                min-height: 100vh;
                min-height: 100dvh;
                align-items: center;
            }
            .left-side {
                flex: 1;
                height: auto;
                padding: 16px 20px;
                text-align: left;
                align-items: flex-start;
                justify-content: center;
            }
            .brand-header {
                justify-content: flex-start;
                margin-bottom: 8px;
            }
            .brand-logo { height: 32px; }
            .hero-title { font-size: 22px; margin-bottom: 4px; }
            .hero-line { margin: 0 0 6px 0; width: 32px; height: 3px; }
            .hero-desc { font-size: 11px; line-height: 1.4; }
            .right-side {
                flex: 1;
                height: auto;
                padding: 12px 16px;
                justify-content: center;
            }
            .login-card {
                max-width: 340px;
                padding: 16px 14px;
                border-radius: 16px;
            }
            .login-logo { width: 110px; margin-bottom: 10px; }
            .form-group { margin-bottom: 6px; }
            .form-label { font-size: 11px; margin-bottom: 3px; }
            .form-input { height: 34px; font-size: 12px; }
            .form-options { margin-top: 2px; margin-bottom: 6px; }
            .checkbox-label { font-size: 11px; }
            .forgot-link { font-size: 11px; }
            .btn-submit { height: 34px; font-size: 12px; }
            .footer-text { margin-top: 8px; font-size: 9.5px; }
        }

        /* --- Very short landscape: hide hero entirely to prioritize form --- */
        @media (max-height: 400px) and (max-width: 1024px) {
            .left-side { display: none; }
            .container {
                justify-content: center;
                align-items: center;
            }
            .right-side {
                flex: none;
                width: 100%;
                padding: 8px 16px;
            }
            .login-card {
                max-width: 420px;
                padding: 14px 16px;
            }
        }
    </style>
</head>
<body>

<!-- SVG ClipPath for Solid Cloud Contour Diagonal Edge (Alunan Awan Pekat Miring) -->
<svg width="0" height="0" style="position: absolute; pointer-events: none;">
    <defs>
        <clipPath id="cloudSlideClip" clipPathUnits="objectBoundingBox">
            <path d="M 1,0 L 0.58,0 C 0.52,0.06 0.46,0.14 0.42,0.22 C 0.38,0.28 0.40,0.36 0.32,0.44 C 0.24,0.52 0.26,0.60 0.20,0.68 C 0.14,0.76 0.10,0.86 0,1 L 1,1 Z" />
        </clipPath>
    </defs>
</svg>

<!-- FULL-PAGE PARALLAX & MOTION WRAPPER -->
<div class="page-wrapper" id="pageWrapper">

    <!-- Layer 1: Sky Layer (Langit - Paling Belakang) -->
    <div class="layer-sky" id="layerSky"></div>

    <!-- Layer 2: Cloud Layer (Awan - 3 Layer Bergerak Independen) -->
    <div class="layer-clouds-container" id="layerClouds">
        <div class="cloud-layer cloud-layer-1"></div>
        <div class="cloud-layer cloud-layer-2"></div>
        <div class="cloud-layer cloud-layer-3"></div>
    </div>

    <!-- Layer 3: Building Layer (Gedung BKPSDM PNG Transparan) -->
    <div class="layer-building" id="layerBuilding"></div>

    <!-- Layer 4: Dark Gradient Overlay Layer -->
    <div class="layer-dark-overlay"></div>

    <!-- Layer 5: Ambient Moving Smoke / Fog Layer (Belakang Login Card) -->
    <div class="layer-fog" id="layerFog">
        <div class="fog-cloud fog-cloud-1"></div>
        <div class="fog-cloud fog-cloud-2"></div>
        <div class="fog-cloud fog-cloud-3"></div>
    </div>

    <!-- Layer 6 & 7: Hero Content Layer & Login Card Layer -->
    <div class="container">
        <!-- Left Side: Hero Brand & Text Content -->
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

        <!-- Right Side: Login Card Layer -->
        <div class="right-side" id="rightSide">
            <!-- Floating Organic Mesh Cloud Gradient Backdrop -->
            <div class="mesh-gradient-container">
                <div class="mesh-blob mesh-blob-halo"></div>
                <div class="mesh-blob mesh-blob-top-right"></div>
                <div class="mesh-blob mesh-blob-bottom-right"></div>
                <div class="mesh-blob mesh-blob-left-flow"></div>
            </div>

            @include('livewire.auth.login-form')
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

<script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/gsap.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/ScrollTrigger.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/lenis@1.1.9/dist/lenis.min.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        gsap.registerPlugin(ScrollTrigger);

        // Initialize Lenis Smooth Scroll
        const lenis = new Lenis({
            duration: 1.2,
            easing: (t) => Math.min(1, 1.001 - Math.pow(2, -10 * t)),
            smoothTouch: true
        });

        function raf(time) {
            lenis.raf(time);
            requestAnimationFrame(raf);
        }
        requestAnimationFrame(raf);

        const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

        if (prefersReducedMotion) {
            gsap.set(['#layerSky', '#layerBuilding', '.stagger-brand', '.hero-title', '.hero-line', '.hero-desc-line', '#loginCard', '.stagger-item'], {
                opacity: 1, y: 0, x: 0, scale: 1, filter: 'blur(0px)'
            });
            return;
        }

        // ================= 1. OPENING ANIMATION TIMELINE =================
        const tl = gsap.timeline({ defaults: { ease: 'power3.out' } });

        // Gedung BKPSDM: Slide up dari bawah ke atas dengan efek fade in
        tl.fromTo('#layerBuilding', 
            { y: 160, opacity: 0 }, 
            { y: 0, opacity: 1, duration: 1.2, ease: 'power3.out' },
            0.05
        );

        // Logo Intro (Pemalang brand logo top left)
        tl.fromTo('.stagger-brand',
            { y: -20, opacity: 0 },
            { y: 0, opacity: 1, duration: 0.5 },
            "-=0.7"
        );

        // Hero Title ("360° Kinerja ASN")
        tl.fromTo('.hero-title',
            { x: -50, opacity: 0, filter: 'blur(6px)' },
            { x: 0, opacity: 1, filter: 'blur(0px)', duration: 0.6 },
            "-=0.4"
        );

        // Garis Kuning Accent
        tl.fromTo('.hero-line',
            { scaleX: 0, transformOrigin: 'left center' },
            { scaleX: 1, duration: 0.4 },
            "-=0.4"
        );

        // Description Intro (Staggered lines)
        tl.fromTo('.hero-desc-line',
            { x: -30, opacity: 0, filter: 'blur(3px)' },
            { x: 0, opacity: 1, filter: 'blur(0px)', duration: 0.5, stagger: 0.1 },
            "-=0.3"
        );

        // Login Card Intro
        tl.fromTo('#loginCard',
            { opacity: 0, y: 15 },
            { opacity: 1, y: 0, duration: 0.6 },
            0.15
        );

        // Inputs & Button Intro (Staggered items)
        tl.fromTo('.stagger-item',
            { opacity: 0, y: 10 },
            { opacity: 1, y: 0, duration: 0.4, stagger: 0.05 },
            0.25
        );

        // ================= 2. IDLE FLOATING CARD ANIMATION =================
        gsap.to('#loginCard', {
            y: -4,
            duration: 3,
            repeat: -1,
            yoyo: true,
            ease: 'sine.inOut',
            delay: 2.5
        });

        // ================= 3. SEAMLESS NATURAL CLOUD ANIMATIONS =================
        gsap.to('.cloud-layer-1', {
            xPercent: -50,
            ease: 'none',
            duration: 90,
            repeat: -1
        });
        gsap.to('.cloud-layer-1', {
            y: 5,
            duration: 6,
            repeat: -1,
            yoyo: true,
            ease: 'sine.inOut'
        });

        gsap.to('.cloud-layer-2', {
            xPercent: -50,
            ease: 'none',
            duration: 70,
            repeat: -1
        });
        gsap.to('.cloud-layer-2', {
            y: -4,
            duration: 5,
            repeat: -1,
            yoyo: true,
            ease: 'sine.inOut'
        });

        gsap.to('.cloud-layer-3', {
            xPercent: -35,
            ease: 'none',
            duration: 110,
            repeat: -1
        });
        gsap.to('.cloud-layer-3', {
            y: 3,
            duration: 7,
            repeat: -1,
            yoyo: true,
            ease: 'sine.inOut'
        });

        // ================= 4. AMBIENT FOG / SMOKE MOTION =================
        gsap.to('.fog-cloud-1', {
            x: 40,
            y: -25,
            scale: 1.12,
            opacity: 0.16,
            duration: 20,
            repeat: -1,
            yoyo: true,
            ease: 'sine.inOut'
        });

        gsap.to('.fog-cloud-2', {
            x: -30,
            y: 20,
            scale: 1.08,
            opacity: 0.12,
            duration: 24,
            repeat: -1,
            yoyo: true,
            ease: 'sine.inOut',
            delay: 2
        });

        gsap.to('.fog-cloud-3', {
            x: 20,
            y: -15,
            scale: 1.15,
            opacity: 0.18,
            duration: 18,
            repeat: -1,
            yoyo: true,
            ease: 'sine.inOut',
            delay: 4
        });

        // ================= 5. MOUSE PARALLAX DEPTH =================
        const isMobile = window.innerWidth <= 768;
        const mFactor = isMobile ? 0.25 : 1.0;

        const skyMX = gsap.quickTo('#layerSky', 'x', { duration: 0.8, ease: 'power2.out' });
        const skyMY = gsap.quickTo('#layerSky', 'y', { duration: 0.8, ease: 'power2.out' });

        const cloudMX = gsap.quickTo('#layerClouds', 'x', { duration: 0.7, ease: 'power2.out' });
        const cloudMY = gsap.quickTo('#layerClouds', 'y', { duration: 0.7, ease: 'power2.out' });

        window.addEventListener('mousemove', (e) => {
            const { innerWidth, innerHeight } = window;
            const mx = (e.clientX / innerWidth - 0.5) * 2;
            const my = (e.clientY / innerHeight - 0.5) * 2;

            skyMX(mx * 1 * mFactor);
            skyMY(my * 1 * mFactor);

            cloudMX(mx * 4 * mFactor);
            cloudMY(my * 4 * mFactor);
        });

        // ================= 6. SCROLL PARALLAX =================
        ScrollTrigger.create({
            trigger: '#pageWrapper',
            start: 'top top',
            end: 'bottom top',
            scrub: 0.5,
            onUpdate: (self) => {
                const progress = self.progress;
                const pFactor = isMobile ? 0.25 : 1.0;

                gsap.set('#layerSky', { y: progress * -30 * pFactor });
                gsap.set('#layerClouds', { y: progress * -52.5 * pFactor });
                gsap.set('#layerBuilding', { y: progress * -82.5 * pFactor });
                gsap.set('#leftContent', { y: progress * -150 * pFactor, opacity: 1 - progress * 0.4 });
                gsap.set('#rightSide', { y: progress * -20 * pFactor });
            }
        });
    });
</script>

</body>
</html>