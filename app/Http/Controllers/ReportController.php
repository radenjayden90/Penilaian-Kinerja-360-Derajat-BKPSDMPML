<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Employee;
use App\Models\Department;
use App\Models\Period;
use App\Models\AssessmentResult;

use App\Services\Export\ReportExporter;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $periods = Period::orderBy('year', 'desc')->orderBy('month', 'desc')->get();
        $departments = Department::orderBy('name')->get();
        $activePeriod = Period::where('is_active', true)->orWhere('status', 'OPEN')->first();

        if ($request->has('period_id')) {
            $selectedPeriodId = $request->input('period_id');
        } else {
            // Default to active period if it has results, otherwise default to the latest period with results
            if ($activePeriod && AssessmentResult::where('period_id', $activePeriod->id)->exists()) {
                $selectedPeriodId = $activePeriod->id;
            } else {
                $latestPeriodId = AssessmentResult::latest()->value('period_id');
                $selectedPeriodId = $latestPeriodId ?? ($activePeriod->id ?? null);
            }
        }

        $selectedDepartmentId = $request->input('department_id');
        $search = $request->input('search');

        $resultsQuery = AssessmentResult::with(['employee', 'employee.department', 'employee.position', 'period'])
            ->when($selectedPeriodId, function($q) use ($selectedPeriodId) {
                return $q->where('period_id', $selectedPeriodId);
            })
            ->when($selectedDepartmentId, function($q) use ($selectedDepartmentId) {
                return $q->whereHas('employee', function($empQ) use ($selectedDepartmentId) {
                    $empQ->where('department_id', $selectedDepartmentId);
                });
            })
            ->when($search, function($q) use ($search) {
                $term = mb_strtolower($search, 'UTF-8');
                return $q->whereHas('employee', function($empQ) use ($term) {
                    $empQ->where(\Illuminate\Support\Facades\DB::raw('LOWER(name)'), 'LIKE', '%' . $term . '%')
                         ->orWhere(\Illuminate\Support\Facades\DB::raw('LOWER(nip)'), 'LIKE', '%' . $term . '%');
                });
            });

        $activeTab = $request->input('tab', 'department');

        $results = $resultsQuery->paginate($request->input('per_page', 15))->withQueryString();

        // Department breakdown stats (hanya bidang/unit kerja, bukan instansi utama BKPSDM)
        $departmentStats = Department::where('code', '!=', 'BKPSDM')
            ->where(\Illuminate\Support\Facades\DB::raw('LOWER(name)'), 'NOT LIKE', '%bkpsdm kabupaten%')
            ->withCount('employees')
            ->orderBy('name')
            ->get()
            ->map(function($dept) use ($selectedPeriodId) {
                $query = AssessmentResult::whereHas('employee', function($q) use ($dept) {
                    $q->where('department_id', $dept->id);
                });

                if ($selectedPeriodId) {
                    $query->where('period_id', $selectedPeriodId);
                }

                $results = $query->get();
                $avgScore = $results->count() > 0 ? round($results->avg('final_score'), 2) : 0;
                $highestScore = $results->count() > 0 ? round($results->max('final_score'), 2) : 0;

                return [
                    'department' => $dept,
                    'total_evaluated' => $results->count(),
                    'average_score' => $avgScore,
                    'highest_score' => $highestScore,
                    'very_good' => $results->filter(fn($r) => (is_object($r->category) ? $r->category->value : $r->category) === 'VERY_GOOD')->count(),
                    'good' => $results->filter(fn($r) => (is_object($r->category) ? $r->category->value : $r->category) === 'GOOD')->count(),
                    'fair' => $results->filter(fn($r) => (is_object($r->category) ? $r->category->value : $r->category) === 'FAIR')->count(),
                    'needs_improvement' => $results->filter(fn($r) => (is_object($r->category) ? $r->category->value : $r->category) === 'NEEDS_IMPROVEMENT')->count(),
                ];
            });

        // Analytics stats
        $allPeriodResults = AssessmentResult::when($selectedPeriodId, function($q) use ($selectedPeriodId) {
            return $q->where('period_id', $selectedPeriodId);
        })->get();

        $categoryDistribution = [
            'VERY_GOOD' => $allPeriodResults->filter(fn($r) => (is_object($r->category) ? $r->category->value : $r->category) === 'VERY_GOOD')->count(),
            'GOOD' => $allPeriodResults->filter(fn($r) => (is_object($r->category) ? $r->category->value : $r->category) === 'GOOD')->count(),
            'FAIR' => $allPeriodResults->filter(fn($r) => (is_object($r->category) ? $r->category->value : $r->category) === 'FAIR')->count(),
            'NEEDS_IMPROVEMENT' => $allPeriodResults->filter(fn($r) => (is_object($r->category) ? $r->category->value : $r->category) === 'NEEDS_IMPROVEMENT')->count(),
        ];

        $overallAverage = $allPeriodResults->count() > 0 ? round($allPeriodResults->avg('final_score'), 2) : 0;

        return view('report.index', compact(
            'results', 'periods', 'departments', 'selectedPeriodId', 
            'selectedDepartmentId', 'search', 'activePeriod', 'activeTab', 
            'departmentStats', 'categoryDistribution', 'overallAverage', 'allPeriodResults'
        ));
    }

    public function print(Request $request)
    {
        $selectedPeriodId = $request->input('period_id');
        $selectedDepartmentId = $request->input('department_id');

        $period = Period::find($selectedPeriodId) ?? Period::where('is_active', true)->first();
        $department = Department::find($selectedDepartmentId);

        $results = AssessmentResult::with(['employee', 'employee.department', 'employee.position', 'period'])
            ->when($selectedPeriodId, function($q) use ($selectedPeriodId) {
                return $q->where('period_id', $selectedPeriodId);
            })
            ->when($selectedDepartmentId, function($q) use ($selectedDepartmentId) {
                return $q->whereHas('employee', function($empQ) use ($selectedDepartmentId) {
                    $empQ->where('department_id', $selectedDepartmentId);
                });
            })
            ->get();

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('report.print', compact('results', 'period', 'department'))
            ->setPaper('a4', 'landscape');

        $fileName = 'Laporan_Rekapitulasi_Penilaian_360_' . date('Ymd_His') . '.pdf';

        return $pdf->stream($fileName);
    }

    public function exportCsv(Request $request)
    {
        $selectedPeriodId = $request->input('period_id');
        $selectedDepartmentId = $request->input('department_id');

        $period = Period::find($selectedPeriodId);
        $department = Department::find($selectedDepartmentId);

        $results = AssessmentResult::with(['employee', 'employee.department', 'employee.position', 'period'])
            ->when($selectedPeriodId, function($q) use ($selectedPeriodId) {
                return $q->where('period_id', $selectedPeriodId);
            })
            ->when($selectedDepartmentId, function($q) use ($selectedDepartmentId) {
                return $q->whereHas('employee', function($empQ) use ($selectedDepartmentId) {
                    $empQ->where('department_id', $selectedDepartmentId);
                });
            })
            ->get();

        $exporter = new ReportExporter($results, $period, $department);
        $spreadsheet = $exporter->export();

        $fileName = 'Rekap_Penilaian_360_Laporan_' . date('Ymd') . '.xlsx';

        return response()->streamDownload(function() use ($spreadsheet) {
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        }, $fileName, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Cache-Control' => 'max-age=0',
        ]);
    }
}
