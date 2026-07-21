<?php

namespace App\Http\Controllers\Transaction;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\Assessment;
use App\Http\Requests\Transaction\StoreAssessmentRequest;
use App\Services\AssessmentService;
use App\Repositories\AssessmentRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Enums\AssessmentType;

class AssessmentController extends Controller
{
    protected $assessmentService;
    protected $repository;

    public function __construct(AssessmentService $assessmentService, AssessmentRepository $repository)
    {
        $this->assessmentService = $assessmentService;
        $this->repository = $repository;
    }

    public function index()
    {
        $user = Auth::user();
        $employee = Employee::where('email', $user->email)->orWhere('nip', $user->nip)->first() ?? $user;

        if (!$employee instanceof Employee) {
            return redirect()->route('dashboard')->with('error', 'Akun Anda tidak terhubung dengan data Pegawai.');
        }

        $activePeriod = $this->repository->getActivePeriod();

        if (!$activePeriod) {
            return view('transaction.assessments.dashboard', [
                'activePeriod' => null,
                'superior' => null,
                'peers' => collect(),
                'subordinates' => collect(),
                'employee' => $employee,
            ]);
        }

        // Get Superior
        $superior = $this->repository->getSuperior($employee);
        if ($superior) {
            $superior->load(['department', 'position']);
            $supAssessment = $this->repository->getAssessmentStatus($activePeriod->id, $employee->id, $superior->id, AssessmentType::SUPERIOR->value);
            $superior->assessment_status = ($supAssessment && ($supAssessment->status?->value === 'SUBMITTED' || $supAssessment->status === 'SUBMITTED')) ? 'COMPLETED' : 'PENDING';
        }

        // Get Peers (Cross-department, including Kabid, with COMPLETED / FULL / PENDING status)
        $peers = $this->repository->getEligiblePeers($employee, $activePeriod);

        // Get Subordinates (For Kabid: all employees in the same department/bidang)
        $subordinates = $this->repository->getSubordinates($employee);
        $subordinates->map(function ($subordinate) use ($activePeriod, $employee) {
            $subordinate->load(['department', 'position']);
            $subAssessment = $this->repository->getAssessmentStatus($activePeriod->id, $employee->id, $subordinate->id, AssessmentType::SUBORDINATE->value);
            $subordinate->assessment_status = ($subAssessment && ($subAssessment->status?->value === 'SUBMITTED' || $subAssessment->status === 'SUBMITTED')) ? 'COMPLETED' : 'PENDING';
            return $subordinate;
        });

        return view('transaction.assessments.dashboard', compact('activePeriod', 'superior', 'peers', 'subordinates', 'employee'));
    }

    public function create(Request $request)
    {
        $targetId = $request->query('target_id');
        $type = $request->query('type'); // SUPERIOR, PEER, SUBORDINATE

        if (!$targetId || !in_array($type, ['SUPERIOR', 'PEER', 'SUBORDINATE'])) {
            return redirect()->route('transaction.assessments.index')->with('error', 'Parameter tidak valid.');
        }

        $target = Employee::with('department', 'position')->findOrFail($targetId);
        $activePeriod = $this->repository->getActivePeriod();

        if (!$activePeriod) {
            return redirect()->route('transaction.assessments.index')->with('error', 'Tidak ada periode penilaian yang aktif.');
        }

        $categories = $this->assessmentService->getAssessmentFormData();

        return view('transaction.assessments.create', compact('target', 'type', 'activePeriod', 'categories'));
    }

    public function store(StoreAssessmentRequest $request)
    {
        $user = Auth::user();
        $assessor = Employee::where('email', $user->email)->orWhere('nip', $user->nip)->first() ?? $user;
        $target = Employee::findOrFail($request->target_id);

        try {
            $this->assessmentService->storeAssessment($assessor, $target, $request->type, $request->scores, $request->general_notes);
            return redirect()->route('transaction.assessments.index')->with('success', 'Penilaian berhasil disimpan.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage())->withInput();
        }
    }

    public function show(Assessment $assessment)
    {
        $user = Auth::user();
        $employee = Employee::where('email', $user->email)->orWhere('nip', $user->nip)->first();

        // Non-admin users can only view their own assessments
        if ($employee && !$employee->isAdmin() && $assessment->assessor_id !== $employee->id && $assessment->employee_id !== $employee->id) {
            return redirect()->route('assessment.index')->with('error', 'Anda tidak memiliki akses untuk melihat penilaian ini.');
        }

        $assessment->load(['assessor', 'employee', 'scores.indicator.category', 'period']);
        
        $groupedScores = $assessment->scores->groupBy(function($score) {
            return $score->indicator->category->name;
        });

        return view('transaction.assessments.show', compact('assessment', 'groupedScores'));
    }

    public function history(Request $request)
    {
        $user = Auth::user();
        $employee = Employee::where('email', $user->email)->orWhere('nip', $user->nip)->first();

        if (!$employee) {
            return redirect()->route('dashboard')->with('error', 'Akun Anda tidak terhubung dengan data Pegawai.');
        }

        $selectedPeriodId = $request->input('period_id');
        $periods = \App\Models\Period::orderBy('year', 'desc')->orderBy('month', 'desc')->get();

        // Query strictly the logged-in employee's own 360° evaluation results
        $myResults = \App\Models\AssessmentResult::with(['period'])
            ->where('employee_id', $employee->id)
            ->when($selectedPeriodId, function($q) use ($selectedPeriodId) {
                return $q->where('period_id', $selectedPeriodId);
            })
            ->latest()
            ->paginate(10);

        return view('assessment.index', compact(
            'employee',
            'periods',
            'selectedPeriodId',
            'myResults'
        ));
    }
}
