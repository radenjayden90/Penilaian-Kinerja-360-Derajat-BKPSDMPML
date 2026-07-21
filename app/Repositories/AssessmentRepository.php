<?php

namespace App\Repositories;

use App\Models\Employee;
use App\Models\Assessment;
use App\Models\Period;
use Illuminate\Support\Facades\DB;

class AssessmentRepository
{
    /**
     * Get active period
     */
    public function getActivePeriod()
    {
        return Period::getActivePeriod();
    }

    /**
     * Get Superior of an employee
     */
    public function getSuperior(Employee $employee)
    {
        if (!$employee->supervisor_id) {
            return null;
        }

        $roleName = strtolower($employee->role?->name ?? '');
        $positionName = strtolower($employee->position?->name ?? '');
        
        // Kepala BKPSDM & Kabid do not evaluate superiors
        if ($employee->position?->level == '1' || $employee->position?->level == '2' || str_contains($positionName, 'kepala bkpsdm') || str_contains($positionName, 'kepala bidang') || str_contains($positionName, 'kabid')) {
            return null;
        }

        return Employee::where('id', $employee->supervisor_id)->where('is_active', true)->first();
    }

    /**
     * Get Subordinates of an employee
     */
    public function getSubordinates(Employee $employee)
    {
        $roleName = strtolower($employee->role?->name ?? '');
        $positionName = strtolower($employee->position?->name ?? '');
        $isKepalaBkpsdm = ($employee->position?->level == '1' || str_contains($positionName, 'kepala bkpsdm'));
        $isKabid = ($roleName === 'kabid' || $roleName === 'head' || $employee->position?->level == '2' || str_contains($positionName, 'kepala bidang') || str_contains($positionName, 'kabid'));

        // 1. Kepala BKPSDM: Hanya menilai para Kabid (level 2), tidak menilai staff
        if ($isKepalaBkpsdm) {
            return Employee::where('is_active', true)
                ->where('id', '!=', $employee->id)
                ->whereHas('position', function($q) {
                    $q->where('level', '2')->orWhere(\Illuminate\Support\Facades\DB::raw('LOWER(name)'), 'LIKE', '%kepala bidang%')->orWhere(\Illuminate\Support\Facades\DB::raw('LOWER(name)'), 'LIKE', '%kabid%');
                })
                ->with(['department', 'position'])
                ->get();
        }

        // 2. Kepala Bidang (Kabid): Menilai seluruh bawahan dalam satu bidang yang sama
        if ($isKabid) {
            return Employee::where('department_id', $employee->department_id)
                ->where('id', '!=', $employee->id)
                ->where('is_active', true)
                ->where(function($q) use ($employee) {
                    if ($employee->position && $employee->position->level) {
                        $level = (int)$employee->position->level;
                        $q->whereHas('position', function($pQuery) use ($level) {
                            $pQuery->where('level', '>', $level);
                        })->orWhereNull('position_id');
                    }
                })
                ->with(['department', 'position'])
                ->get();
        }

        return Employee::where('supervisor_id', $employee->id)
            ->where('is_active', true)
            ->with(['department', 'position'])
            ->get();
    }

    /**
     * Get eligible Peers for an employee
     */
    public function getEligiblePeers(Employee $employee, Period $activePeriod)
    {
        $roleName = strtolower($employee->role?->name ?? '');
        $positionName = strtolower($employee->position?->name ?? '');
        $isKepalaBkpsdm = ($employee->position?->level == '1' || str_contains($positionName, 'kepala bkpsdm'));

        // Kepala BKPSDM tidak memiliki peers (rekan sejawat)
        if ($isKepalaBkpsdm) {
            return collect();
        }

        // Eligible peers:
        // - Not the employee themselves
        // - Not their direct supervisor
        // - Across any department/division (can be from different division, including sesama Kabid)
        // - Filter by same position level if position is set
        
        $query = Employee::where('id', '!=', $employee->id)
            ->where('is_active', true);

        if ($employee->supervisor_id) {
            $query->where('id', '!=', $employee->supervisor_id);
        }
            
        if ($employee->position && $employee->position->level) {
            $level = $employee->position->level;
            $query->whereHas('position', function($q) use ($level) {
                $q->where('level', $level);
            });
        }

        $peers = $query->with(['department', 'position'])->get();

        $eligiblePeers = collect();

        foreach ($peers as $peer) {
            // Check if THIS employee already assessed this peer
            $alreadyAssessedByMe = Assessment::where('period_id', $activePeriod->id)
                ->where('assessor_id', $employee->id)
                ->where('employee_id', $peer->id)
                ->where('assessment_type', 'PEER')
                ->where('status', 'SUBMITTED')
                ->exists();

            // Count total submitted peer assessments for this target peer
            $receivedPeerAssessmentsCount = Assessment::where('period_id', $activePeriod->id)
                ->where('employee_id', $peer->id)
                ->where('assessment_type', 'PEER')
                ->where('status', 'SUBMITTED')
                ->count();

            $peer->received_assessments_count = $receivedPeerAssessmentsCount;

            if ($alreadyAssessedByMe) {
                $peer->assessment_status = 'COMPLETED';
                $eligiblePeers->push($peer);
            } else {
                if ($receivedPeerAssessmentsCount >= 3) {
                    // Quota 3/3 filled by others, still show as FULL (greyed out)
                    $peer->assessment_status = 'FULL';
                    $eligiblePeers->push($peer);
                } else {
                    // Quota available (< 3), pending assessment
                    $peer->assessment_status = 'PENDING';
                    $eligiblePeers->push($peer);
                }
            }
        }

        return $eligiblePeers;
    }

    /**
     * Get assessment status for a specific assessor and target in a period
     */
    public function getAssessmentStatus($periodId, $assessorId, $targetId, $type)
    {
        return Assessment::where('period_id', $periodId)
            ->where('assessor_id', $assessorId)
            ->where('employee_id', $targetId)
            ->where('assessment_type', $type)
            ->first();
    }
}
