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
            'sangat_baik' => $results->where('category', 'SANGAT_BAIK')->count(),
            'baik' => $results->where('category', 'BAIK')->count(),
            'cukup' => $results->where('category', 'CUKUP')->count(),
            'kurang' => $results->where('category', 'KURANG')->count(),
        ];

        $recentEmployees = Employee::with(['department', 'position', 'role'])
            ->latest()
            ->take(5)
            ->get();

        return view('dashboard.admin', compact('stats', 'recentEmployees', 'categoryStats'));
    }

    private function pegawaiDashboard($user)
    {
        $activePeriod = Period::where('is_active', true)->first();
        
        $myAssessments = collect();
        $submittedCount = 0;
        $pendingCount = 0;

        if ($user) {
            $myAssessmentsQuery = Assessment::with(['employee', 'employee.department', 'employee.position'])
                ->where('assessor_id', $user->id);

            if ($activePeriod) {
                $myAssessmentsQuery->where('period_id', $activePeriod->id);
            }

            $myAssessments = $myAssessmentsQuery->get();

            $submittedCount = $myAssessments->whereIn('status', [AssessmentStatus::COMPLETED->value, AssessmentStatus::SUBMITTED->value, 'COMPLETED', 'SUBMITTED'])->count();
            $pendingCount = $myAssessments->whereNotIn('status', [AssessmentStatus::COMPLETED->value, AssessmentStatus::SUBMITTED->value, 'COMPLETED', 'SUBMITTED'])->count();
        }

        return view('dashboard.pegawai', compact('user', 'activePeriod', 'myAssessments', 'submittedCount', 'pendingCount'));
    }
}
