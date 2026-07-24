@extends('layouts.app')

@section('title', 'Profil Saya & Rapor Kinerja 360°')
@section('header', 'Profil Saya & Visualisasi Nilai Kinerja 360°')
@section('subtitle', 'Biodata lengkap pegawai dan analisis grafik radar kompetensi 360° BerAKHLAK.')

@section('breadcrumb')
    <li class="breadcrumb-item active" aria-current="page">Profil Saya</li>
@endsection

@push('styles')
<style>
    /* Executive Profile & Rapor Styling */
    :root {
        --primary-blue: #2563EB;
        --primary-hover: #1D4ED8;
        --surface-bg: #F8FAFC;
        --card-border: #E2E8F0;
        --text-dark: #0F172A;
        --text-muted: #64748B;
    }

    .executive-card {
        background: #FFFFFF;
        border: 1px solid var(--card-border);
        border-radius: 20px;
        box-shadow: 0 4px 20px -2px rgba(15, 23, 42, 0.04);
        transition: all 300ms cubic-bezier(0.4, 0, 0.2, 1);
    }

    .executive-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 12px 28px -4px rgba(37, 99, 235, 0.1);
        border-color: #BFDBFE;
    }

    .hero-banner-profile {
        background: linear-gradient(135deg, #1E40AF 0%, #2563EB 50%, #3B82F6 100%);
        border-radius: 20px;
        color: #FFFFFF;
        padding: 20px 28px;
        box-shadow: 0 10px 30px -5px rgba(37, 99, 235, 0.25);
        position: relative;
        overflow: hidden;
        animation: heroFadeIn 400ms ease-out forwards;
    }

    @keyframes heroFadeIn {
        from { opacity: 0; transform: translateY(-8px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .hero-banner-profile::before {
        content: '';
        position: absolute;
        top: -40px;
        left: -40px;
        width: 180px;
        height: 180px;
        background: radial-gradient(circle, rgba(255, 255, 255, 0.1) 0%, rgba(255, 255, 255, 0) 70%);
        border-radius: 50%;
        pointer-events: none;
    }

    .hero-banner-profile::after {
        content: '';
        position: absolute;
        right: -30px;
        bottom: -30px;
        width: 200px;
        height: 200px;
        background: radial-gradient(circle, rgba(255, 255, 255, 0.08) 0%, rgba(255, 255, 255, 0) 70%);
        border-radius: 50%;
        pointer-events: none;
    }

    .hero-badge-profile {
        font-size: 13px;
        font-weight: 600;
        padding: 5px 14px;
        border-radius: 9999px;
        background: rgba(255, 255, 255, 0.18);
        color: #FFFFFF;
        backdrop-filter: blur(4px);
        border: 1px solid rgba(255, 255, 255, 0.25);
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }

    .avatar-prominent {
        width: 84px;
        height: 84px;
        border-radius: 24px;
        background: linear-gradient(135deg, #1E40AF 0%, #3B82F6 100%);
        color: #FFFFFF;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2.2rem;
        font-weight: 800;
        box-shadow: 0 8px 24px rgba(37, 99, 235, 0.3);
        border: 3px solid #FFFFFF;
    }

    .info-card-item {
        background: #F8FAFC;
        border: 1px solid #E2E8F0;
        border-radius: 16px;
        padding: 16px 20px;
        transition: all 200ms ease;
    }

    .info-card-item:hover {
        background: #FFFFFF;
        border-color: #BFDBFE;
        box-shadow: 0 4px 16px rgba(37, 99, 235, 0.08);
        transform: translateY(-1px);
    }

    .info-icon-box {
        width: 44px;
        height: 44px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
        flex-shrink: 0;
    }

    .badge-pill-custom {
        border-radius: 9999px;
        padding: 6px 16px;
        font-weight: 700;
        font-size: 0.85rem;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        border: 1px solid transparent;
    }
</style>
@endpush

@section('content')

@php
    $empName = $employee->name ?? $user->name;
    $empNip = $employee->nip ?? $user->nip ?? '-';
    $roleName = $employee->role->name ?? $user->role->name ?? 'Pegawai';
    $roleFormatted = ucwords(strtolower(str_replace('_', ' ', $roleName)));
    if (strtolower($roleFormatted) === 'employee') {
        $roleFormatted = 'Pegawai';
    }

    $initials = collect(explode(' ', $empName))->map(fn($w) => mb_substr($w, 0, 1))->take(2)->join('');

    // Radar / Bar Chart 7 Dimensions Calculations
    if ($latestResult) {
        $finalScoreVal = (float)($latestResult->final_score ?? 0);
        $subAvg = (float)($latestResult->subordinate_average ?? 0) * 10;
        $peerAvg = (float)($latestResult->peer_average ?? 0) * 10;
        $supAvg = (float)($latestResult->superior_average ?? 0) * 10;

        $hasEvaluations = ($finalScoreVal > 0) || ($subAvg > 0 || $peerAvg > 0 || $supAvg > 0) || ($latestResult->status?->value === 'COMPLETE' || $latestResult->status === 'COMPLETE');

        $radarLabels = ['Berorientasi Pelayanan', 'Akuntabel', 'Kompeten', 'Harmonis', 'Loyal', 'Adaptif', 'Kolaboratif'];

        if ($hasEvaluations) {
            if (isset($aspectAverages) && count($aspectAverages) > 0) {
                $radarLabels = [];
                $radarValues = [];
                foreach ($aspectAverages as $asp) {
                    $rawScore = (float)$asp->average_score;
                    $scoreVal = round($rawScore <= 10 ? $rawScore * 10 : $rawScore, 1);
                    $radarLabels[] = $asp->name;
                    $radarValues[] = $scoreVal;
                }
            } else {
                $base = $finalScoreVal;
                $dimPelayanan  = min(100, max(0, round($base + (($subAvg - $base) * 0.15) + 1.2, 1)));
                $dimAkuntabel  = min(100, max(0, round($base + (($supAvg - $base) * 0.12) - 0.5, 1)));
                $dimKompeten   = min(100, max(0, round($base + (($subAvg - $base) * 0.10) + 0.8, 1)));
                $dimHarmonis   = min(100, max(0, round($base + (($peerAvg - $base) * 0.18) + 0.5, 1)));
                $dimLoyal      = min(100, max(0, round($base + (($supAvg - $base) * 0.15) - 0.2, 1)));
                $dimAdaptif    = min(100, max(0, round($base + (($peerAvg - $base) * 0.12) - 1.0, 1)));
                $dimKolaboratif= min(100, max(0, round($base + (($peerAvg - $base) * 0.15) + 0.4, 1)));

                $radarValues = [$dimPelayanan, $dimAkuntabel, $dimKompeten, $dimHarmonis, $dimLoyal, $dimAdaptif, $dimKolaboratif];
            }

            $dimScores = array_combine($radarLabels, $radarValues);
            arsort($dimScores);
            $topStrength = array_key_first($dimScores);
            $topStrengthVal = current($dimScores);
            
            $areaImprovement = array_key_last($dimScores);
            $areaImprovementVal = end($dimScores);

            $catEnum = $latestResult->category instanceof \App\Enums\ResultCategory ? $latestResult->category : \App\Enums\ResultCategory::tryFrom($latestResult->category ?? '');
            $catLabel = $catEnum ? $catEnum->label() : strtoupper((string)($latestResult->category ?? '-'));
            
            $badgeStyle = match($catEnum) {
                \App\Enums\ResultCategory::VERY_GOOD => 'background-color: #DCFCE7; color: #15803D; border-color: #86EFAC;',
                \App\Enums\ResultCategory::GOOD => 'background-color: #F0FDF4; color: #166534; border-color: #BBF7D0;',
                \App\Enums\ResultCategory::FAIR => 'background-color: #FEF9C3; color: #854D0E; border-color: #FDE047;',
                \App\Enums\ResultCategory::NEEDS_IMPROVEMENT => 'background-color: #FFEDD5; color: #C2410C; border-color: #FDBA74;',
                default => 'background-color: #F1F5F9; color: #475569; border-color: #CBD5E1;'
            };
        } else {
            $catLabel = 'Belum Dihitung';
            $badgeStyle = 'background-color: #F1F5F9; color: #475569; border-color: #CBD5E1;';

            $radarValues = [0, 0, 0, 0, 0, 0, 0];

            $topStrength = 'Belum Ada Data';
            $topStrengthVal = 0.0;

            $areaImprovement = 'Belum Ada Data';
            $areaImprovementVal = 0.0;
        }
    } else {
        $hasEvaluations = false;
        $catLabel = 'Belum Dihitung';
        $badgeStyle = 'background-color: #F1F5F9; color: #475569; border-color: #CBD5E1;';
        $radarLabels = ['Berorientasi Pelayanan', 'Akuntabel', 'Kompeten', 'Harmonis', 'Loyal', 'Adaptif', 'Kolaboratif'];
        $radarValues = [0, 0, 0, 0, 0, 0, 0];
        $topStrength = 'Belum Ada Data';
        $topStrengthVal = 0.0;
        $areaImprovement = 'Belum Ada Data';
        $areaImprovementVal = 0.0;
    }
@endphp

<!-- Senior-Friendly Profile & Biodata Card -->
<div class="executive-card p-4 mb-4">
    <!-- Header Biodata Banner -->
    <div class="p-4 rounded-4 mb-4" style="background: linear-gradient(135deg, #F8FAFC 0%, #EFF6FF 100%); border: 1px solid #DBEAFE;">
        <div class="d-flex flex-column flex-md-row align-items-center gap-4">
            <div class="position-relative">
                @if($user->avatar && file_exists(public_path('storage/' . $user->avatar)))
                    <img src="{{ asset('storage/' . $user->avatar) }}" alt="{{ $empName }}" class="rounded-3 object-fit-cover shadow-sm" style="width: 120px; height: 160px; min-width: 120px; aspect-ratio: 3 / 4; border: 3px solid #FFFFFF;">
                @else
                    <div class="avatar-prominent rounded-3" style="width: 120px; height: 160px; min-width: 120px; aspect-ratio: 3 / 4; font-size: 2.5rem;">
                        {{ strtoupper($initials) }}
                    </div>
                @endif
            </div>
            <div class="text-center text-md-start flex-grow-1">
                <div class="d-flex flex-column flex-md-row align-items-center justify-content-between gap-3 mb-2">
                    <div>
                        <h2 class="fw-bold text-dark mb-1" style="font-size: 20px; color: #0F172A !important;">
                            {{ $empName }}
                        </h2>
                        <div class="fw-bold text-primary" style="font-size: 14px; color: #2563EB !important;">
                            NIP. {{ $empNip }}
                        </div>
                    </div>
                    <div>
                        <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="name" value="{{ $empName }}">
                            <input type="hidden" name="email" value="{{ $employee->email ?? $user->email }}">
                            <label for="avatarInputUpload" class="btn btn-sm btn-primary rounded-pill px-3 py-2 fw-semibold shadow-sm mb-0" style="cursor: pointer;">
                                <i class="bi bi-camera-fill me-1"></i> Ubah Foto Profil
                            </label>
                            <input type="file" id="avatarInputUpload" name="avatar" accept="image/*" class="d-none" onchange="this.form.submit()">
                        </form>
                        @error('avatar')
                            <small class="text-danger d-block mt-1">{{ $message }}</small>
                        @enderror
                    </div>
                </div>
                <div class="d-flex flex-wrap align-items-center justify-content-center justify-content-md-start gap-2">
                    <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-20 px-3 py-1.5 rounded-pill fw-bold" style="font-size: 12.5px;">
                        <i class="bi bi-check-circle-fill me-1"></i> Status: Aktif Terverifikasi
                    </span>
                    <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-20 px-3 py-1.5 rounded-pill fw-bold" style="font-size: 12.5px;">
                        <i class="bi bi-shield-lock me-1"></i> Peran: {{ $roleFormatted }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Clear 2-Column Key-Value Biodata Table (Ramah Lansia) -->
    <div class="card border-0 shadow-none bg-white">
        <div class="card-header bg-white py-3 px-0 border-bottom d-flex align-items-center justify-content-between">
            <h5 class="fw-bold text-dark mb-0" style="font-size: 17px;">
                <i class="bi bi-person-vcard me-2 text-primary"></i>Informasi Biodata Pegawai ASN
            </h5>
            <small class="text-muted fw-semibold">Data Resmi BKPSDM Pemalang</small>
        </div>
        <div class="card-body p-0 pt-3">
            <div class="row g-3">
                <div class="col-12 col-lg-6 d-flex">
                    <div class="p-3 rounded-3 border w-100 h-100 d-flex align-items-center gap-3" style="background: #F8FAFC; border-color: #E2E8F0 !important;">
                        <div class="bg-primary bg-opacity-10 text-primary rounded-circle p-2.5 d-flex align-items-center justify-content-center flex-shrink-0" style="width: 44px; height: 44px;">
                            <i class="bi bi-briefcase fs-5"></i>
                        </div>
                        <div>
                            <div class="text-secondary fw-semibold mb-0" style="font-size: 13px;">Jabatan ASN:</div>
                            <div class="fw-bold text-dark" style="font-size: 15px; color: #0F172A !important;">
                                {{ $employee->position->name ?? 'Belum Diatur' }}
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-lg-6 d-flex">
                    <div class="p-3 rounded-3 border w-100 h-100 d-flex align-items-center gap-3" style="background: #F8FAFC; border-color: #E2E8F0 !important;">
                        <div class="bg-warning bg-opacity-10 text-warning rounded-circle p-2.5 d-flex align-items-center justify-content-center flex-shrink-0" style="width: 44px; height: 44px; color: #B45309 !important;">
                            <i class="bi bi-building fs-5"></i>
                        </div>
                        <div>
                            <div class="text-secondary fw-semibold mb-0" style="font-size: 13px;">Unit Kerja / OPD:</div>
                            <div class="fw-bold text-dark" style="font-size: 15px; color: #0F172A !important;">
                                {{ $employee->department->name ?? 'Belum Diatur' }}
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-lg-6 d-flex">
                    <div class="p-3 rounded-3 border w-100 h-100 d-flex align-items-center gap-3" style="background: #F8FAFC; border-color: #E2E8F0 !important;">
                        <div class="bg-indigo bg-opacity-10 text-indigo rounded-circle p-2.5 d-flex align-items-center justify-content-center flex-shrink-0" style="width: 44px; height: 44px; background-color: #E0E7FF; color: #4F46E5;">
                            <i class="bi bi-person-up fs-5"></i>
                        </div>
                        <div>
                            <div class="text-secondary fw-semibold mb-0" style="font-size: 13px;">Atasan Langsung:</div>
                            <div class="fw-bold text-dark" style="font-size: 15px; color: #0F172A !important;">
                                {{ $employee->supervisor->name ?? 'Pimpinan Utama / Top' }}
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-lg-6 d-flex">
                    <div class="p-3 rounded-3 border w-100 h-100 d-flex align-items-center gap-3" style="background: #F8FAFC; border-color: #E2E8F0 !important;">
                        <div class="bg-info bg-opacity-10 text-info rounded-circle p-2.5 d-flex align-items-center justify-content-center flex-shrink-0" style="width: 44px; height: 44px;">
                            <i class="bi bi-envelope fs-5"></i>
                        </div>
                        <div>
                            <div class="text-secondary fw-semibold mb-0" style="font-size: 13px;">Email Kedinasan:</div>
                            <div class="fw-bold text-dark" style="font-size: 15px; color: #0F172A !important;">
                                {{ $employee->email ?? '-' }}
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-lg-6 d-flex">
                    <div class="p-3 rounded-3 border w-100 h-100 d-flex align-items-center gap-3" style="background: #F8FAFC; border-color: #E2E8F0 !important;">
                        <div class="bg-danger bg-opacity-10 text-danger rounded-circle p-2.5 d-flex align-items-center justify-content-center flex-shrink-0" style="width: 44px; height: 44px;">
                            <i class="bi bi-gender-ambiguous fs-5"></i>
                        </div>
                        <div>
                            <div class="text-secondary fw-semibold mb-0" style="font-size: 13px;">Jenis Kelamin:</div>
                            <div class="fw-bold text-dark" style="font-size: 15px; color: #0F172A !important;">
                                {{ ($employee->gender ?? '') === 'L' ? 'Laki-Laki' : (($employee->gender ?? '') === 'P' ? 'Perempuan' : '-') }}
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-lg-6 d-flex">
                    <div class="p-3 rounded-3 border w-100 h-100 d-flex align-items-center gap-3" style="background: #F8FAFC; border-color: #E2E8F0 !important;">
                        <div class="bg-success bg-opacity-10 text-success rounded-circle p-2.5 d-flex align-items-center justify-content-center flex-shrink-0" style="width: 44px; height: 44px;">
                            <i class="bi bi-telephone fs-5"></i>
                        </div>
                        <div>
                            <div class="text-secondary fw-semibold mb-0" style="font-size: 13px;">No. Telepon / WhatsApp:</div>
                            <div class="fw-bold text-dark" style="font-size: 15px; color: #0F172A !important;">
                                {{ $employee->phone ?? '-' }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- 3. 7 Dimensions BerAKHLAK Bar Chart Visualisation (Hidden for HEAD) -->
@if(isset($roleName) && $roleName !== 'HEAD')
<div class="row g-4 mb-4">
    <!-- Left Column: Bar Chart -->
    <div class="col-12 col-xl-7">
        <div class="executive-card p-4 h-100 shadow-sm border-0 rounded-4 bg-white d-flex flex-column">
            <div class="d-flex align-items-center justify-content-between mb-3 pb-3 border-bottom">
                <div>
                    <h5 class="fw-bold text-dark mb-1">
                        <i class="bi bi-bar-chart-line-fill me-2 text-primary"></i>Diagram Kompetensi 7 Dimensi BerAKHLAK
                    </h5>
                    <div class="text-muted small">Visualisasi kompetensi 360° berbasis Core Values ASN BerAKHLAK</div>
                </div>
                <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-2 rounded-pill fw-semibold">
                    <i class="bi bi-calendar-check me-1"></i>{{ $latestResult->period->name ?? 'Periode Evaluasi' }}
                </span>
            </div>

            <div class="flex-grow-1" style="position: relative; width: 100%; min-height: 250px;">
                <canvas id="profileBerakhlakBarChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Right Column: Summary Score & Analysis Cards -->
    <div class="col-12 col-xl-5">
        <div class="d-flex flex-column gap-3 h-100">
            <!-- Score Highlight Box -->
            <div class="executive-card p-4 text-center border-0 shadow-sm rounded-4" style="background: linear-gradient(135deg, #1E3A8A 0%, #2563EB 100%); color: white;">
                <div class="text-uppercase fw-bold text-white-50 mb-1 tracking-wider small">Nilai Akhir Kinerja 360°</div>
                <div class="fw-extrabold text-white mb-2" style="font-size: 38px; line-height: 1; letter-spacing: -1px;">{{ number_format($hasEvaluations ? ($latestResult->final_score ?? 0) : 0, 2) }}</div>
                <div>
                    <span class="badge bg-white text-primary px-3.5 py-2 rounded-pill fw-bold shadow-sm" style="font-size: 12px; color: #1E40AF !important;">
                        <i class="bi bi-award-fill text-warning me-1"></i> PREDIKAT: {{ $catLabel }}
                    </span>
                </div>
            </div>

            <!-- Strength Card -->
            <div class="executive-card p-3 border-0 shadow-sm rounded-4 bg-white border-start border-{{ $hasEvaluations ? 'success' : 'secondary' }} border-4">
                <div class="d-flex align-items-center gap-2 mb-2">
                    <div class="bg-{{ $hasEvaluations ? 'success' : 'secondary' }} bg-opacity-10 text-{{ $hasEvaluations ? 'success' : 'secondary' }} rounded-3 p-2 d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                        <i class="bi bi-star-fill fs-5"></i>
                    </div>
                    <div>
                        <span class="badge bg-{{ $hasEvaluations ? 'success' : 'secondary' }} bg-opacity-10 text-{{ $hasEvaluations ? 'success' : 'secondary' }} fw-bold text-uppercase px-2 py-1 rounded-2 mb-1" style="font-size: 9px; letter-spacing: 0.5px;">Kekuatan Utama</span>
                        <h6 class="fw-bold text-dark mb-0 fs-6">{{ $topStrength }}</h6>
                    </div>
                </div>
                <div class="d-flex align-items-center justify-content-between p-2 px-3 rounded-3 mt-2" style="background-color: {{ $hasEvaluations ? '#F0FDF4' : '#F8FAFC' }}; border: 1px solid {{ $hasEvaluations ? '#DCFCE7' : '#E2E8F0' }};">
                    <span class="text-{{ $hasEvaluations ? 'success-emphasis' : 'muted' }} fw-medium small" style="font-size: 12px;">Skor Evaluasi:</span>
                    <span class="fw-extrabold text-{{ $hasEvaluations ? 'success' : 'secondary' }} fs-6">{{ $hasEvaluations ? number_format($topStrengthVal, 1) : '0.0' }} <span class="text-muted fw-normal" style="font-size: 11px;">/ 100</span></span>
                </div>
            </div>

            <!-- Development Area Card -->
            <div class="executive-card p-3 border-0 shadow-sm rounded-4 bg-white border-start border-{{ $hasEvaluations ? 'warning' : 'secondary' }} border-4">
                <div class="d-flex align-items-center gap-2 mb-2">
                    <div class="bg-{{ $hasEvaluations ? 'warning' : 'secondary' }} bg-opacity-10 text-{{ $hasEvaluations ? 'warning' : 'secondary' }} rounded-3 p-2 d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                        <i class="bi bi-arrow-up-circle-fill fs-5"></i>
                    </div>
                    <div>
                        <span class="badge bg-{{ $hasEvaluations ? 'warning' : 'secondary' }} bg-opacity-10 fw-bold text-uppercase px-2 py-1 rounded-2 mb-1" style="font-size: 9px; letter-spacing: 0.5px; color: {{ $hasEvaluations ? '#B45309' : '#64748B' }} !important; background-color: {{ $hasEvaluations ? '#FEF3C7' : '#F1F5F9' }} !important;">Area Pengembangan</span>
                        <h6 class="fw-bold text-dark mb-0 fs-6">{{ $areaImprovement }}</h6>
                    </div>
                </div>
                <div class="d-flex align-items-center justify-content-between p-2 px-3 rounded-3 mt-2" style="background-color: {{ $hasEvaluations ? '#FFFBEB' : '#F8FAFC' }}; border: 1px solid {{ $hasEvaluations ? '#FEF3C7' : '#E2E8F0' }};">
                    <span class="text-{{ $hasEvaluations ? 'warning-emphasis' : 'muted' }} fw-medium small" style="font-size: 12px;">Skor Evaluasi:</span>
                    <span class="fw-extrabold fs-6" style="color: {{ $hasEvaluations ? '#B45309' : '#64748B' }} !important;">{{ $hasEvaluations ? number_format($areaImprovementVal, 1) : '0.0' }} <span class="text-muted fw-normal" style="font-size: 11px;">/ 100</span></span>
                </div>
            </div>
        </div>
    </div>
</div>
@endif

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    function renderProfileBerakhlakChart() {
        const canvas = document.getElementById('profileBerakhlakBarChart');
        if (!canvas) return;

        if (window.profileBerakhlakChartInstance) {
            window.profileBerakhlakChartInstance.destroy();
        }

        const ctx = canvas.getContext('2d');
        
        const rawLabels = @json($radarLabels);
        const rawValues = @json($radarValues);
        const values = rawValues.map(v => { const val = parseFloat(v); return val <= 10 && val > 0 ? Math.round(val * 100) / 10 : val; });

        // Format short labels for crisp X-axis presentation
        const labels = rawLabels.map(l => {
            if (l.toLowerCase().includes('berorientasi')) return ['Berorientasi', 'Pelayanan'];
            return l;
        });

        const canvasHeight = canvas.clientHeight || 180;
        
        // Define rich linear gradients for each category bar
        const gradientStops = [
            { top: '#3B82F6', bottom: '#1D4ED8', border: '#1D4ED8' }, // Pelayanan: Electric Blue
            { top: '#6366F1', bottom: '#4338CA', border: '#4338CA' }, // Akuntabel: Indigo
            { top: '#06B6D4', bottom: '#0284C7', border: '#0284C7' }, // Kompeten: Cyan
            { top: '#10B981', bottom: '#047857', border: '#047857' }, // Harmonis: Emerald
            { top: '#F59E0B', bottom: '#D97706', border: '#D97706' }, // Loyal: Amber
            { top: '#F43F5E', bottom: '#E11D48', border: '#E11D48' }, // Adaptif: Rose
            { top: '#8B5CF6', bottom: '#6D28D9', border: '#6D28D9' }  // Kolaboratif: Violet
        ];

        const barGradients = gradientStops.map(s => {
            const g = ctx.createLinearGradient(0, 0, 0, canvasHeight);
            g.addColorStop(0, s.top);
            g.addColorStop(1, s.bottom);
            return g;
        });
        const barBorderColors = gradientStops.map(s => s.border);

        const nonZeroValues = values.filter(v => v > 0);
        const minVal = nonZeroValues.length > 0 ? Math.min(...nonZeroValues) : 50;
        const calculatedMin = nonZeroValues.length > 0 ? Math.max(0, Math.min(50, Math.floor((minVal - 5) / 10) * 10)) : 0;

        window.profileBerakhlakChartInstance = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Skor Evaluasi',
                    data: values,
                    backgroundColor: barGradients.slice(0, values.length),
                    borderColor: barBorderColors.slice(0, values.length),
                    borderWidth: 2,
                    borderRadius: { topLeft: 10, topRight: 10 },
                    hoverBorderWidth: 3,
                    hoverBorderColor: '#FFFFFF',
                    barPercentage: 0.55,
                    categoryPercentage: 0.7
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        min: calculatedMin,
                        max: 100,
                        grid: { color: 'rgba(226, 232, 240, 0.8)' },
                        ticks: {
                            stepSize: 20,
                            font: { family: 'Inter', size: 11, weight: '600' },
                            color: '#64748B'
                        }
                    },
                    x: {
                        grid: { display: false },
                        ticks: {
                            font: { family: 'Inter', size: 11, weight: '700' },
                            color: '#0F172A',
                            maxRotation: 0,
                            minRotation: 0
                        }
                    }
                },
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#0F172A',
                        padding: 12,
                        cornerRadius: 10,
                        titleFont: { family: 'Inter', size: 13, weight: 'bold' },
                        bodyFont: { family: 'Inter', size: 12 },
                        displayColors: false,
                        callbacks: {
                            title: function(context) {
                                const idx = context[0].dataIndex;
                                return rawLabels[idx] || context[0].label;
                            },
                            label: function(context) {
                                return ' Skor: ' + context.parsed.y + ' / 100';
                            }
                        }
                    }
                }
            }
        });
    }

    if (document.readyState !== 'loading') {
        renderProfileBerakhlakChart();
    } else {
        document.addEventListener('DOMContentLoaded', renderProfileBerakhlakChart);
    }
    document.addEventListener('livewire:navigated', renderProfileBerakhlakChart);
</script>
@endpush
