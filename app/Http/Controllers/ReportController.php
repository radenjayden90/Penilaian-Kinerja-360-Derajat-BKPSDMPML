<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Employee;
use App\Models\Department;
use App\Models\Period;
use App\Models\AssessmentResult;

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

        $fileName = 'laporan_kinerja_360_' . date('Ymd_His') . '.csv';

        $headers = [
            "Content-type"        => "text/csv; charset=UTF-8",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $columns = ['No', 'NIP', 'Nama Pegawai', 'Unit Kerja', 'Jabatan', 'Skor Atasan', 'Skor Sejawat', 'Skor Bawahan', 'Skor Diri', 'Nilai Akhir 360', 'Kategori Predikat'];

        $callback = function() use($results, $columns) {
            $file = fopen('php://output', 'w');
            // Add UTF-8 BOM for Excel compatibility
            fputs($file, "\xEF\xBB\xBF");
            fputcsv($file, $columns);

            foreach ($results as $index => $res) {
                $catVal = is_object($res->category) ? $res->category->value : $res->category;
                fputcsv($file, [
                    $index + 1,
                    $res->employee->nip ?? '-',
                    $res->employee->name ?? '-',
                    $res->employee->department->name ?? '-',
                    $res->employee->position->name ?? '-',
                    number_format($res->superior_score ?? 0, 2),
                    number_format($res->peer_score ?? 0, 2),
                    number_format($res->subordinate_score ?? 0, 2),
                    number_format($res->self_score ?? 0, 2),
                    number_format($res->final_score ?? 0, 2),
                    str_replace('_', ' ', $catVal ?? '-')
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
