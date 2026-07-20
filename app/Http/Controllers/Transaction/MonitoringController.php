<?php

namespace App\Http\Controllers\Transaction;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Repositories\AssessmentRepository;
use Illuminate\Http\Request;

class MonitoringController extends Controller
{
    protected $repository;

    public function __construct(AssessmentRepository $repository)
    {
        $this->repository = $repository;
    }

    public function index(Request $request)
    {
        $activePeriod = $this->repository->getActivePeriod();

        if (!$activePeriod) {
            return view('transaction.monitoring.index', [
                'activePeriod' => null,
                'employees' => collect(),
                'departments' => Department::orderBy('name')->get(),
            ]);
        }

        $search = $request->input('search');
        $departmentId = $request->input('department_id');
        $perPage = $request->input('per_page', 10);

        $employees = $this->repository->getMonitoringData($activePeriod->id, $search, $departmentId)
            ->paginate($perPage);

        // Map status logic
        // This is a bit simplified for monitoring. Realistically we'd need to know if they HAVE a superior/peer/subordinate to assess.
        // For now, just show counts of assessments they made.
        $employees->getCollection()->transform(function ($employee) {
            $assessmentsGiven = $employee->assessmentsGiven;
            
            $superiorCount = $assessmentsGiven->where('assessment_type', 'SUPERIOR')->count();
            $peerCount = $assessmentsGiven->where('assessment_type', 'PEER')->count();
            $subCount = $assessmentsGiven->where('assessment_type', 'SUBORDINATE')->count();
            
            // To know if they are done, we'd compare against targets.
            // Simplified: Just display counts they have done.
            $employee->monitoring_superior = $superiorCount > 0 ? 'Sudah (' . $superiorCount . ')' : 'Belum';
            $employee->monitoring_peer = $peerCount > 0 ? 'Sudah (' . $peerCount . ')' : 'Belum';
            $employee->monitoring_subordinate = $subCount > 0 ? 'Sudah (' . $subCount . ')' : 'Belum';
            
            $employee->total_assessed = $superiorCount + $peerCount + $subCount;
            return $employee;
        });

        $departments = Department::orderBy('name')->get();

        return view('transaction.monitoring.index', compact('activePeriod', 'employees', 'departments', 'search', 'departmentId', 'perPage'));
    }
}
