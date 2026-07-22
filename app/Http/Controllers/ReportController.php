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
        $activePeriod = Period::where('is_active', true)->orWhere('status', 'OPEN')->first();
        $selectedPeriodId = $request->input('period_id', $activePeriod->id ?? null);
        $selectedDepartmentId = $request->input('department_id');
        $search = $request->input('search');

        $periods = Period::orderBy('year', 'desc')->orderBy('month', 'desc')->get();
        $departments = Department::orderBy('name')->get();

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

        $results = $resultsQuery->paginate($request->input('per_page', 15));

        return view('report.index', compact('results', 'periods', 'departments', 'selectedPeriodId', 'selectedDepartmentId', 'search', 'activePeriod'));
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

        return view('report.print', compact('results', 'period', 'department'));
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
