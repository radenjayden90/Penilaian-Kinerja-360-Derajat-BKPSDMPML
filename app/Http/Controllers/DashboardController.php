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

        if ($user && $user->isAdmin()) {
            return $this->adminDashboard();
        }

        return $this->pegawaiDashboard($user);
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
}
