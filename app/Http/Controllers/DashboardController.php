<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Employee;
use App\Models\Department;
use App\Models\Position;
use App\Models\Period;
use App\Models\Assessment;
use App\Enums\AssessmentStatus;

class DashboardController extends Controller
{
    public function index()
    {
        /** @var \App\Models\Employee $user */
        $user = Auth::user();

        if ($user && $user->isKepalaBkpsdm()) {
            return $this->kepalaDashboard($user);
        }

        if ($user && $user->isAdmin()) {
            return $this->adminDashboard();
        }

        return $this->pegawaiDashboard($user);
    }

    private function kepalaDashboard($user)
    {
        $activePeriod = Period::where('is_active', true)->orWhere('status', 'OPEN')->first();

        $totalAsnActive = Employee::where('is_active', true)
            ->whereDoesntHave('role', fn($q) => $q->whereIn('name', ['ADMIN', 'SUPER_ADMIN']))
            ->count();

        $activePeriodResults = \App\Models\AssessmentResult::with(['employee.department', 'employee.position', 'period'])
            ->when($activePeriod, fn($q) => $q->where('period_id', $activePeriod->id))
            ->whereHas('employee', function($q) {
                $q->whereHas('role', fn($r) => $r->whereNotIn('name', ['ADMIN', 'SUPER_ADMIN']));
            })
            ->get();

        $evaluatedEmployeesCount = $activePeriodResults->pluck('employee_id')->unique()->count();
        $averageScore = $activePeriodResults->count() > 0 ? round($activePeriodResults->avg('final_score'), 2) : 0;

        // Total assessments progress
        $totalAssessmentsCount = Assessment::when($activePeriod, fn($q) => $q->where('period_id', $activePeriod->id))->count();
        $completedAssessmentsCount = Assessment::when($activePeriod, fn($q) => $q->where('period_id', $activePeriod->id))
            ->whereIn('status', ['COMPLETED', 'SUBMITTED'])
            ->count();
        $assessmentProgressPct = $totalAssessmentsCount > 0 ? round(($completedAssessmentsCount / $totalAssessmentsCount) * 100, 1) : 0;

        // 1. Distribusi Predikat (Sangat Baik: 90-100, Baik: 76-89, Cukup: 61-75, Perlu Pembinaan: <60)
        $totalEval = max($activePeriodResults->count(), 1);

        $countSangatBaik = $activePeriodResults->filter(fn($r) => $r->final_score >= 90)->count();
        $countBaik = $activePeriodResults->filter(fn($r) => $r->final_score >= 76 && $r->final_score < 90)->count();
        $countCukup = $activePeriodResults->filter(fn($r) => $r->final_score >= 61 && $r->final_score < 76)->count();
        $countKurang = $activePeriodResults->filter(fn($r) => $r->final_score < 61)->count();

        $distribusiStats = [
            'sangat_baik' => ['count' => $countSangatBaik, 'pct' => round(($countSangatBaik / $totalEval) * 100, 1)],
            'baik' => ['count' => $countBaik, 'pct' => round(($countBaik / $totalEval) * 100, 1)],
            'cukup' => ['count' => $countCukup, 'pct' => round(($countCukup / $totalEval) * 100, 1)],
            'kurang' => ['count' => $countKurang, 'pct' => round(($countKurang / $totalEval) * 100, 1)],
        ];

        // 2. Rata-Rata Nilai per Bidang (hanya bidang/unit kerja bawahan, bukan instansi utama BKPSDM)
        $departments = Department::where('code', '!=', 'BKPSDM')
            ->where(\Illuminate\Support\Facades\DB::raw('LOWER(name)'), 'NOT LIKE', '%bkpsdm kabupaten%')
            ->orderBy('name')
            ->get();
        $departmentAverages = $departments->map(function($dept) use ($activePeriodResults) {
            $deptResults = $activePeriodResults->filter(fn($r) => $r->employee?->department_id === $dept->id);
            $avg = $deptResults->count() > 0 ? round($deptResults->avg('final_score'), 2) : 0;
            return [
                'id' => $dept->id,
                'name' => $dept->name,
                'avg' => $avg,
                'count' => $deptResults->count(),
                'category_label' => \App\Enums\ResultCategory::formatLabel($avg >= 90 ? 'VERY_GOOD' : ($avg >= 76 ? 'GOOD' : ($avg >= 61 ? 'FAIR' : 'NEEDS_IMPROVEMENT'))),
            ];
        });

        // 3. Trend Nilai Rata-Rata Instansi per Periode
        $pastPeriods = Period::orderBy('year', 'asc')->orderBy('month', 'asc')->take(12)->get();
        $periodTrends = $pastPeriods->map(function($p) {
            $pResults = \App\Models\AssessmentResult::where('period_id', $p->id)->get();
            return [
                'period_name' => $p->name ?? ($p->year . ' M' . $p->month),
                'avg_score' => $pResults->count() > 0 ? round($pResults->avg('final_score'), 2) : 0,
            ];
        });

        // 4. Nilai Tertinggi per Bidang & Perlu Perhatian
        $validDeptAverages = $departmentAverages->filter(fn($d) => $d['count'] > 0)->sortByDesc('avg');
        $topDepartment = $validDeptAverages->first();
        $lowestDepartment = $validDeptAverages->last();

        // 5. Top 5 Pegawai Nilai Tertinggi
        $topEmployees = $activePeriodResults->sortByDesc('final_score')->take(5);

        // 6. Pegawai Memerlukan Pembinaan (Bottom / Score < 61)
        $needImprovementEmployees = $activePeriodResults->filter(fn($r) => $r->final_score < 61)->sortBy('final_score')->take(5);
        if ($needImprovementEmployees->isEmpty()) {
            $needImprovementEmployees = $activePeriodResults->sortBy('final_score')->take(5);
        }

        return view('dashboard.kepala', compact(
            'activePeriod', 'totalAsnActive', 'evaluatedEmployeesCount',
            'averageScore', 'assessmentProgressPct', 'distribusiStats',
            'departmentAverages', 'periodTrends', 'topDepartment',
            'lowestDepartment', 'topEmployees', 'needImprovementEmployees'
        ));
    }

