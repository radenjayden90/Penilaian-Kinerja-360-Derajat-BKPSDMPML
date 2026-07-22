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
use App\Services\Export\AssessmentResultExporter;
use App\Services\Export\AssessmentHistoryExporter;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Illuminate\Support\Str;

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

        if (!$employee instanceof Employee || $employee->isAdmin()) {
            return redirect()->route('dashboard')->with('error', 'Akun Anda tidak memiliki akses untuk mengisi penilaian.');
        }

        $activePeriod = $this->repository->getActivePeriod();

        if (!$activePeriod) {
            return view('transaction.assessments.dashboard', [
                'activePeriod' => null,
                'superior' => null,
                'peers' => collect(),
                'subordinates' => collect(),
                'employee' => $employee,
                'isLimitReached' => false,
            ]);
        }
        
        $totalTugas = $this->repository->getTotalTugasPenilaian($employee);
        $submittedCount = Assessment::where('assessor_id', $employee->id)
            ->where('period_id', $activePeriod->id)
            ->whereIn('status', ['COMPLETED', 'SUBMITTED'])
            ->count();
            
        $isLimitReached = $submittedCount >= $totalTugas;

        // Get Superior
        $superior = $this->repository->getSuperior($employee);
        if ($superior) {
            $superior->load(['department', 'position']);
            $supAssessment = $this->repository->getAssessmentStatus($activePeriod->id, $employee->id, $superior->id, AssessmentType::SUPERIOR->value);
            $superior->assessment_status = ($supAssessment && ($supAssessment->status?->value === 'SUBMITTED' || $supAssessment->status === 'SUBMITTED')) ? 'COMPLETED' : 'PENDING';
        }

        // Get Peers (Cross-department, including Kabid, with COMPLETED / FULL / PENDING status)
        $allPeers = $this->repository->getEligiblePeers($employee, $activePeriod);
        $peersPage = request()->input('peers_page', 1);
        $peers = new \Illuminate\Pagination\LengthAwarePaginator(
            $allPeers->forPage($peersPage, 6),
            $allPeers->count(),
            6,
            $peersPage,
            ['path' => request()->url(), 'pageName' => 'peers_page']
        );

        // Get Subordinates (For Kabid: all employees in the same department/bidang)
        $allSubordinates = $this->repository->getSubordinates($employee);
        $allSubordinates->map(function ($subordinate) use ($activePeriod, $employee) {
            $subordinate->load(['department', 'position']);
            $subAssessment = $this->repository->getAssessmentStatus($activePeriod->id, $employee->id, $subordinate->id, AssessmentType::SUBORDINATE->value);
            $subordinate->assessment_status = ($subAssessment && ($subAssessment->status?->value === 'SUBMITTED' || $subAssessment->status === 'SUBMITTED')) ? 'COMPLETED' : 'PENDING';
            return $subordinate;
        });

        $subsPage = request()->input('subs_page', 1);
        $subordinates = new \Illuminate\Pagination\LengthAwarePaginator(
            $allSubordinates->forPage($subsPage, 6),
            $allSubordinates->count(),
            6,
            $subsPage,
            ['path' => request()->url(), 'pageName' => 'subs_page']
        );

        return view('transaction.assessments.dashboard', compact('activePeriod', 'superior', 'peers', 'subordinates', 'employee', 'isLimitReached', 'totalTugas', 'submittedCount'));
    }

    public function create(Request $request)
    {
        $user = Auth::user();
        $employee = Employee::where('email', $user->email)->orWhere('nip', $user->nip)->first();

        if (!$employee || $employee->isAdmin()) {
            return redirect()->route('dashboard')->with('error', 'Akun Anda tidak memiliki akses untuk mengisi penilaian.');
        }

        $targetId = $request->query('target_id');
        $type = $request->query('type'); // SUPERIOR, PEER, SUBORDINATE

        if (!$targetId || !in_array($type, ['SUPERIOR', 'PEER', 'SUBORDINATE'])) {
            return redirect()->route('transaction.assessments.index')->with('error', 'Parameter tidak valid.');
        }

        $activePeriod = $this->repository->getActivePeriod();
        if (!$activePeriod) {
            return redirect()->route('transaction.assessments.index')->with('error', 'Tidak ada periode penilaian yang aktif.');
        }

        $totalTugas = $this->repository->getTotalTugasPenilaian($employee);
        $submittedCount = Assessment::where('assessor_id', $employee->id)
            ->where('period_id', $activePeriod->id)
            ->whereIn('status', ['COMPLETED', 'SUBMITTED'])
            ->count();
            
        if ($submittedCount >= $totalTugas) {
            return redirect()->route('transaction.assessments.index')->with('error', 'Anda telah mencapai batas maksimal tugas penilaian ('.$totalTugas.' penilaian).');
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

        $activePeriod = \App\Models\Period::where('is_active', true)->orWhere('status', 'OPEN')->first();
        if ($activePeriod) {
            $totalTugas = $this->repository->getTotalTugasPenilaian($assessor);
            $submittedCount = Assessment::where('assessor_id', $assessor->id)
                ->where('period_id', $activePeriod->id)
                ->whereIn('status', [\App\Enums\AssessmentStatus::COMPLETED->value, \App\Enums\AssessmentStatus::SUBMITTED->value])
                ->count();
                
            if ($submittedCount >= $totalTugas) {
                return redirect()->route('transaction.assessments.index')->with('error', 'Anda telah mencapai batas maksimal tugas penilaian.');
            }
        }

        try {
            $this->assessmentService->storeAssessment($assessor, $target, $request->type, $request->scores, $request->general_notes);
            
            // Automatic calculation after assessment is stored
            $activePeriod = \App\Models\Period::where('is_active', true)->orWhere('status', 'OPEN')->first();
            if ($activePeriod) {
                $calculator = app(\App\Services\AssessmentCalculatorService::class);
                $calculator->calculateEmployee($target, $activePeriod);
            }

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

        // Exclude Admin and Kepala BKPSDM from viewing history
        $posName = strtolower($employee->position?->name ?? '');
        $isKepalaBkpsdm = ($employee->position?->level == '1' || str_contains($posName, 'kepala bkpsdm'));
        if ($employee->isAdmin() || $isKepalaBkpsdm) {
            return redirect()->route('dashboard')->with('error', 'Riwayat penilaian tidak tersedia untuk akun Anda.');
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

        foreach ($myResults as $res) {
            $res->aspectAverages = \Illuminate\Support\Facades\DB::table('assessment_scores')
                ->join('assessments', 'assessment_scores.assessment_id', '=', 'assessments.id')
                ->join('assessment_indicators', 'assessment_scores.indicator_id', '=', 'assessment_indicators.id')
                ->join('assessment_categories', 'assessment_indicators.category_id', '=', 'assessment_categories.id')
                ->where('assessments.employee_id', $employee->id)
                ->where('assessments.period_id', $res->period_id)
                ->where('assessments.status', 'SUBMITTED')
                ->select('assessment_categories.name', \Illuminate\Support\Facades\DB::raw('AVG(assessment_scores.score) as average_score'))
                ->groupBy('assessment_categories.id', 'assessment_categories.name', 'assessment_categories.display_order')
                ->orderBy('assessment_categories.display_order')
                ->get();
        }

        return view('assessment.index', compact(
            'employee',
            'periods',
            'selectedPeriodId',
            'myResults'
        ));
    }

    public function exportPdf(Request $request, $id)
    {
        $user = Auth::user();
        $employee = Employee::where('email', $user->email)->orWhere('nip', $user->nip)->first();

        if (!$employee) {
            abort(403, 'Akun Anda tidak terhubung dengan data Pegawai.');
        }

        $result = \App\Models\AssessmentResult::with(['employee.position', 'employee.department', 'period'])
            ->findOrFail($id);

        if (!$employee->isAdmin() && $result->employee_id !== $employee->id) {
            abort(403, 'Anda tidak memiliki akses untuk mengekspor rapor ini.');
        }

        $empName = Str::slug($result->employee->name ?? 'pegawai', '_');
        $fileName = 'Rekap_Penilaian_360_' . $empName . '_' . date('Ymd') . '.pdf';

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('assessment.print', compact('result'))
            ->setPaper('a4', 'portrait');

        return $pdf->stream($fileName);
    }

    public function exportExcel(Request $request, $id)
    {
        $user = Auth::user();
        $employee = Employee::where('email', $user->email)->orWhere('nip', $user->nip)->first();

        if (!$employee) {
            abort(403, 'Akun Anda tidak terhubung dengan data Pegawai.');
        }

        $result = \App\Models\AssessmentResult::with(['employee.position', 'employee.department', 'period'])
            ->findOrFail($id);

        if (!$employee->isAdmin() && $result->employee_id !== $employee->id) {
            abort(403, 'Anda tidak memiliki akses untuk mengekspor rapor ini.');
        }

        $exporter = new AssessmentResultExporter($result);
        $spreadsheet = $exporter->export();

        $empName = Str::slug($result->employee->name ?? 'pegawai', '_');
        $fileName = 'Rekap_Penilaian_360_' . $empName . '_' . date('Ymd') . '.xlsx';

        return response()->streamDownload(function() use ($spreadsheet) {
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        }, $fileName, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Cache-Control' => 'max-age=0',
        ]);
    }

    public function exportAllPdf(Request $request)
    {
        $user = Auth::user();
        $employee = Employee::where('email', $user->email)->orWhere('nip', $user->nip)->first();

        if (!$employee) {
            abort(403, 'Akun Anda tidak terhubung dengan data Pegawai.');
        }

        $year = $request->query('year');

        $query = \App\Models\AssessmentResult::with(['period'])
            ->where('employee_id', $employee->id);

        if ($year && $year !== 'all') {
            $query->whereHas('period', function($q) use ($year) {
                $q->where('year', $year);
            });
        }

        $results = $query->latest()->get();

        if ($results->isEmpty()) {
            return back()->with('error', 'Tidak ada data penilaian untuk diekspor pada filter yang dipilih.');
        }

        $empName = Str::slug($employee->name ?? 'pegawai', '_');
        $fileName = 'Rekap_Penilaian_360_Histori_' . $empName . '_' . date('Ymd') . '.pdf';

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('assessment.print_all', compact('employee', 'results'))
            ->setPaper('a4', 'portrait');

        return $pdf->stream($fileName);
    }

    public function exportAllExcel(Request $request)
    {
        $user = Auth::user();
        $employee = Employee::where('email', $user->email)->orWhere('nip', $user->nip)->first();

        if (!$employee) {
            abort(403, 'Akun Anda tidak terhubung dengan data Pegawai.');
        }

        $year = $request->query('year');

        $query = \App\Models\AssessmentResult::with(['period'])
            ->where('employee_id', $employee->id);

        if ($year && $year !== 'all') {
            $query->whereHas('period', function($q) use ($year) {
                $q->where('year', $year);
            });
        }

        $results = $query->latest()->get();

        if ($results->isEmpty()) {
            return back()->with('error', 'Tidak ada data penilaian untuk diekspor pada filter yang dipilih.');
        }

        $exporter = new AssessmentHistoryExporter($employee, $results);
        $spreadsheet = $exporter->export();

        $empName = Str::slug($employee->name ?? 'pegawai', '_');
        $fileName = 'Rekap_Penilaian_360_' . $empName . '_' . date('Ymd') . '.xlsx';

        return response()->streamDownload(function() use ($spreadsheet) {
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        }, $fileName, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Cache-Control' => 'max-age=0',
        ]);
    }
}
