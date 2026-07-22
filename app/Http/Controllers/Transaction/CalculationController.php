<?php

namespace App\Http\Controllers\Transaction;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Employee;
use App\Models\Period;
use App\Models\AssessmentResult;
use App\Services\AssessmentCalculatorService;

class CalculationController extends Controller
{
    protected $calculator;

    public function __construct(AssessmentCalculatorService $calculator)
    {
        $this->calculator = $calculator;
    }

    public function index(Request $request)
    {
        $activePeriod = Period::where('is_active', true)->orWhere('status', 'OPEN')->first();
        
        $selectedPeriodId = $request->input('period_id');
        if ($selectedPeriodId) {
            $targetPeriod = Period::find($selectedPeriodId);
        } else {
            if ($activePeriod && AssessmentResult::where('period_id', $activePeriod->id)->exists()) {
                $targetPeriod = $activePeriod;
            } else {
                $latestPeriodId = AssessmentResult::latest()->value('period_id');
                $targetPeriod = $latestPeriodId ? Period::find($latestPeriodId) : $activePeriod;
            }
        }

        // Automatic real-time calculation sync
        if ($targetPeriod) {
            $this->calculator->calculateAll($targetPeriod);
        }
        
        $query = Employee::where('is_active', true)
            ->whereHas('role', function($q) {
                $q->whereNotIn('name', ['ADMIN', 'SUPER_ADMIN']);
            })
            ->where(function($q) {
                $q->whereNull('position_id')
                  ->orWhereHas('position', function($pos) {
                      $pos->where('level', '!=', '1')
                          ->where(\Illuminate\Support\Facades\DB::raw('LOWER(name)'), 'NOT LIKE', '%kepala bkpsdm%');
                  });
            })
            ->with(['department', 'position', 'assessmentResult' => function($q) use ($targetPeriod) {
                if ($targetPeriod) {
                    $q->where('period_id', $targetPeriod->id);
                }
            }]);

        if ($request->search) {
            $term = mb_strtolower($request->search, 'UTF-8');
            $query->where(function($q) use ($term) {
                $q->where(\Illuminate\Support\Facades\DB::raw('LOWER(name)'), 'LIKE', '%' . $term . '%')
                  ->orWhere(\Illuminate\Support\Facades\DB::raw('LOWER(nip)'), 'LIKE', '%' . $term . '%');
            });
        }

        if ($request->department_id) {
            $query->where('department_id', $request->department_id);
        }

        $employees = $query->paginate($request->per_page ?? 10);
        $departments = \App\Models\Department::all();

        return view('transaction.calculation.index', compact('employees', 'departments', 'activePeriod'));
    }

    public function calculateAll()
    {
        $activePeriod = Period::where('is_active', true)->orWhere('status', 'OPEN')->first();
        
        if (!$activePeriod) {
            return back()->with('error', 'Tidak ada periode penilaian yang aktif.');
        }

        $processed = $this->calculator->calculateAll($activePeriod);

        return back()->with('success', "Perhitungan masal selesai. Total {$processed} pegawai diproses.");
    }

    public function calculate(Employee $employee)
    {
        $activePeriod = Period::where('is_active', true)->orWhere('status', 'OPEN')->first();
        
        if (!$activePeriod) {
            return back()->with('error', 'Tidak ada periode penilaian yang aktif.');
        }

        $this->calculator->calculateEmployee($employee, $activePeriod);

        return back()->with('success', "Perhitungan untuk pegawai {$employee->name} telah diperbarui.");
    }

    public function show(Employee $employee)
    {
        $activePeriod = Period::where('is_active', true)->orWhere('status', 'OPEN')->first();
        
        if (!$activePeriod) {
            return back()->with('error', 'Tidak ada periode penilaian yang aktif.');
        }

        $result = AssessmentResult::where('employee_id', $employee->id)
            ->where('period_id', $activePeriod->id)
            ->first();

        if (!$result) {
            return back()->with('error', 'Belum ada data perhitungan untuk pegawai ini.');
        }

        return view('transaction.calculation.show', compact('employee', 'result', 'activePeriod'));
    }
}
