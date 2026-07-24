<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>360° Kinerja ASN - BKPSDM Kabupaten Pemalang</title>

    <!-- Favicon Logo Pemalang -->
    <link rel="icon" type="image/png" href="{{ asset('images/pemalang-shield.png') }}">
    <link rel="shortcut icon" href="{{ asset('images/pemalang-shield.png') }}">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                    },
                    colors: {
                        primary: '#1D4ED8',
                        secondary: '#2563EB',
                        accent: '#60A5FA',
                        surface: 'rgba(255, 255, 255, 0.75)',
                        dark: '#0F172A',
                        grayCustom: '#64748B',
                        success: '#22C55E',
                        warning: '#F59E0B',
                        danger: '#DC2626',
                    },
                    animation: {
                        'float': 'float 6s ease-in-out infinite',
                        'float-slow': 'float 10s ease-in-out infinite',
                        'blob': 'blob 7s infinite',
                        'fade-up': 'fadeUp 0.8s ease-out forwards',
                    },
                    keyframes: {
                        float: {
                            '0%, 100%': { transform: 'translateY(0)' },
                            '50%': { transform: 'translateY(-20px)' },
                        },
                        blob: {
                            '0%': { transform: 'translate(0px, 0px) scale(1)' },
                            '33%': { transform: 'translate(30px, -50px) scale(1.1)' },
                            '66%': { transform: 'translate(-20px, 20px) scale(0.9)' },
                            '100%': { transform: 'translate(0px, 0px) scale(1)' },
                        },
                        fadeUp: {
                            '0%': { opacity: '0', transform: 'translateY(30px)' },
                            '100%': { opacity: '1', transform: 'translateY(0)' },
                        }
                    }
                }
            }
        }
    </script>

    <!-- Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>

    <!-- Custom Styles -->
    <style>
        body {
            background-color: #F8FAFC;
            color: #0F172A;
            overflow-x: hidden;
        }

        .glass-panel {
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.5);
            box-shadow: 0 4px 30px rgba(0, 0, 0, 0.05);
        }

        .mesh-bg {
            background-color: #F8FAFC;
            background-image: 
                radial-gradient(at 0% 0%, hsla(213,100%,75%,0.3) 0px, transparent 50%),
                radial-gradient(at 100% 0%, hsla(213,100%,85%,0.3) 0px, transparent 50%),
                radial-gradient(at 100% 100%, hsla(213,100%,75%,0.3) 0px, transparent 50%),
                radial-gradient(at 0% 100%, hsla(213,100%,85%,0.3) 0px, transparent 50%);
        }

        .noise-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            pointer-events: none;
            z-index: 9999;
            opacity: 0.03;
            background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 200 200' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='noiseFilter'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.65' numOctaves='3' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23noiseFilter)'/%3E%3C/svg%3E");
        }

        .reveal-on-scroll {
            opacity: 0;
            transform: translateY(30px);
            transition: all 0.8s ease-out;
        }
        
        .reveal-on-scroll.is-visible {
            opacity: 1;
            transform: translateY(0);
        }

        @keyframes progressGrow {
            0% { width: 0%; opacity: 0.3; }
            100% { opacity: 1; }
        }
        .progress-animate {
            animation: progressGrow 1.5s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        }

        .hover-card-glow {
            transition: all 0.35s ease;
        }
        .hover-card-glow:hover {
            transform: translateY(-6px);
            box-shadow: 0 20px 30px -10px rgba(37, 99, 235, 0.15), 0 10px 15px -5px rgba(0, 0, 0, 0.04);
            border-color: rgba(59, 130, 246, 0.4);
        }

        /* Mockup Dashboard */
        .mac-frame {
            border-radius: 12px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
            border: 1px solid rgba(255,255,255,0.2);
            overflow: hidden;
            background: white;
            position: relative;
        }
        .mac-header {
            background: #F1F5F9;
            height: 32px;
            display: flex;
            align-items: center;
            padding: 0 16px;
            border-bottom: 1px solid #E2E8F0;
        }
        .mac-dots {
            display: flex;
            gap: 6px;
        }
        .mac-dot {
            width: 12px; height: 12px; border-radius: 50%;
        }
        .dot-red { background: #FF5F56; }
        .dot-yellow { background: #FFBD2E; }
        .dot-green { background: #27C93F; }

        .hover-lift {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .hover-lift:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }

        .gradient-text {
            background: linear-gradient(135deg, #1D4ED8 0%, #3B82F6 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
    </style>
</head>
<body class="antialiased mesh-bg">
    <div class="noise-overlay"></div>

    <!-- Navbar -->
    <nav id="navbar" class="fixed w-full z-50 transition-all duration-300 glass-panel py-3">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center">
                <div class="flex items-center gap-3">
                    <img src="{{ asset('images/logo-pemalang.png') }}" alt="Logo Pemalang" class="h-10 w-10">
                    <div>
                        <h1 class="font-bold text-dark text-xl leading-none">360° <span class="text-primary">Kinerja ASN</span></h1>
                        <p class="text-[10px] text-grayCustom font-semibold uppercase tracking-wider">BKPSDM Pemalang</p>
                    </div>
                </div>
                
                <div class="hidden md:flex space-x-8">
                    <a href="#beranda" class="text-sm font-semibold text-dark hover:text-primary transition-colors">Beranda</a>
                    <a href="#tentang" class="text-sm font-semibold text-grayCustom hover:text-primary transition-colors">Tentang</a>
                    <a href="#fitur" class="text-sm font-semibold text-grayCustom hover:text-primary transition-colors">Fitur</a>
                    <a href="#berakhlak" class="text-sm font-semibold text-grayCustom hover:text-primary transition-colors">BerAKHLAK</a>
                    <a href="#faq" class="text-sm font-semibold text-grayCustom hover:text-primary transition-colors">FAQ</a>
                </div>

                <div>
                    <a wire:navigate href="{{ route('login') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-dark text-white text-sm font-medium rounded-full hover:bg-primary transition-colors shadow-lg shadow-blue-500/30 hover-lift">
                        <i data-lucide="log-in" class="w-4 h-4"></i>
                        Masuk
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section id="beranda" class="relative pt-24 pb-16 lg:pt-28 lg:pb-24 overflow-hidden">
        <!-- Background Blobs -->
        <div class="absolute top-0 -left-4 w-72 h-72 bg-blue-300 rounded-full mix-blend-multiply filter blur-2xl opacity-30 animate-blob"></div>
        <div class="absolute top-0 -right-4 w-72 h-72 bg-indigo-300 rounded-full mix-blend-multiply filter blur-2xl opacity-30 animate-blob" style="animation-delay: 2s"></div>
        <div class="absolute -bottom-8 left-20 w-72 h-72 bg-sky-300 rounded-full mix-blend-multiply filter blur-2xl opacity-30 animate-blob" style="animation-delay: 4s"></div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="grid lg:grid-cols-2 gap-12 items-center">
                <!-- Left Content -->
                <div class="reveal-on-scroll">
                    <h1 class="text-5xl lg:text-6xl font-extrabold tracking-tight text-dark mb-6 leading-[1.1]">
                        Evaluasi Kinerja <br/>
                        <span class="gradient-text">Objektif & Transparan</span>
                    </h1>
                    <p class="text-lg text-grayCustom mb-8 max-w-lg leading-relaxed">
                        Platform Penilaian Kinerja ASN berbasis 360 Degree Feedback dan Core Values BerAKHLAK untuk mewujudkan tata kelola pemerintahan yang berkelas dunia.
                    </p>
                    
                    <div class="flex flex-wrap gap-4 mb-10">
                        <a wire:navigate href="{{ route('login') }}" class="px-8 py-3.5 bg-primary text-white font-medium rounded-full hover:bg-secondary transition-all shadow-lg shadow-blue-500/40 hover-lift inline-flex items-center gap-2">
                            Masuk ke Aplikasi
                            <i data-lucide="arrow-right" class="w-4 h-4"></i>
                        </a>
                        <a href="#tentang" class="px-8 py-3.5 bg-white text-dark font-medium rounded-full border border-gray-200 hover:border-gray-300 transition-all hover-lift inline-flex items-center gap-2 shadow-sm">
                            Pelajari Sistem
                        </a>
                    </div>

                    <div class="flex flex-wrap gap-x-6 gap-y-3">
                        <div class="flex items-center gap-2 text-sm text-grayCustom font-medium">
                            <i data-lucide="check-circle-2" class="w-4 h-4 text-success"></i> Berbasis BerAKHLAK
                        </div>
                        <div class="flex items-center gap-2 text-sm text-grayCustom font-medium">
                            <i data-lucide="check-circle-2" class="w-4 h-4 text-success"></i> 360 Degree Feedback
                        </div>
                        <div class="flex items-center gap-2 text-sm text-grayCustom font-medium">
                            <i data-lucide="check-circle-2" class="w-4 h-4 text-success"></i> Digital Assessment
                        </div>
                    </div>
                </div>

                <!-- Right Content (Dashboard Mockup) -->
                <div class="relative lg:h-[600px] flex items-center justify-center reveal-on-scroll">
                    <!-- Main Dashboard Frame -->
                    <div class="mac-frame w-full max-w-2xl transform lg:rotate-[-2deg] animate-float z-10">
                        <div class="mac-header">
                            <div class="mac-dots">
                                <div class="mac-dot dot-red"></div>
                                <div class="mac-dot dot-yellow"></div>
                                <div class="mac-dot dot-green"></div>
                            </div>
                        </div>
                        <div class="p-6 bg-slate-50 h-80 lg:h-96">
                            <!-- Dummy Dashboard Content -->
                            <div class="flex justify-between items-center mb-6">
                                <div class="h-6 w-32 bg-slate-200 rounded animate-pulse"></div>
                                <div class="h-8 w-8 bg-blue-100 rounded-full"></div>
                            </div>
                            <div class="grid grid-cols-3 gap-4 mb-6">
                                <div class="h-24 bg-white rounded-xl shadow-sm border border-slate-100 p-4">
                                    <div class="h-3 w-16 bg-slate-200 rounded mb-2"></div>
                                    <div class="h-6 w-12 bg-primary rounded"></div>
                                </div>
                                <div class="h-24 bg-white rounded-xl shadow-sm border border-slate-100 p-4">
                                    <div class="h-3 w-16 bg-slate-200 rounded mb-2"></div>
                                    <div class="h-6 w-12 bg-success rounded"></div>
                                </div>
                                <div class="h-24 bg-white rounded-xl shadow-sm border border-slate-100 p-4">
                                    <div class="h-3 w-16 bg-slate-200 rounded mb-2"></div>
                                    <div class="h-6 w-12 bg-warning rounded"></div>
                                </div>
                            </div>
                            <div class="h-32 bg-white rounded-xl shadow-sm border border-slate-100 p-4">
                                <div class="flex gap-2 items-end h-full">
                                    <div class="w-full bg-blue-100 rounded-t h-[40%]"></div>
                                    <div class="w-full bg-blue-200 rounded-t h-[60%]"></div>
                                    <div class="w-full bg-blue-300 rounded-t h-[30%]"></div>
                                    <div class="w-full bg-blue-400 rounded-t h-[80%]"></div>
                                    <div class="w-full bg-blue-500 rounded-t h-[50%]"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Floating Card 1 -->
                    <div class="absolute -right-6 top-20 glass-panel p-4 rounded-xl shadow-xl z-20 animate-float-slow transform lg:rotate-[4deg]">
                        <div class="flex items-center gap-3">
                            <div class="bg-success/10 p-2 rounded-lg">
                                <i data-lucide="trending-up" class="w-5 h-5 text-success"></i>
                            </div>
                            <div>
                                <p class="text-xs text-grayCustom font-medium">Progress Penilaian</p>
                                <p class="text-sm font-bold text-dark">92.5% Selesai</p>
                            </div>
                        </div>
                    </div>

                    <!-- Floating Card 2 -->
                    <div class="absolute -left-6 bottom-32 glass-panel p-4 rounded-xl shadow-xl z-20 animate-float transform lg:rotate-[-4deg]" style="animation-delay: 1s;">
                        <div class="flex items-center gap-3">
                            <div class="bg-primary/10 p-2 rounded-lg">
                                <i data-lucide="award" class="w-5 h-5 text-primary"></i>
                            </div>
                            <div>
                                <p class="text-xs text-grayCustom font-medium">Hasil Akhir</p>
                                <p class="text-sm font-bold text-dark">Sangat Baik</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Trust Section -->
    <section class="py-10 border-y border-slate-200/60 bg-white/50 backdrop-blur-sm">
        <div class="max-w-7xl mx-auto px-4 text-center">
            <p class="text-sm font-semibold text-grayCustom uppercase tracking-widest mb-6">Dipercaya Sebagai Standar Penilaian ASN</p>
            <div class="flex flex-wrap justify-center items-center gap-8 md:gap-16 opacity-60 grayscale hover:grayscale-0 transition-all duration-500">
                <div class="flex items-center gap-2 font-bold text-xl"><i data-lucide="shield-check"></i> BKPSDM Pemalang</div>
                <div class="flex items-center gap-2 font-bold text-xl"><i data-lucide="fingerprint"></i> Digital Assessment</div>
                <div class="flex items-center gap-2 font-bold text-xl"><i data-lucide="heart-handshake"></i> Core Values BerAKHLAK</div>
                <div class="flex items-center gap-2 font-bold text-xl"><i data-lucide="users"></i> 360 Degree Feedback</div>
            </div>
        </div>
    </section>

    <!-- About Section -->
    <section id="tentang" class="py-24 relative">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto mb-16 reveal-on-scroll">
                <h2 class="text-3xl md:text-4xl font-bold text-dark mb-4">Apa itu 360° Kinerja ASN?</h2>
                <p class="text-lg text-grayCustom">Sistem Informasi Penilaian Kinerja ASN berbasis metode 360 Degree Feedback yang komprehensif, melibatkan berbagai sudut pandang untuk hasil yang adil dan objektif.</p>
            </div>

            <div class="grid md:grid-cols-3 gap-8 relative z-10">
                <div class="glass-panel p-8 rounded-2xl text-center hover-lift reveal-on-scroll">
                    <div class="w-16 h-16 mx-auto bg-blue-50 text-primary rounded-2xl flex items-center justify-center mb-6 transform rotate-3">
                        <i data-lucide="user-cog" class="w-8 h-8"></i>
                    </div>
                    <h3 class="text-xl font-bold text-dark mb-3">Atasan</h3>
                    <p class="text-sm text-grayCustom">Evaluasi langsung dari pimpinan untuk mengukur pencapaian target dan kapabilitas manajerial.</p>
                </div>
                
                <div class="glass-panel p-8 rounded-2xl text-center hover-lift reveal-on-scroll" style="transition-delay: 100ms;">
                    <div class="w-16 h-16 mx-auto bg-green-50 text-success rounded-2xl flex items-center justify-center mb-6 transform -rotate-3">
                        <i data-lucide="users-2" class="w-8 h-8"></i>
                    </div>
                    <h3 class="text-xl font-bold text-dark mb-3">Rekan Kerja</h3>
                    <p class="text-sm text-grayCustom">Penilaian sejawat untuk mengukur aspek kolaborasi, harmonisasi, dan etika kerja sehari-hari.</p>
                </div>

                <div class="glass-panel p-8 rounded-2xl text-center hover-lift reveal-on-scroll" style="transition-delay: 200ms;">
                    <div class="w-16 h-16 mx-auto bg-amber-50 text-warning rounded-2xl flex items-center justify-center mb-6 transform rotate-3">
                        <i data-lucide="user-check" class="w-8 h-8"></i>
                    </div>
                    <h3 class="text-xl font-bold text-dark mb-3">Bawahan</h3>
                    <p class="text-sm text-grayCustom">Feedback dari subordinat (jika ada) untuk menilai efektivitas kepemimpinan dan pengayoman.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section id="fitur" class="py-24 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="mb-16 reveal-on-scroll">
                <span class="text-primary font-bold tracking-wider uppercase text-sm mb-2 block">Fitur Utama</span>
                <h2 class="text-3xl md:text-4xl font-bold text-dark">Platform Lengkap untuk<br/>Manajemen Kinerja</h2>
            </div>

            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
                <!-- Feature 1 -->
                <div class="p-8 rounded-2xl border border-slate-100 bg-slate-50 hover:bg-white hover:border-blue-100 hover:shadow-xl hover:shadow-blue-900/5 transition-all duration-300 hover-lift reveal-on-scroll">
                    <i data-lucide="scan" class="w-10 h-10 text-primary mb-5"></i>
                    <h3 class="text-xl font-bold text-dark mb-2">Penilaian 360°</h3>
                    <p class="text-sm text-grayCustom">Mekanisme penilaian menyeluruh dari atasan, rekan sejawat, hingga bawahan secara terstruktur.</p>
                </div>
                <!-- Feature 2 -->
                <div class="p-8 rounded-2xl border border-slate-100 bg-slate-50 hover:bg-white hover:border-blue-100 hover:shadow-xl hover:shadow-blue-900/5 transition-all duration-300 hover-lift reveal-on-scroll">
                    <i data-lucide="layout-dashboard" class="w-10 h-10 text-primary mb-5"></i>
                    <h3 class="text-xl font-bold text-dark mb-2">Dashboard Analitik</h3>
                    <p class="text-sm text-grayCustom">Visualisasi data dan ringkasan skor secara real-time untuk memantau perkembangan penilaian.</p>
                </div>
                <!-- Feature 3 -->
                <div class="p-8 rounded-2xl border border-slate-100 bg-slate-50 hover:bg-white hover:border-blue-100 hover:shadow-xl hover:shadow-blue-900/5 transition-all duration-300 hover-lift reveal-on-scroll">
                    <i data-lucide="users" class="w-10 h-10 text-primary mb-5"></i>
                    <h3 class="text-xl font-bold text-dark mb-2">Manajemen Pegawai</h3>
                    <p class="text-sm text-grayCustom">Pengelolaan data hierarki pegawai (atasan-bawahan) yang dinamis dan mudah disesuaikan.</p>
                </div>
                <!-- Feature 4 -->
                <div class="p-8 rounded-2xl border border-slate-100 bg-slate-50 hover:bg-white hover:border-blue-100 hover:shadow-xl hover:shadow-blue-900/5 transition-all duration-300 hover-lift reveal-on-scroll">
                    <i data-lucide="heart-handshake" class="w-10 h-10 text-primary mb-5"></i>
                    <h3 class="text-xl font-bold text-dark mb-2">Penilaian BerAKHLAK</h3>
                    <p class="text-sm text-grayCustom">Indikator kuesioner yang didasarkan murni pada implementasi *Core Values* ASN BerAKHLAK.</p>
                </div>
                <!-- Feature 5 -->
                <div class="p-8 rounded-2xl border border-slate-100 bg-slate-50 hover:bg-white hover:border-blue-100 hover:shadow-xl hover:shadow-blue-900/5 transition-all duration-300 hover-lift reveal-on-scroll">
                    <i data-lucide="file-bar-chart-2" class="w-10 h-10 text-primary mb-5"></i>
                    <h3 class="text-xl font-bold text-dark mb-2">Laporan Otomatis</h3>
                    <p class="text-sm text-grayCustom">Generate laporan PDF / Excel hasil penilaian individu dengan satu klik.</p>
                </div>
                <!-- Feature 6 -->
                <div class="p-8 rounded-2xl border border-slate-100 bg-slate-50 hover:bg-white hover:border-blue-100 hover:shadow-xl hover:shadow-blue-900/5 transition-all duration-300 hover-lift reveal-on-scroll">
                    <i data-lucide="activity" class="w-10 h-10 text-primary mb-5"></i>
                    <h3 class="text-xl font-bold text-dark mb-2">Monitoring Progress</h3>
                    <p class="text-sm text-grayCustom">Lacak siapa yang belum mengisi penilaian untuk memastikan partisipasi 100%.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Mengapa Memilih -->
    <section class="py-24 relative overflow-hidden">
        <!-- Ambient Glowing Background Blobs -->
        <div class="absolute top-1/4 -left-20 w-96 h-96 bg-blue-300/30 rounded-full mix-blend-multiply filter blur-3xl opacity-50 animate-blob"></div>
        <div class="absolute bottom-10 -right-20 w-96 h-96 bg-indigo-300/30 rounded-full mix-blend-multiply filter blur-3xl opacity-50 animate-blob" style="animation-delay: 3.5s"></div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="grid lg:grid-cols-2 gap-16 items-center">
                <div class="reveal-on-scroll">
                    <h2 class="text-3xl md:text-4xl font-bold text-dark mb-6">Mengapa Memilih 360° Kinerja ASN?</h2>
                    <p class="text-lg text-grayCustom mb-8">Transformasi dari penilaian konvensional satu arah menjadi penilaian modern pelbagai dimensi (360 derajat) membawa perubahan signifikan.</p>
                    
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <div class="glass-panel p-6 rounded-xl hover-card-glow group">
                            <div class="w-10 h-10 bg-primary/10 text-primary rounded-lg flex items-center justify-center mb-4 group-hover:bg-primary group-hover:text-white group-hover:scale-110 transition-all duration-300 shadow-sm">
                                <i data-lucide="target" class="w-5 h-5"></i>
                            </div>
                            <h4 class="font-bold text-dark mb-1 group-hover:text-primary transition-colors">Objektif</h4>
                            <p class="text-sm text-grayCustom">Mengurangi bias subjektivitas dari satu penilai tunggal.</p>
                        </div>
                        <div class="glass-panel p-6 rounded-xl hover-card-glow group">
                            <div class="w-10 h-10 bg-indigo-50 text-indigo-600 rounded-lg flex items-center justify-center mb-4 group-hover:bg-indigo-600 group-hover:text-white group-hover:scale-110 transition-all duration-300 shadow-sm">
                                <i data-lucide="eye" class="w-5 h-5"></i>
                            </div>
                            <h4 class="font-bold text-dark mb-1 group-hover:text-indigo-600 transition-colors">Transparan</h4>
                            <p class="text-sm text-grayCustom">Proses dan rekam jejak yang tercatat jelas dalam sistem.</p>
                        </div>
                        <div class="glass-panel p-6 rounded-xl hover-card-glow group">
                            <div class="w-10 h-10 bg-amber-50 text-amber-600 rounded-lg flex items-center justify-center mb-4 group-hover:bg-amber-500 group-hover:text-white group-hover:scale-110 transition-all duration-300 shadow-sm">
                                <i data-lucide="zap" class="w-5 h-5"></i>
                            </div>
                            <h4 class="font-bold text-dark mb-1 group-hover:text-amber-600 transition-colors">Cepat</h4>
                            <p class="text-sm text-grayCustom">Sistem digital tanpa kertas (paperless) mempercepat rekapitulasi.</p>
                        </div>
                        <div class="glass-panel p-6 rounded-xl hover-card-glow group">
                            <div class="w-10 h-10 bg-emerald-50 text-emerald-600 rounded-lg flex items-center justify-center mb-4 group-hover:bg-emerald-600 group-hover:text-white group-hover:scale-110 transition-all duration-300 shadow-sm">
                                <i data-lucide="network" class="w-5 h-5"></i>
                            </div>
                            <h4 class="font-bold text-dark mb-1 group-hover:text-emerald-600 transition-colors">Terintegrasi</h4>
                            <p class="text-sm text-grayCustom">Data master pegawai yang terpusat dan mudah diakses.</p>
                        </div>
                    </div>
                </div>

                <div class="relative flex items-center justify-center reveal-on-scroll">
                    <div class="absolute inset-0 bg-gradient-to-tr from-blue-600/30 to-indigo-600/30 rounded-3xl blur-2xl opacity-60 animate-pulse"></div>
                    <div class="w-full relative z-10 rounded-3xl shadow-2xl border border-slate-200/80 bg-white/95 backdrop-blur-xl p-6 lg:p-8 space-y-6 animate-float">
                        <!-- Live Preview Card Header -->
                        <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl bg-blue-600/10 text-primary flex items-center justify-center font-bold">
                                    <i data-lucide="pie-chart" class="w-5 h-5 animate-spin-slow"></i>
                                </div>
                                <div>
                                    <h4 class="font-bold text-dark text-base">Matriks Penilaian 360°</h4>
                                    <p class="text-xs text-grayCustom">Sistem Evaluasi Multi-Perspektif</p>
                                </div>
                            </div>
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-50 text-emerald-600 text-xs font-semibold border border-emerald-100 shadow-sm">
                                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-ping"></span>
                                Realtime Assessment
                            </span>
                        </div>

                        <!-- 3 Assessment Sources Progress Bars -->
                        <div class="space-y-4">
                            <div class="group">
                                <div class="flex justify-between text-xs font-semibold mb-1.5">
                                    <span class="text-dark flex items-center gap-2 group-hover:text-blue-600 transition-colors">
                                        <i data-lucide="user-check" class="w-4 h-4 text-blue-600"></i> Penilaian Atasan Langsung
                                    </span>
                                    <span class="text-primary font-bold">96% (Sangat Baik)</span>
                                </div>
                                <div class="w-full bg-slate-100 rounded-full h-2.5 overflow-hidden p-0.5 shadow-inner">
                                    <div class="bg-gradient-to-r from-blue-600 via-indigo-600 to-blue-500 h-2 rounded-full progress-animate shadow-sm" style="width: 96%"></div>
                                </div>
                            </div>

                            <div class="group">
                                <div class="flex justify-between text-xs font-semibold mb-1.5">
                                    <span class="text-dark flex items-center gap-2 group-hover:text-indigo-600 transition-colors">
                                        <i data-lucide="users" class="w-4 h-4 text-indigo-600"></i> Penilaian Rekan Sejawat (Peers)
                                    </span>
                                    <span class="text-indigo-600 font-bold">92% (Sangat Baik)</span>
                                </div>
                                <div class="w-full bg-slate-100 rounded-full h-2.5 overflow-hidden p-0.5 shadow-inner">
                                    <div class="bg-gradient-to-r from-indigo-500 via-blue-500 to-indigo-600 h-2 rounded-full progress-animate shadow-sm" style="width: 92%; animation-delay: 200ms;"></div>
                                </div>
                            </div>

                            <div class="group">
                                <div class="flex justify-between text-xs font-semibold mb-1.5">
                                    <span class="text-dark flex items-center gap-2 group-hover:text-sky-600 transition-colors">
                                        <i data-lucide="user-minus" class="w-4 h-4 text-sky-600"></i> Penilaian Bawahan Langsung
                                    </span>
                                    <span class="text-sky-600 font-bold">94% (Sangat Baik)</span>
                                </div>
                                <div class="w-full bg-slate-100 rounded-full h-2.5 overflow-hidden p-0.5 shadow-inner">
                                    <div class="bg-gradient-to-r from-sky-400 via-blue-500 to-indigo-500 h-2 rounded-full progress-animate shadow-sm" style="width: 94%; animation-delay: 400ms;"></div>
                                </div>
                            </div>
                        </div>

                        <!-- Highlight Metric Cards Row -->
                        <div class="grid grid-cols-2 gap-3 pt-2">
                            <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-100 flex items-center gap-3 hover:-translate-y-1 hover:bg-blue-50/50 hover:border-blue-200 transition-all duration-300">
                                <div class="w-10 h-10 rounded-xl bg-blue-100 text-blue-700 flex items-center justify-center flex-shrink-0 shadow-sm">
                                    <i data-lucide="shield-check" class="w-5 h-5"></i>
                                </div>
                                <div>
                                    <div class="text-[11px] text-grayCustom font-medium">Standar Evaluasi</div>
                                    <div class="text-xs font-bold text-dark">PermenPANRB BerAKHLAK</div>
                                </div>
                            </div>
                            <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-100 flex items-center gap-3 hover:-translate-y-1 hover:bg-amber-50/50 hover:border-amber-200 transition-all duration-300">
                                <div class="w-10 h-10 rounded-xl bg-amber-100 text-amber-700 flex items-center justify-center flex-shrink-0 shadow-sm">
                                    <i data-lucide="award" class="w-5 h-5"></i>
                                </div>
                                <div>
                                    <div class="text-[11px] text-grayCustom font-medium">Tingkat Akurasi</div>
                                    <div class="text-xs font-bold text-dark">Multi-Rater Validated</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Core Values BerAKHLAK -->
    <section id="berakhlak" class="py-24 bg-dark text-white relative">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16 reveal-on-scroll">
                <span class="text-accent font-bold tracking-wider uppercase text-sm mb-2 block">Core Values</span>
                <h2 class="text-3xl md:text-4xl font-bold mb-4">Indikator BerAKHLAK</h2>
                <p class="text-slate-400 max-w-2xl mx-auto">Penilaian difokuskan pada 7 nilai dasar aparatur sipil negara yang tertuang dalam akronim BerAKHLAK.</p>
            </div>

            <div class="flex flex-wrap justify-center gap-4 reveal-on-scroll">
                <!-- Cards -->
                <div class="bg-white/5 border border-white/10 p-6 rounded-2xl hover:bg-white/10 transition-colors w-full sm:w-[calc(50%-1rem)] lg:w-[calc(25%-1rem)] hover-lift">
                    <i data-lucide="heart" class="w-8 h-8 text-accent mb-4"></i>
                    <h4 class="font-bold text-lg mb-2">Berorientasi Pelayanan</h4>
                    <p class="text-xs text-slate-400">Komitmen memberikan pelayanan prima demi kepuasan masyarakat.</p>
                </div>
                <div class="bg-white/5 border border-white/10 p-6 rounded-2xl hover:bg-white/10 transition-colors w-full sm:w-[calc(50%-1rem)] lg:w-[calc(25%-1rem)] hover-lift">
                    <i data-lucide="scale" class="w-8 h-8 text-accent mb-4"></i>
                    <h4 class="font-bold text-lg mb-2">Akuntabel</h4>
                    <p class="text-xs text-slate-400">Bertanggung jawab atas kepercayaan yang diberikan.</p>
                </div>
                <div class="bg-white/5 border border-white/10 p-6 rounded-2xl hover:bg-white/10 transition-colors w-full sm:w-[calc(50%-1rem)] lg:w-[calc(25%-1rem)] hover-lift">
                    <i data-lucide="award" class="w-8 h-8 text-accent mb-4"></i>
                    <h4 class="font-bold text-lg mb-2">Kompeten</h4>
                    <p class="text-xs text-slate-400">Terus belajar dan mengembangkan kapabilitas.</p>
                </div>
                <div class="bg-white/5 border border-white/10 p-6 rounded-2xl hover:bg-white/10 transition-colors w-full sm:w-[calc(50%-1rem)] lg:w-[calc(25%-1rem)] hover-lift">
                    <i data-lucide="users" class="w-8 h-8 text-accent mb-4"></i>
                    <h4 class="font-bold text-lg mb-2">Harmonis</h4>
                    <p class="text-xs text-slate-400">Saling peduli dan menghargai perbedaan.</p>
                </div>
                <div class="bg-white/5 border border-white/10 p-6 rounded-2xl hover:bg-white/10 transition-colors w-full sm:w-[calc(50%-1rem)] lg:w-[calc(25%-1rem)] hover-lift">
                    <i data-lucide="shield" class="w-8 h-8 text-accent mb-4"></i>
                    <h4 class="font-bold text-lg mb-2">Loyal</h4>
                    <p class="text-xs text-slate-400">Berdedikasi dan mengutamakan kepentingan bangsa dan negara.</p>
                </div>
                <div class="bg-white/5 border border-white/10 p-6 rounded-2xl hover:bg-white/10 transition-colors w-full sm:w-[calc(50%-1rem)] lg:w-[calc(25%-1rem)] hover-lift">
                    <i data-lucide="lightbulb" class="w-8 h-8 text-accent mb-4"></i>
                    <h4 class="font-bold text-lg mb-2">Adaptif</h4>
                    <p class="text-xs text-slate-400">Terus berinovasi dan antusias dalam menggerakkan perubahan.</p>
                </div>
                <div class="bg-white/5 border border-white/10 p-6 rounded-2xl hover:bg-white/10 transition-colors w-full sm:w-[calc(50%-1rem)] lg:w-[calc(25%-1rem)] hover-lift">
                    <i data-lucide="git-merge" class="w-8 h-8 text-accent mb-4"></i>
                    <h4 class="font-bold text-lg mb-2">Kolaboratif</h4>
                    <p class="text-xs text-slate-400">Membangun kerja sama yang sinergis.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Stats Section -->
    <section class="py-20 border-b border-slate-200 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-8">
                <div class="reveal-on-scroll">
                    <div class="text-4xl md:text-5xl font-extrabold text-primary mb-2"><span class="counter" data-target="360">0</span>°</div>
                    <div class="text-sm font-semibold text-grayCustom uppercase tracking-wide">Feedback System</div>
                </div>
                <div class="reveal-on-scroll" style="transition-delay: 100ms;">
                    <div class="text-4xl md:text-5xl font-extrabold text-primary mb-2"><span class="counter" data-target="7">0</span></div>
                    <div class="text-sm font-semibold text-grayCustom uppercase tracking-wide">Kategori Nilai</div>
                </div>
                <div class="reveal-on-scroll" style="transition-delay: 200ms;">
                    <div class="text-4xl md:text-5xl font-extrabold text-primary mb-2"><span class="counter" data-target="100">0</span>%</div>
                    <div class="text-sm font-semibold text-grayCustom uppercase tracking-wide">Digital & Paperless</div>
                </div>
                <div class="reveal-on-scroll" style="transition-delay: 300ms;">
                    <div class="text-4xl md:text-5xl font-extrabold text-primary mb-2"><span class="counter" data-target="3">0</span></div>
                    <div class="text-sm font-semibold text-grayCustom uppercase tracking-wide">Arah Penilaian</div>
                </div>
            </div>
            <p class="text-xs text-slate-400 mt-8">*Statistik di atas merupakan representasi konsep 360° Kinerja ASN</p>
        </div>
    </section>

    <!-- FAQ -->
    <section id="faq" class="py-24">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16 reveal-on-scroll">
                <h2 class="text-3xl font-bold text-dark mb-4">Pertanyaan yang Sering Diajukan</h2>
            </div>

            <div class="space-y-4 reveal-on-scroll">
                <!-- Accordion Item 1 -->
                <div class="glass-panel rounded-xl overflow-hidden">
                    <button class="w-full px-6 py-4 text-left font-semibold text-dark flex justify-between items-center focus:outline-none" onclick="toggleAccordion('faq1')">
                        <span>Apa itu 360 Degree Feedback?</span>
                        <i data-lucide="chevron-down" class="w-5 h-5 text-grayCustom transition-transform duration-300" id="icon-faq1"></i>
                    </button>
                    <div id="faq1" class="hidden px-6 pb-4 text-grayCustom text-sm">
                        360 Degree Feedback adalah metode evaluasi kinerja di mana umpan balik dikumpulkan dari berbagai arah: atasan langsung, rekan kerja sejawat, dan bawahan, sehingga memberikan gambaran yang menyeluruh dan objektif mengenai kinerja seseorang.
                    </div>
                </div>
                <!-- Accordion Item 2 -->
                <div class="glass-panel rounded-xl overflow-hidden">
                    <button class="w-full px-6 py-4 text-left font-semibold text-dark flex justify-between items-center focus:outline-none" onclick="toggleAccordion('faq2')">
                        <span>Apakah penilaian bersifat rahasia?</span>
                        <i data-lucide="chevron-down" class="w-5 h-5 text-grayCustom transition-transform duration-300" id="icon-faq2"></i>
                    </button>
                    <div id="faq2" class="hidden px-6 pb-4 text-grayCustom text-sm">
                        Sistem mencatat siapa yang memberikan penilaian untuk keperluan validitas (seperti batas 3 rekan kerja), namun pada laporan akhir target, nilai rata-rata akan diakumulasi per komponen sehingga target tidak dapat melihat skor spesifik dari tiap individu rekan kerjanya.
                    </div>
                </div>
                <!-- Accordion Item 3 -->
                <div class="glass-panel rounded-xl overflow-hidden">
                    <button class="w-full px-6 py-4 text-left font-semibold text-dark flex justify-between items-center focus:outline-none" onclick="toggleAccordion('faq3')">
                        <span>Bagaimana nilai akhir dihitung?</span>
                        <i data-lucide="chevron-down" class="w-5 h-5 text-grayCustom transition-transform duration-300" id="icon-faq3"></i>
                    </button>
                    <div id="faq3" class="hidden px-6 pb-4 text-grayCustom text-sm">
                        Nilai akhir dihitung berdasarkan pembobotan (Atasan 50%, Rekan 30%, Bawahan 20%). Mesin kalkulasi otomatis mengonversi skor (skala 1-10) menjadi skala 10-100 dan menentukan predikat akhir (Sangat Baik, Baik, Cukup, Kurang).
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="py-20">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 reveal-on-scroll">
            <div class="bg-gradient-to-r from-blue-700 to-indigo-800 rounded-3xl p-10 md:p-16 text-center text-white shadow-2xl relative overflow-hidden">
                <div class="absolute top-0 left-0 w-full h-full bg-white/5 opacity-50 blur-xl rounded-full transform -translate-y-1/2"></div>
                
                <h2 class="text-3xl md:text-5xl font-bold mb-6 relative z-10">Siap Melakukan Penilaian?</h2>
                <p class="text-blue-100 mb-10 max-w-2xl mx-auto relative z-10 text-lg">Masuk ke sistem sekarang menggunakan NIP Anda dan mulai berikan evaluasi yang objektif untuk rekan kerja di lingkungan BKPSDM Kabupaten Pemalang.</p>
                
                <div class="flex flex-wrap justify-center gap-4 relative z-10">
                    <a wire:navigate href="{{ route('login') }}" class="px-8 py-3.5 bg-white text-primary font-bold rounded-full hover:bg-blue-50 transition-colors shadow-lg hover-lift">
                        Masuk ke Aplikasi
                    </a>
                    <a href="#" class="px-8 py-3.5 bg-white/10 text-white font-medium rounded-full hover:bg-white/20 transition-colors border border-white/20 hover-lift">
                        Hubungi Admin
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-slate-900 text-slate-300 py-12 border-t border-slate-800 mt-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid md:grid-cols-3 gap-8">
                <div>
                    <div class="flex items-center gap-3 mb-6">
                        <img src="{{ asset('images/logo-pemalang.png') }}" alt="Logo Pemalang" class="h-12 w-auto object-contain">
                        <div>
                            <h2 class="font-bold text-white text-xl">360° <span class="text-accent">Kinerja ASN</span></h2>
                            <p class="text-[10px] text-slate-400 font-semibold uppercase tracking-wider">BKPSDM Pemalang</p>
                        </div>
                    </div>
                    <p class="text-sm text-slate-400 max-w-sm">Sistem Informasi Penilaian Kinerja ASN berbasis 360 Degree Feedback dan Core Values BerAKHLAK.</p>
                </div>
                <div>
                    <h3 class="text-white font-bold mb-4">Tautan Cepat</h3>
                    <ul class="space-y-2 text-sm">
                        <li><a href="#beranda" class="hover:text-white transition-colors">Beranda</a></li>
                        <li><a href="#tentang" class="hover:text-white transition-colors">Tentang Sistem</a></li>
                        <li><a href="#faq" class="hover:text-white transition-colors">FAQ</a></li>
                        <li><a href="{{ route('login') }}" class="hover:text-white transition-colors">Login Aplikasi</a></li>
                    </ul>
                </div>
                <div>
                    <h3 class="text-white font-bold mb-4">Hubungi Kami</h3>
                    <ul class="space-y-3 text-sm text-slate-400">
                        <li class="flex items-start gap-3">
                            <i data-lucide="map-pin" class="w-5 h-5 flex-shrink-0 text-slate-500"></i>
                            <span>Badan Kepegawaian Daerah Kabupaten Pemalang<br/>Jl. Surohadikusumo No.1, Pemalang</span>
                        </li>
                        <li class="flex items-center gap-3">
                            <i data-lucide="mail" class="w-5 h-5 text-slate-500"></i>
                            <span>admin@bkpsdm.pemalangkab.go.id</span>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="border-t border-slate-800 mt-12 pt-8 flex flex-col md:flex-row justify-between items-center gap-4">
                <p class="text-sm text-slate-500">&copy; {{ date('Y') }} BKPSDM Kabupaten Pemalang. Hak Cipta Dilindungi.</p>
                <div class="text-sm text-slate-500">
                    Versi 2.0.0
                </div>
            </div>
        </div>
    </footer>

    <!-- Scripts -->
    <script>
        // Initialize Lucide Icons
        lucide.createIcons();

        // Navbar Scroll Effect
        window.addEventListener('scroll', () => {
            const navbar = document.getElementById('navbar');
            if (window.scrollY > 20) {
                navbar.classList.add('shadow-sm', 'bg-white/80');
                navbar.classList.remove('py-3');
                navbar.classList.add('py-2');
            } else {
                navbar.classList.remove('shadow-sm', 'bg-white/80');
                navbar.classList.add('py-3');
                navbar.classList.remove('py-2');
            }
        });

        // Intersection Observer for Scroll Animations
        const observerOptions = {
            threshold: 0.1,
            rootMargin: "0px 0px -50px 0px"
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('is-visible');
                    // If it has counter, start counting
                    const counters = entry.target.querySelectorAll('.counter');
                    counters.forEach(counter => {
                        if (!counter.classList.contains('counted')) {
                            startCounter(counter);
                            counter.classList.add('counted');
                        }
                    });
                }
            });
        }, observerOptions);

        document.querySelectorAll('.reveal-on-scroll').forEach(el => {
            observer.observe(el);
        });

        // Counter Animation
        function startCounter(el) {
            const target = parseInt(el.getAttribute('data-target'));
            const duration = 2000; // ms
            const stepTime = Math.abs(Math.floor(duration / target));
            let current = 0;
            const timer = setInterval(() => {
                current += 1;
                el.innerText = current;
                if (current >= target) {
                    clearInterval(timer);
                    el.innerText = target;
                }
            }, stepTime);
        }

        // Accordion Logic
        function toggleAccordion(id) {
            const content = document.getElementById(id);
            const icon = document.getElementById('icon-' + id);
            if (content.classList.contains('hidden')) {
                content.classList.remove('hidden');
                icon.style.transform = 'rotate(180deg)';
            } else {
                content.classList.add('hidden');
                icon.style.transform = 'rotate(0deg)';
            }
        }
    </script>
</body>
</html>
