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
        $employee = Employee::where('email', $user->email)->first();

        if (!$employee) {
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
            $superior->assessment_status = $this->repository->getAssessmentStatus($activePeriod->id, $employee->id, $superior->id, AssessmentType::SUPERIOR->value);
        }

        // Get Peers
        $peers = $this->repository->getEligiblePeers($employee, $activePeriod);
        $peers->map(function ($peer) use ($activePeriod, $employee) {
            $peer->assessment_status = $this->repository->getAssessmentStatus($activePeriod->id, $employee->id, $peer->id, AssessmentType::PEER->value);
            return $peer;
        });

        // Get Subordinates
        $subordinates = collect();
        if ($employee->role?->name === 'Kabid' || $employee->role?->name === 'Kepala BKPSDM' || $this->repository->getSubordinates($employee)->count() > 0) {
            $subordinates = $this->repository->getSubordinates($employee);
            $subordinates->map(function ($subordinate) use ($activePeriod, $employee) {
                $subordinate->assessment_status = $this->repository->getAssessmentStatus($activePeriod->id, $employee->id, $subordinate->id, AssessmentType::SUBORDINATE->value);
                return $subordinate;
            });
        }

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
        $assessor = Employee::where('email', $user->email)->firstOrFail();
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
        $assessment->load(['assessor', 'employee', 'scores.indicator.category', 'period']);
        
        $groupedScores = $assessment->scores->groupBy(function($score) {
            return $score->indicator->category->name;
        });

        return view('transaction.assessments.show', compact('assessment', 'groupedScores'));
    }
}
