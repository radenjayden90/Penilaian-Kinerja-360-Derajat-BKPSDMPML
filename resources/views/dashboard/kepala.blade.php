@extends('layouts.app')

@section('title', 'Dashboard Eksekutif Kepala BKPSDM')
@section('header', 'Dashboard Eksekutif Kinerja ASN 360°')
@section('subtitle', 'Ikhtisar statistik, distribusi predikat, serta tren perkembangan kinerja ASN Kabupaten Pemalang')

@push('styles')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<style>
    .exec-metric-card {
        background: #FFFFFF;
        border: 1px solid #E2E8F0;
        border-radius: 18px;
        padding: 20px;
        box-shadow: 0 4px 18px -2px rgba(15, 23, 42, 0.04);
        transition: all 250ms cubic-bezier(0.4, 0, 0.2, 1);
    }
    .exec-metric-card:hover {
        box-shadow: 0 10px 25px -4px rgba(37, 99, 235, 0.1);
        border-color: #CBD5E1;
    }
    .exec-chart-card {
        background: #FFFFFF;
        border: 1px solid #E2E8F0;
        border-radius: 20px;
        box-shadow: 0 4px 20px -2px rgba(15, 23, 42, 0.04);
        height: 100%;
        display: flex;
        flex-direction: column;
    }
    .exec-chart-card .card-header-custom {
        padding: 16px 20px;
        border-bottom: 1px solid #F1F5F9;
        font-weight: 700;
        color: #0F172A;
        font-size: 14.5px;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    .exec-chart-card .card-body-custom {
        padding: 20px;
        flex-grow: 1;
    }
    .legend-item {
        padding: 8px 10px;
        border-radius: 10px;
        margin-bottom: 6px;
        font-size: 12px;
    }
</style>
@endpush

@section('content')

<!-- 1. BARIS PALING ATAS: 4 CARD METRIK UTAMA -->
<div class="row g-3 mb-4">
    <!-- Card 1: ASN Aktif -->
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="exec-metric-card">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <span class="text-muted fw-semibold small text-uppercase tracking-wider">ASN Aktif</span>
                <div class="kpi-icon-wrapper" style="background-color: #DBEAFE; color: #2563EB;">
                    <i class="bi bi-people-fill fs-5"></i>
                </div>
            </div>
            <div class="d-flex align-items-baseline gap-2">
                <h2 class="fw-extrabold text-dark mb-0 fs-1">{{ $totalAsnActive }}</h2>
                <span class="text-muted small">Pegawai</span>
            </div>
            <div class="mt-2 text-muted small"><i class="bi bi-building me-1"></i>BKPSDM Pemalang</div>
        </div>
    </div>

    <!-- Card 2: Pegawai Yang Sudah Dinilai -->
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="exec-metric-card">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <span class="text-muted fw-semibold small text-uppercase tracking-wider">Pegawai Sudah Dinilai</span>
                <div class="kpi-icon-wrapper" style="background-color: #DCFCE7; color: #16A34A;">
                    <i class="bi bi-person-check-fill fs-5"></i>
                </div>
            </div>
            <div class="d-flex align-items-baseline gap-2">
                <h2 class="fw-extrabold text-dark mb-0 fs-1">{{ $evaluatedEmployeesCount }}</h2>
                <span class="text-muted small">/ {{ $totalAsnActive }} ASN</span>
            </div>
            <div class="mt-2 text-emerald-600 small fw-medium">
                <i class="bi bi-check-circle-fill me-1"></i>Terevaluasi Periode Aktif
            </div>
        </div>
    </div>

    <!-- Card 3: Nilai Rata-Rata -->
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="exec-metric-card">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <span class="text-muted fw-semibold small text-uppercase tracking-wider">Nilai Rata-Rata Instansi</span>
                <div class="kpi-icon-wrapper" style="background-color: #FEF3C7; color: #D97706;">
                    <i class="bi bi-graph-up-arrow fs-5"></i>
                </div>
            </div>
            <div class="d-flex align-items-baseline gap-2">
                <h2 class="fw-extrabold text-dark mb-0 fs-1" style="color: #2563EB !important;">{{ number_format($averageScore, 2) }}</h2>
                <span class="text-muted small">/ 100</span>
            </div>
            <div class="mt-2 text-muted small"><i class="bi bi-star-fill text-warning me-1"></i>Akumulasi Evaluasi 360°</div>
        </div>
    </div>

    <!-- Card 4: Progress Penilaian Periode Berjalan -->
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="exec-metric-card">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <span class="text-muted fw-semibold small text-uppercase tracking-wider">Progress Penilaian</span>
                <div class="kpi-icon-wrapper" style="background-color: #F3E8FF; color: #9333EA;">
                    <i class="bi bi-pie-chart-fill fs-5"></i>
                </div>
            </div>
            <div class="d-flex align-items-baseline gap-2 mb-2">
                <h2 class="fw-extrabold text-dark mb-0 fs-1">{{ $assessmentProgressPct }}%</h2>
                <span class="text-muted small">Selesai</span>
            </div>
            <div class="progress rounded-pill mb-1" style="height: 7px;">
                <div class="progress-bar bg-purple" role="progressbar" style="width: {{ $assessmentProgressPct }}%; background-color: #9333EA;" aria-valuenow="{{ $assessmentProgressPct }}" aria-valuemin="0" aria-valuemax="100"></div>
            </div>
            <div class="text-muted small" style="font-size: 11.5px;">
                Periode: {{ $activePeriod->name ?? 'Aktif' }}
            </div>
        </div>
    </div>
</div>


<!-- 2. BARIS KEDUA: 3 CARD DIAGRAM (DONUT, BAR HORIZONTAL, LINE TREND) -->
<div class="row g-3 mb-4">
    <!-- Card 1: Distribusi Hasil Penilaian (Donut Chart + Legend Keterangan Nilai) -->
    <div class="col-12 col-lg-4">
        <div class="exec-chart-card">
            <div class="card-header-custom">
                <span><i class="bi bi-pie-chart-fill text-primary me-2"></i>Distribusi Hasil Penilaian</span>
                <span class="badge bg-light text-dark border">Donut Chart</span>
            </div>
            <div class="card-body-custom">
                <div class="row align-items-center">
                    <div class="col-12 col-sm-6 col-lg-6 mb-3 mb-sm-0 text-center">
                        <div style="position: relative; height: 170px; width: 170px; margin: 0 auto;">
                            <canvas id="doughnutCategoryChart"></canvas>
                        </div>
                    </div>
                    <div class="col-12 col-sm-6 col-lg-6">
                        <div class="legend-item bg-emerald-50 border border-emerald-200">
                            <div class="fw-bold text-emerald-800">Sangat Baik <small class="fw-normal text-muted">(90-100)</small></div>
                            <div class="d-flex justify-content-between align-items-center mt-1">
                                <span class="fw-extrabold text-emerald-600 fs-6">{{ $distribusiStats['sangat_baik']['count'] }} ASN</span>
                                <span class="badge bg-emerald-200 text-emerald-800">{{ $distribusiStats['sangat_baik']['pct'] }}%</span>
                            </div>
                        </div>

                        <div class="legend-item bg-blue-50 border border-blue-200">
                            <div class="fw-bold text-blue-800">Baik <small class="fw-normal text-muted">(76-89)</small></div>
                            <div class="d-flex justify-content-between align-items-center mt-1">
                                <span class="fw-extrabold text-blue-600 fs-6">{{ $distribusiStats['baik']['count'] }} ASN</span>
                                <span class="badge bg-blue-200 text-blue-800">{{ $distribusiStats['baik']['pct'] }}%</span>
                            </div>
                        </div>

                        <div class="legend-item bg-amber-50 border border-amber-200">
                            <div class="fw-bold text-amber-800">Cukup <small class="fw-normal text-muted">(61-75)</small></div>
                            <div class="d-flex justify-content-between align-items-center mt-1">
                                <span class="fw-extrabold text-amber-600 fs-6">{{ $distribusiStats['cukup']['count'] }} ASN</span>
                                <span class="badge bg-amber-200 text-amber-800">{{ $distribusiStats['cukup']['pct'] }}%</span>
                            </div>
                        </div>

                        <div class="legend-item bg-rose-50 border border-rose-200">
                            <div class="fw-bold text-rose-800">Perlu Pembinaan <small class="fw-normal text-muted">(&lt;60)</small></div>
                            <div class="d-flex justify-content-between align-items-center mt-1">
                                <span class="fw-extrabold text-rose-600 fs-6">{{ $distribusiStats['kurang']['count'] }} ASN</span>
                                <span class="badge bg-rose-200 text-rose-800">{{ $distribusiStats['kurang']['pct'] }}%</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Card 2: Rata-Rata Nilai Per Bidang (Horizontal Bar Chart) -->
    <div class="col-12 col-md-6 col-lg-4">
        <div class="exec-chart-card">
            <div class="card-header-custom">
                <span><i class="bi bi-bar-chart-steps text-success me-2"></i>Rata-Rata Nilai Per Bidang</span>
                <span class="badge bg-light text-dark border">Bar Menyamping</span>
            </div>
            <div class="card-body-custom">
                <div style="position: relative; height: 230px; width: 100%;">
                    <canvas id="barDepartmentChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Card 3: Trend Nilai Rata-Rata Instansi (Line Chart dengan Titik-Titik) -->
    <div class="col-12 col-md-6 col-lg-4">
        <div class="exec-chart-card">
            <div class="card-header-custom">
                <span><i class="bi bi-graph-up text-indigo me-2" style="color: #6366F1;"></i>Trend Rata-Rata Instansi</span>
                <span class="badge bg-light text-dark border">Line Chart</span>
            </div>
            <div class="card-body-custom">
                <div style="position: relative; height: 230px; width: 100%;">
                    <canvas id="lineTrendChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>


<!-- 3. BARIS KETIGA: 4 CARD EKSEKUTIF (HIGHEST BIDANG, LOWEST BIDANG, TOP 5 PEGAWAI, MEMERLUKAN PEMBINAAN) -->
<div class="row g-3 mb-4">
    <!-- Card 1: Nilai Tertinggi Per Bidang -->
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="exec-chart-card">
            <div class="card-header-custom bg-emerald-50 border-bottom-0 text-emerald-800 py-3">
                <span class="d-flex align-items-center gap-2">
                    <i class="bi bi-trophy-fill text-warning fs-5"></i> Nilai Tertinggi Per Bidang
                </span>
            </div>
            <div class="card-body-custom p-4 d-flex flex-column justify-content-between">
                @if($topDepartment)
                    <div>
                        <span class="badge bg-emerald-100 text-emerald-800 mb-2">Bidang Performa Terbaik</span>
                        <h5 class="fw-bold text-dark mb-1 lh-sm">{{ $topDepartment['name'] }}</h5>
                        <small class="text-muted d-block mb-3">Total Evaluasi: {{ $topDepartment['count'] }} Pegawai</small>
                    </div>
                    <div class="pt-3 border-top d-flex align-items-center justify-content-between">
                        <div>
                            <span class="text-muted small d-block">Nilai Rata-Rata</span>
                            <span class="fw-extrabold text-emerald-600 fs-3">{{ number_format($topDepartment['avg'], 2) }}</span>
                        </div>
                        <span class="badge bg-emerald-100 text-emerald-800 border border-emerald-300 px-3 py-2 fs-6 fw-bold">
                            {{ $topDepartment['category_label'] }}
                        </span>
                    </div>
                @else
                    <div class="text-center text-muted py-4">Belum ada data nilai per bidang</div>
                @endif
            </div>
        </div>
    </div>

    <!-- Card 2: Perlu Perhatian (Bidang Nilai Terendah) -->
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="exec-chart-card">
            <div class="card-header-custom bg-rose-50 border-bottom-0 text-rose-800 py-3">
                <span class="d-flex align-items-center gap-2">
                    <i class="bi bi-exclamation-triangle-fill text-rose-600 fs-5"></i> Perlu Perhatian
                </span>
            </div>
            <div class="card-body-custom p-4 d-flex flex-column justify-content-between">
                @if($lowestDepartment)
                    <div>
                        <span class="badge bg-rose-100 text-rose-800 mb-2">Bidang Butuh Perhatian</span>
                        <h5 class="fw-bold text-dark mb-1 lh-sm">{{ $lowestDepartment['name'] }}</h5>
                        <small class="text-muted d-block mb-3">Total Evaluasi: {{ $lowestDepartment['count'] }} Pegawai</small>
                    </div>
                    <div class="pt-3 border-top d-flex align-items-center justify-content-between">
                        <div>
                            <span class="text-muted small d-block">Nilai Rata-Rata</span>
                            <span class="fw-extrabold text-rose-600 fs-3">{{ number_format($lowestDepartment['avg'], 2) }}</span>
                        </div>
                        <span class="badge bg-rose-100 text-rose-800 border border-rose-300 px-3 py-2 fs-6 fw-bold">
                            {{ $lowestDepartment['category_label'] }}
                        </span>
                    </div>
                @else
                    <div class="text-center text-muted py-4">Belum ada data nilai per bidang</div>
                @endif
            </div>
        </div>
    </div>

    <!-- Card 3: Top 5 Pegawai dengan Nilai Tertinggi -->
    <div class="col-12 col-md-6 col-xl-3">
        <div class="exec-chart-card">
            <div class="card-header-custom py-3">
                <span><i class="bi bi-star-fill text-warning me-2"></i>Top 5 Pegawai Nilai Tertinggi</span>
            </div>
            <div class="card-body-custom p-0">
                <div class="table-responsive">
                    <table class="table table-hover table-striped align-middle mb-0" style="font-size: 12px;">
                        <thead>
                            <tr class="bg-light">
                                <th class="ps-3" style="width: 30px;">NO</th>
                                <th>NAMA PEGAWAI</th>
                                <th>BIDANG</th>
                                <th class="text-center pe-3">NILAI</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($topEmployees as $idx => $empRes)
                                <tr>
                                    <td class="ps-3 fw-bold text-muted">{{ $idx + 1 }}</td>
                                    <td>
                                        <div class="fw-bold text-dark lh-sm">{{ $empRes->employee->name ?? '-' }}</div>
                                        <small class="text-muted">NIP. {{ $empRes->employee->nip ?? '-' }}</small>
                                    </td>
                                    <td>
                                        <small class="text-dark fw-medium">{{ $empRes->employee->department->name ?? '-' }}</small>
                                    </td>
                                    <td class="text-center pe-3">
                                        <span class="badge bg-emerald-100 text-emerald-800 fw-bold px-2 py-1 fs-6">
                                            {{ number_format($empRes->final_score, 2) }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-3">Belum ada data pegawai</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Card 4: Pegawai yang Memerlukan Pembinaan -->
    <div class="col-12 col-md-6 col-xl-3">
        <div class="exec-chart-card">
            <div class="card-header-custom py-3">
                <span><i class="bi bi-shield-exclamation text-rose-600 me-2"></i>Pegawai Memerlukan Pembinaan</span>
            </div>
            <div class="card-body-custom p-0">
                <div class="table-responsive">
                    <table class="table table-hover table-striped align-middle mb-0" style="font-size: 12px;">
                        <thead>
                            <tr class="bg-light">
                                <th class="ps-3" style="width: 30px;">NO</th>
                                <th>NAMA PEGAWAI</th>
                                <th>BIDANG</th>
                                <th class="text-center pe-3">NILAI</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($needImprovementEmployees as $idx => $empRes)
                                <tr>
                                    <td class="ps-3 fw-bold text-muted">{{ $idx + 1 }}</td>
                                    <td>
                                        <div class="fw-bold text-dark lh-sm">{{ $empRes->employee->name ?? '-' }}</div>
                                        <small class="text-muted">NIP. {{ $empRes->employee->nip ?? '-' }}</small>
                                    </td>
                                    <td>
                                        <small class="text-dark fw-medium">{{ $empRes->employee->department->name ?? '-' }}</small>
                                    </td>
                                    <td class="text-center pe-3">
                                        <span class="badge bg-rose-100 text-rose-800 fw-bold px-2 py-1 fs-6">
                                            {{ number_format($empRes->final_score, 2) }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-3">Tidak ada pegawai yang memerlukan pembinaan</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    // 1. CHART DONUT: DISTRIBUSI HASIL PENILAIAN
    const ctxDoughnut = document.getElementById('doughnutCategoryChart');
    if (ctxDoughnut) {
        new Chart(ctxDoughnut, {
            type: 'doughnut',
            data: {
                labels: ['Sangat Baik', 'Baik', 'Cukup', 'Perlu Pembinaan'],
                datasets: [{
                    data: [
                        {{ $distribusiStats['sangat_baik']['count'] }},
                        {{ $distribusiStats['baik']['count'] }},
                        {{ $distribusiStats['cukup']['count'] }},
                        {{ $distribusiStats['kurang']['count'] }}
                    ],
                    backgroundColor: ['#10B981', '#3B82F6', '#F59E0B', '#EF4444'],
                    borderWidth: 2,
                    borderColor: '#FFFFFF'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                cutout: '70%'
            }
        });
    }

    // 2. CHART BAR HORIZONTAL: RATA-RATA NILAI PER BIDANG
    const ctxBar = document.getElementById('barDepartmentChart');
    if (ctxBar) {
        const deptNames = {!! json_encode($departmentAverages->pluck('name')) !!};
        const deptScores = {!! json_encode($departmentAverages->pluck('avg')) !!};

        new Chart(ctxBar, {
            type: 'bar',
            data: {
                labels: deptNames,
                datasets: [{
                    label: 'Rata-Rata Nilai',
                    data: deptScores,
                    backgroundColor: '#2563EB',
                    borderRadius: 6,
                    borderSkipped: false
                }]
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    x: {
                        min: 0,
                        max: 100,
                        grid: { color: '#F1F5F9' }
                    },
                    y: {
                        grid: { display: false },
                        ticks: {
                            callback: function(value, index) {
                                let label = this.getLabelForValue(index);
                                return label.length > 18 ? label.substr(0, 18) + '...' : label;
                            }
                        }
                    }
                }
            }
        });
    }

    // 3. CHART LINE TREN: TREN RATA-RATA INSTANSI PER PERIODE
    const ctxLine = document.getElementById('lineTrendChart');
    if (ctxLine) {
        const periodNames = {!! json_encode($periodTrends->pluck('period_name')) !!};
        const periodScores = {!! json_encode($periodTrends->pluck('avg_score')) !!};

        new Chart(ctxLine, {
            type: 'line',
            data: {
                labels: periodNames,
                datasets: [{
                    label: 'Nilai Rata-Rata',
                    data: periodScores,
                    borderColor: '#4F46E5',
                    backgroundColor: 'rgba(79, 70, 229, 0.1)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.35,
                    pointRadius: 6,
                    pointHoverRadius: 8,
                    pointBackgroundColor: '#4F46E5',
                    pointBorderColor: '#FFFFFF',
                    pointBorderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: {
                        min: 0,
                        max: 100,
                        grid: { color: '#F1F5F9' }
                    },
                    x: {
                        grid: { display: false }
                    }
                }
            }
        });
    }
});
</script>
@endpush
