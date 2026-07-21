<?php

namespace App\Services;

use App\Models\Assessment;
use App\Models\AssessmentScore;
use App\Models\Employee;
use App\Repositories\AssessmentRepository;
use Illuminate\Support\Facades\DB;
use App\Enums\AssessmentType;
use App\Enums\AssessmentStatus;
use App\Models\AssessmentCategory;
use App\Services\AssessmentCalculatorService;
use Illuminate\Validation\ValidationException;

class AssessmentService
{
    protected $repository;
    protected $calculatorService;

    public function __construct(AssessmentRepository $repository, AssessmentCalculatorService $calculatorService)
    {
        $this->repository = $repository;
        $this->calculatorService = $calculatorService;
    }

    /**
     * Get Assessment Form Data (Categories & Indicators)
     */
    public function getAssessmentFormData()
    {
        return AssessmentCategory::with(['indicators' => function($q) {
            $q->where('is_active', true)->orderBy('display_order', 'asc');
        }])->where('is_active', true)
          ->orderBy('display_order', 'asc')
          ->get();
    }

    /**
     * Store assessment result
     */
    public function storeAssessment(Employee $assessor, Employee $target, string $type, array $scoresData, ?string $notes = null)
    {
        $activePeriod = $this->repository->getActivePeriod();

        if (!$activePeriod) {
            throw ValidationException::withMessages([
                'period' => 'Tidak ada periode penilaian yang aktif (OPEN).',
            ]);
        }

        // Validate if already assessed
        $existing = $this->repository->getAssessmentStatus($activePeriod->id, $assessor->id, $target->id, $type);
        if ($existing) {
            throw ValidationException::withMessages([
                'duplicate' => 'Anda sudah melakukan penilaian ini pada periode aktif.',
            ]);
        }

        // Validate peer constraints
        if ($type === AssessmentType::PEER->value) {
            $mySubmittedPeersCount = Assessment::where('period_id', $activePeriod->id)
                ->where('assessor_id', $assessor->id)
                ->where('assessment_type', AssessmentType::PEER->value)
                ->where('status', AssessmentStatus::SUBMITTED->value)
                ->count();

            if ($mySubmittedPeersCount >= 3) {
                throw ValidationException::withMessages([
                    'peer_assessor_limit' => 'Anda hanya dapat menilai maksimal 3 rekan kerja.',
                ]);
            }

            $receivedPeerAssessments = Assessment::where('period_id', $activePeriod->id)
                ->where('employee_id', $target->id)
                ->where('assessment_type', AssessmentType::PEER->value)
                ->where('status', AssessmentStatus::SUBMITTED->value)
                ->count();

            if ($receivedPeerAssessments >= 3) {
                throw ValidationException::withMessages([
                    'peer_limit' => 'Pegawai ini sudah mendapatkan 3 penilaian rekan kerja dan tidak bisa dinilai lagi.',
                ]);
            }
        }

        return DB::transaction(function () use ($activePeriod, $assessor, $target, $type, $scoresData, $notes) {
            $assessment = Assessment::create([
                'period_id' => $activePeriod->id,
                'assessor_id' => $assessor->id,
                'employee_id' => $target->id,
                'assessment_type' => $type,
                'status' => AssessmentStatus::SUBMITTED->value,
                'notes' => $notes,
                'submitted_at' => now(),
            ]);

            $scoreRecords = [];
            foreach ($scoresData as $indicatorId => $data) {
                $scoreNum = is_array($data) ? ($data['score'] ?? 0) : $data;
                $commentVal = is_array($data) ? ($data['comment'] ?? null) : null;

                $scoreRecords[] = [
                    'id' => (string) \Illuminate\Support\Str::uuid(),
                    'assessment_id' => $assessment->id,
                    'indicator_id' => $indicatorId,
                    'score' => (float) $scoreNum,
                    'comment' => $commentVal,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            AssessmentScore::insert($scoreRecords);

            // Real-time automatic calculation for target employee
            $this->calculatorService->calculateEmployee($target, $activePeriod);

            return $assessment;
        });
    }
}
