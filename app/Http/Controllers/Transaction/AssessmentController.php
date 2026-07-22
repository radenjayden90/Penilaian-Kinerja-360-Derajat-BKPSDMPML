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
        $allPeers = $this->repository->getEligiblePeers($employee, $activePeriod);
        $peersPage = request()->input('peers_page', 1);
        $peers = new \Illuminate\Pagination\LengthAwarePaginator(
            $allPeers->forPage($peersPage, 9),
            $allPeers->count(),
            9,
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
            $allSubordinates->forPage($subsPage, 9),
            $allSubordinates->count(),
            9,
            $subsPage,
            ['path' => request()->url(), 'pageName' => 'subs_page']
        );

        return view('transaction.assessments.dashboard', compact('activePeriod', 'superior', 'peers', 'subordinates', 'employee'));
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

        return view('assessment.print', compact('result'));
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

        $fileName = 'rapor_kinerja_360_' . ($result->employee->nip ?? 'pegawai') . '_' . date('Ymd_His') . '.csv';

        $headers = [
            "Content-type"        => "text/csv; charset=UTF-8",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $posName = strtolower($result->employee->position?->name ?? '');
        $isKabid = ($result->employee->position?->level == '2' || str_contains($posName, 'kepala bidang') || str_contains($posName, 'kabid') || str_contains($posName, 'sekretaris'));
        $catEnum = $result->category instanceof \App\Enums\ResultCategory ? $result->category : \App\Enums\ResultCategory::tryFrom($result->category);
        $catLabel = $catEnum ? $catEnum->label() : $result->category;

        $callback = function() use($result, $isKabid, $catLabel) {
            $file = fopen('php://output', 'w');
            fputs($file, "\xEF\xBB\xBF");

            fputcsv($file, ['RAPOR INDIVIDU HASIL PENILAIAN KINERJA 360 DERAJAT']);
            fputcsv($file, ['']);
            fputcsv($file, ['Nama Pegawai', $result->employee->name ?? '-']);
            fputcsv($file, ['NIP', $result->employee->nip ?? '-']);
            fputcsv($file, ['Jabatan', $result->employee->position->name ?? '-']);
            fputcsv($file, ['Unit Kerja', $result->employee->department->name ?? '-']);
            fputcsv($file, ['Periode Penilaian', $result->period->name ?? '-']);
            fputcsv($file, ['']);

            fputcsv($file, ['No', 'Komponen Penilaian', 'Bobot', 'Skor Rata-Rata (1-10)', 'Skor Terbobot (10-100)']);

            if ($isKabid) {
                fputcsv($file, [1, 'Penilaian Atasan (Kepala BKPSDM)', '50%', number_format($result->subordinate_average ?? 0, 2), number_format(($result->subordinate_average ?? 0) * 10 * 0.50, 2)]);
                fputcsv($file, [2, 'Penilaian Sejawat (Rekan Kepala Bidang)', '30%', number_format($result->peer_average ?? 0, 2), number_format(($result->peer_average ?? 0) * 10 * 0.30, 2)]);
                fputcsv($file, [3, 'Penilaian Bawahan (Staf Divisi)', '20%', number_format($result->superior_average ?? 0, 2), number_format(($result->superior_average ?? 0) * 10 * 0.20, 2)]);
            } else {
                fputcsv($file, [1, 'Penilaian Atasan (Kepala Bidang)', '50%', number_format($result->subordinate_average ?? 0, 2), number_format(($result->subordinate_average ?? 0) * 10 * 0.50, 2)]);
                fputcsv($file, [2, 'Penilaian Sejawat (Rekan Staff)', '50%', number_format($result->peer_average ?? 0, 2), number_format(($result->peer_average ?? 0) * 10 * 0.50, 2)]);
            }

            fputcsv($file, ['', '', '', 'Nilai Akhir Kinerja 360°', number_format($result->final_score ?? 0, 2)]);
            fputcsv($file, ['', '', '', 'Predikat Kategori', strtoupper($catLabel ?? '-')]);

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function exportAllPdf(Request $request)
    {
        $user = Auth::user();
        $employee = Employee::where('email', $user->email)->orWhere('nip', $user->nip)->first();

        if (!$employee) {
            abort(403, 'Akun Anda tidak terhubung dengan data Pegawai.');
        }

        $results = \App\Models\AssessmentResult::with(['period'])
            ->where('employee_id', $employee->id)
            ->latest()
            ->get();

        return view('assessment.print_all', compact('employee', 'results'));
    }

    public function exportAllExcel(Request $request)
    {
        $user = Auth::user();
        $employee = Employee::where('email', $user->email)->orWhere('nip', $user->nip)->first();

        if (!$employee) {
            abort(403, 'Akun Anda tidak terhubung dengan data Pegawai.');
        }

        $results = \App\Models\AssessmentResult::with(['period'])
            ->where('employee_id', $employee->id)
            ->latest()
            ->get();

        $fileName = 'rekap_rapor_kinerja_360_' . ($employee->nip ?? 'pegawai') . '_' . date('Ymd_His') . '.csv';

        $headers = [
            "Content-type"        => "text/csv; charset=UTF-8",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $posName = strtolower($employee->position?->name ?? '');
        $isKabid = ($employee->position?->level == '2' || str_contains($posName, 'kepala bidang') || str_contains($posName, 'kabid') || str_contains($posName, 'sekretaris'));

        $callback = function() use($employee, $results, $isKabid) {
            $file = fopen('php://output', 'w');
            fputs($file, "\xEF\xBB\xBF");

            fputcsv($file, ['REKAPITULASI HISTORI HASIL PENILAIAN KINERJA 360 DERAJAT']);
            fputcsv($file, ['']);
            fputcsv($file, ['Nama Pegawai', $employee->name ?? '-']);
            fputcsv($file, ['NIP', $employee->nip ?? '-']);
            fputcsv($file, ['Jabatan', $employee->position->name ?? '-']);
            fputcsv($file, ['Unit Kerja', $employee->department->name ?? '-']);
            fputcsv($file, ['']);

            if ($isKabid) {
                fputcsv($file, ['No', 'Periode Penilaian', 'Skor Atasan (50%)', 'Skor Sejawat (30%)', 'Skor Bawahan (20%)', 'Nilai Akhir Kinerja 360°', 'Kategori Predikat']);
                foreach ($results as $index => $res) {
                    $catEnum = $res->category instanceof \App\Enums\ResultCategory ? $res->category : \App\Enums\ResultCategory::tryFrom($res->category);
                    $catLabel = $catEnum ? $catEnum->label() : $res->category;
                    fputcsv($file, [
                        $index + 1,
                        $res->period->name ?? '-',
                        number_format($res->subordinate_average ?? 0, 2),
                        number_format($res->peer_average ?? 0, 2),
                        number_format($res->superior_average ?? 0, 2),
                        number_format($res->final_score ?? 0, 2),
                        strtoupper($catLabel ?? '-')
                    ]);
                }
            } else {
                fputcsv($file, ['No', 'Periode Penilaian', 'Skor Atasan (50%)', 'Skor Sejawat (50%)', 'Nilai Akhir Kinerja 360°', 'Kategori Predikat']);
                foreach ($results as $index => $res) {
                    $catEnum = $res->category instanceof \App\Enums\ResultCategory ? $res->category : \App\Enums\ResultCategory::tryFrom($res->category);
                    $catLabel = $catEnum ? $catEnum->label() : $res->category;
                    fputcsv($file, [
                        $index + 1,
                        $res->period->name ?? '-',
                        number_format($res->subordinate_average ?? 0, 2),
                        number_format($res->peer_average ?? 0, 2),
                        number_format($res->final_score ?? 0, 2),
                        strtoupper($catLabel ?? '-')
                    ]);
                }
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
