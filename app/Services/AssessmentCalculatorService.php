<?php

namespace App\Services;

use App\Models\Employee;
use App\Models\Period;
use App\Models\Assessment;
use App\Models\AssessmentResult;
use App\Enums\ResultCategory;
use App\Enums\CalculationStatus;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AssessmentCalculatorService
{
    /**
     * Calculate all active employees for a given period
     */
    public function calculateAll(Period $period)
    {
        Log::info("Starting mass calculation for period: {$period->name}");
        $processed = 0;

        Employee::where('is_active', true)
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
            ->chunk(100, function ($employees) use ($period, &$processed) {
                foreach ($employees as $employee) {
                    try {
                        $this->calculateEmployee($employee, $period);
                        $processed++;
                    } catch (\Exception $e) {
                        Log::error("Failed to calculate for employee {$employee->id}: " . $e->getMessage());
                    }
                }
            });

        Log::info("Completed mass calculation. Processed {$processed} employees.");
        return $processed;
    }

    /**
     * Calculate for a single employee
     */
    public function calculateEmployee(Employee $employee, Period $period)
    {
        Log::info("Calculating for employee: {$employee->name} ({$employee->nip})");

        $posName = strtolower($employee->position?->name ?? '');
        $isKepalaBkpsdm = ($employee->position?->level == '1' || str_contains($posName, 'kepala bkpsdm'));
        if ($employee->isAdmin() || $isKepalaBkpsdm) {
            Log::info("Skipping calculation for Admin/Kepala BKPSDM: {$employee->name}");
            return null;
        }

        // Determine if employee is Kabid (level 2)
        $isKabid = ($employee->position?->level == '2' || str_contains($posName, 'kepala bidang') || str_contains($posName, 'kabid') || str_contains($posName, 'sekretaris'));

        // Fetch all SUBMITTED assessments for this employee in this period
        $assessments = Assessment::with(['scores.indicator.category'])
            ->where('employee_id', $employee->id)
            ->where('period_id', $period->id)
            ->where('status', 'SUBMITTED')
            ->get();

        $superiorAssessments = $assessments->where('assessment_type', 'SUPERIOR');
        $peerAssessments = $assessments->where('assessment_type', 'PEER');
        $subordinateAssessments = $assessments->where('assessment_type', 'SUBORDINATE');

        // Validation Logic
        $pendingReason = [];
        
        if ($isKabid) {
            // Atasan (Kepala BKPSDM) assessment (type SUBORDINATE)
            if ($subordinateAssessments->count() < 1) {
                $pendingReason[] = "Atasan (Kepala BKPSDM) belum melakukan penilaian.";
            }

            // Peers (Rekan Kabid) assessment (type PEER)
            if ($peerAssessments->count() < 3) {
                $pendingReason[] = "Penilaian rekan kerja kurang dari 3 (baru " . $peerAssessments->count() . ").";
            }

            // Subordinate (Staff) assessment (type SUPERIOR)
            $activeSubordinatesCount = Employee::where('supervisor_id', $employee->id)->where('is_active', true)->count();
            if ($superiorAssessments->count() < $activeSubordinatesCount) {
                $pendingReason[] = "Belum semua bawahan melakukan penilaian (" . $superiorAssessments->count() . "/" . $activeSubordinatesCount . ").";
            }
        } else {
            // Staff
            // Atasan (Kabid) assessment (type SUBORDINATE)
            if ($subordinateAssessments->count() < 1) {
                $pendingReason[] = "Atasan (Kabid) belum melakukan penilaian.";
            }

            // Peers (Rekan Staff) assessment (type PEER)
            if ($peerAssessments->count() < 3) {
                $pendingReason[] = "Penilaian rekan kerja kurang dari 3 (baru " . $peerAssessments->count() . ").";
            }
        }

        $status = count($pendingReason) > 0 ? CalculationStatus::PENDING : CalculationStatus::COMPLETE;
        $reason = count($pendingReason) > 0 ? implode(' ', $pendingReason) : null;

        // Calculate Averages
        $superiorAvg = $superiorAssessments->count() > 0 ? $this->calculateAssessmentAverage($superiorAssessments) : 0;
        $peerAvg = $peerAssessments->count() > 0 ? $this->calculateAssessmentAverage($peerAssessments) : 0;
        $subordinateAvg = $subordinateAssessments->count() > 0 ? $this->calculateAssessmentAverage($subordinateAssessments) : 0;

        // Calculate Final Score & Weights
        if ($isKabid) {
            $superiorWeight = 0.20; // staff / subordinates (SUPERIOR type)
            $peerWeight = 0.30;     // rekan kabid (PEER type)
            $subordinateWeight = 0.50; // kepala bkpsdm / superior (SUBORDINATE type)
        } else {
            $superiorWeight = 0.00;
            $peerWeight = 0.50;     // rekan staff (PEER type)
            $subordinateWeight = 0.50; // kabid (SUBORDINATE type)
        }

        $rawScore = ($superiorAvg * $superiorWeight) + ($peerAvg * $peerWeight) + ($subordinateAvg * $subordinateWeight);
        
        // Scale 1-10 to 10-100 for category evaluation
        $finalScore = $rawScore * 10;

        // Determine Category
        $category = $this->determineCategory($finalScore);

        // Save
        return $this->saveResult($employee, $period, [
            'superior_average' => $superiorAvg,
            'peer_average' => $peerAvg,
            'subordinate_average' => $subordinateAvg,
            'superior_weight' => $superiorWeight,
            'peer_weight' => $peerWeight,
            'subordinate_weight' => $subordinateWeight,
            'final_score' => $finalScore,
            'category' => $category,
            'status' => $status,
            'pending_reason' => $reason,
            'calculated_at' => now(),
        ]);
    }

    /**
     * Calculate average of a collection of assessments
     */
    private function calculateAssessmentAverage($assessments)
    {
        if ($assessments->isEmpty()) return 0;

        $assessmentScores = collect();

        foreach ($assessments as $assessment) {
            if ($assessment->scores->count() > 0) {
                // Group scores by category
                $groupedScores = $assessment->scores->groupBy(function($score) {
                    return $score->indicator->category_id ?? 'unknown';
                });

                $totalAssessmentScore = 0;

                foreach ($groupedScores as $categoryId => $scores) {
                    $category = $scores->first()->indicator->category ?? null;
                    if ($category) {
                        $categoryAverage = $scores->avg('score');
                        // Multiply category average by its weight (e.g., 14.28 / 100)
                        $weightedCategoryScore = $categoryAverage * ($category->weight / 100);
                        $totalAssessmentScore += $weightedCategoryScore;
                    }
                }
                
                $assessmentScores->push($totalAssessmentScore);
            }
        }

        return $assessmentScores->isEmpty() ? 0 : $assessmentScores->avg();
    }

    private function determineCategory($score)
    {
        if ($score >= 90) return ResultCategory::VERY_GOOD;
        if ($score >= 76) return ResultCategory::GOOD;
        if ($score >= 61) return ResultCategory::FAIR;
        return ResultCategory::NEEDS_IMPROVEMENT;
    }

    private function saveResult(Employee $employee, Period $period, array $data)
    {
        return AssessmentResult::updateOrCreate(
            ['employee_id' => $employee->id, 'period_id' => $period->id],
            $data
        );
    }
}