    private function adminDashboard()
    {
        $activePeriod = Period::where('is_active', true)->first();
        
        $stats = [
            'total_pegawai' => Employee::count(),
            'total_department' => Department::count(),
            'total_position' => Position::count(),
            'active_period' => $activePeriod,
            'total_periods' => Period::count(),
            'completed_assessments' => Assessment::where('status', AssessmentStatus::COMPLETED->value)->orWhere('status', AssessmentStatus::SUBMITTED->value)->count(),
            'pending_assessments' => Assessment::where('status', '!=', AssessmentStatus::COMPLETED->value)->where('status', '!=', AssessmentStatus::SUBMITTED->value)->count(),
        ];

        $results = \App\Models\AssessmentResult::when($activePeriod, function($q) use ($activePeriod) {
            return $q->where('period_id', $activePeriod->id);
        })->get();

        $categoryStats = [
            'sangat_baik' => $results->filter(function($r) {
                $val = is_object($r->category) ? $r->category->value : $r->category;
                return $val === 'VERY_GOOD' || $val === 'SANGAT_BAIK';
            })->count(),
            'baik' => $results->filter(function($r) {
                $val = is_object($r->category) ? $r->category->value : $r->category;
                return $val === 'GOOD' || $val === 'BAIK';
            })->count(),
            'cukup' => $results->filter(function($r) {
                $val = is_object($r->category) ? $r->category->value : $r->category;
                return $val === 'FAIR' || $val === 'CUKUP';
            })->count(),
            'kurang' => $results->filter(function($r) {
                $val = is_object($r->category) ? $r->category->value : $r->category;
                return $val === 'NEEDS_IMPROVEMENT' || $val === 'KURANG';
            })->count(),
        ];

        $topResults = \App\Models\AssessmentResult::with(['employee.department', 'employee.position'])
            ->when($activePeriod, function($q) use ($activePeriod) {
                return $q->where('period_id', $activePeriod->id);
            })
            ->whereHas('employee', function($q) {
                $q->whereHas('role', function($r) {
                    $r->whereNotIn('name', ['ADMIN', 'SUPER_ADMIN']);
                })
                ->where(function($e) {
                    $e->whereNull('position_id')
                      ->orWhereHas('position', function($pos) {
                          $pos->where('level', '!=', '1')
                              ->where(\Illuminate\Support\Facades\DB::raw('LOWER(name)'), 'NOT LIKE', '%kepala bkpsdm%');
                      });
                });
            })
            ->orderBy('final_score', 'desc')
            ->take(5)
            ->get();

        return view('dashboard.admin', compact('stats', 'topResults', 'categoryStats'));
    }

    private function pegawaiDashboard($user)
    {
        $activePeriod = Period::where('is_active', true)->first();
        
        $myAssessments = collect();
        $submittedCount = 0;
        $pendingCount = 0;
        $receivedAssessmentsCount = 0;

        $totalTugas = app(\App\Repositories\AssessmentRepository::class)->getTotalTugasPenilaian($user);

        if ($user) {
            $myAssessmentsQuery = Assessment::with(['employee', 'employee.department', 'employee.position'])
                ->where('assessor_id', $user->id);

            if ($activePeriod) {
                $myAssessmentsQuery->where('period_id', $activePeriod->id);
            }

            $myAssessments = $myAssessmentsQuery->get();

            $submittedCount = $myAssessments->whereIn('status', [AssessmentStatus::COMPLETED, AssessmentStatus::SUBMITTED])->count();
            
            // Limit the count to totalTugas (max = total tugas penilaian)
            if ($submittedCount > $totalTugas) {
                $submittedCount = $totalTugas;
            }
            
            $pendingCount = max(0, $totalTugas - $submittedCount);

            // Count incoming assessments that have been completed (exclude Kepala BKPSDM and admin)
            $posName = strtolower($user->position?->name ?? '');
            $isKepalaBkpsdm = ($user->position?->level == '1' || str_contains($posName, 'kepala bkpsdm'));
            if ($activePeriod && !$isKepalaBkpsdm && !$user->isAdmin()) {
                $receivedAssessmentsCount = Assessment::where('employee_id', $user->id)
                    ->where('period_id', $activePeriod->id)
                    ->whereIn('status', [AssessmentStatus::COMPLETED->value, AssessmentStatus::SUBMITTED->value])
                    ->count();
            }
        }

        return view('dashboard.pegawai', compact('user', 'activePeriod', 'myAssessments', 'submittedCount', 'pendingCount', 'receivedAssessmentsCount', 'totalTugas'));
    }

    public function notificationCount()
    {
        $user = Auth::user();
        $authEmployee = Employee::where('email', $user->email)->orWhere('nip', $user->nip)->first() ?? $user;
        
        $assessmentCount = 0;
        $lastUpdated = null;

        if ($authEmployee && $authEmployee instanceof Employee) {
            $query = Assessment::where('employee_id', $authEmployee->id)
                ->whereIn('status', ['SUBMITTED', 'COMPLETED']);

            $activePeriod = Period::where('is_active', true)->orWhere('status', 'OPEN')->first();
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
        
        return response()->json([
            'count' => $assessmentCount,
            'last_updated' => $lastUpdated
        ]);
    }
}
