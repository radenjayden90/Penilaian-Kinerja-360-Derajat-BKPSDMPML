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

        Employee::where('is_active', true)->chunk(100, function ($employees) use ($period, &$processed) {
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

        // 1. Determine if employee has subordinates
        $hasSubordinates = Employee::where('supervisor_id', $employee->id)->where('is_active', true)->exists();

        // 2. Fetch all SUBMITTED assessments for this employee in this period
        $assessments = Assessment::with(['scores.indicator.category'])
            ->where('employee_id', $employee->id)
            ->where('period_id', $period->id)
            ->where('status', 'SUBMITTED')
            ->get();

        $superiorAssessments = $assessments->where('assessment_type', 'SUPERIOR');
        $peerAssessments = $assessments->where('assessment_type', 'PEER');
        $subordinateAssessments = $assessments->where('assessment_type', 'SUBORDINATE');

        // 3. Validation Logic
        $pendingReason = [];
        
        // Superior validation (must have exactly 1 if they have a superior registered)
        if ($employee->supervisor_id && $superiorAssessments->count() < 1) {
            $pendingReason[] = "Atasan belum melakukan penilaian.";
        }

        // Peer validation (must have exactly 3)
        if ($peerAssessments->count() < 3) {
            $pendingReason[] = "Penilaian rekan kerja kurang dari 3 (baru " . $peerAssessments->count() . ").";
        }

        // Subordinate validation (if applicable, must have ALL active subordinates submit)
        if ($hasSubordinates) {
            $activeSubordinatesCount = Employee::where('supervisor_id', $employee->id)->where('is_active', true)->count();
            if ($subordinateAssessments->count() < $activeSubordinatesCount) {
                $pendingReason[] = "Belum semua bawahan melakukan penilaian (" . $subordinateAssessments->count() . "/" . $activeSubordinatesCount . ").";
            }
        }

        // If not ready
        if (count($pendingReason) > 0) {
            return $this->saveResult($employee, $period, [
                'status' => CalculationStatus::PENDING,
                'pending_reason' => implode(' ', $pendingReason)
            ]);
        }

        // 4. Calculate Averages
        $superiorAvg = $superiorAssessments->count() > 0 ? $this->calculateAssessmentAverage($superiorAssessments) : 0;
        $peerAvg = $this->calculateAssessmentAverage($peerAssessments);
        $subordinateAvg = $hasSubordinates ? $this->calculateAssessmentAverage($subordinateAssessments) : 0;

        // 5. Calculate Final Score & Weights
        $superiorWeight = 0.50;
        $peerWeight = $hasSubordinates ? 0.30 : 0.50;
        $subordinateWeight = $hasSubordinates ? 0.20 : 0.00;

        $rawScore = ($superiorAvg * $superiorWeight) + ($peerAvg * $peerWeight) + ($subordinateAvg * $subordinateWeight);
        
        // Scale 1-5 to 10-100 for category evaluation
        $finalScore = $rawScore * 20;

        // 6. Determine Category
        $category = $this->determineCategory($finalScore);

        // 7. Save
        return $this->saveResult($employee, $period, [
            'superior_average' => $superiorAvg,
            'peer_average' => $peerAvg,
            'subordinate_average' => $subordinateAvg,
            'superior_weight' => $superiorWeight,
            'peer_weight' => $peerWeight,
            'subordinate_weight' => $subordinateWeight,
            'final_score' => $finalScore,
            'category' => $category,
            'status' => CalculationStatus::COMPLETE,
            'pending_reason' => null,
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
