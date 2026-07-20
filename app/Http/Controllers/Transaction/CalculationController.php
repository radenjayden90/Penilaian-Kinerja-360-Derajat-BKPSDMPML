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
        $activePeriod = Period::where('status', 'OPEN')->first();
        
        $query = Employee::with(['department', 'position', 'assessmentResult' => function($q) use ($activePeriod) {
            if ($activePeriod) {
                $q->where('period_id', $activePeriod->id);
            }
        }]);

        if ($request->search) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'ilike', '%' . $request->search . '%')
                  ->orWhere('nip', 'ilike', '%' . $request->search . '%');
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
        $activePeriod = Period::where('status', 'OPEN')->first();
        
        if (!$activePeriod) {
            return back()->with('error', 'Tidak ada periode penilaian yang aktif.');
        }

        $processed = $this->calculator->calculateAll($activePeriod);

        return back()->with('success', "Perhitungan masal selesai. Total {$processed} pegawai diproses.");
    }

    public function calculate(Employee $employee)
    {
        $activePeriod = Period::where('status', 'OPEN')->first();
        
        if (!$activePeriod) {
            return back()->with('error', 'Tidak ada periode penilaian yang aktif.');
        }

        $this->calculator->calculateEmployee($employee, $activePeriod);

        return back()->with('success', "Perhitungan untuk pegawai {$employee->name} telah diperbarui.");
    }

    public function show(Employee $employee)
    {
        $activePeriod = Period::where('status', 'OPEN')->first();
        
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
