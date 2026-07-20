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
        return Period::where('is_active', true)->where('status', 'OPEN')->first();
    }

    /**
     * Get Superior of an employee
     */
    public function getSuperior(Employee $employee)
    {
        if (!$employee->supervisor_id) {
            return null;
        }
        return Employee::where('id', $employee->supervisor_id)->where('is_active', true)->first();
    }

    /**
     * Get Subordinates of an employee
     */
    public function getSubordinates(Employee $employee)
    {
        return Employee::where('supervisor_id', $employee->id)->where('is_active', true)->get();
    }

    /**
     * Get eligible Peers for an employee
     */
    public function getEligiblePeers(Employee $employee, Period $activePeriod)
    {
        // Eligible peers:
        // - Not the employee themselves
        // - Same department
        // - Same position level (for peers) or just same department is fine. The user said: "jabatan setara ATAU bidang sama. (Dalam implementasi ini, saya akan menggabungkan keduanya: level jabatan sama DAN berada di Department yang sama)"
        // Let's filter by same department and same level if position is set.
        
        $query = Employee::where('department_id', $employee->department_id)
            ->where('id', '!=', $employee->id)
            ->where('is_active', true);
            
        if ($employee->position) {
            $level = $employee->position->level;
            $query->whereHas('position', function($q) use ($level) {
                $q->where('level', $level);
            });
        }

        $peers = $query->get();

        $eligiblePeers = collect();

        foreach ($peers as $peer) {
            // Check if THIS employee already assessed this peer
            $alreadyAssessedByMe = Assessment::where('period_id', $activePeriod->id)
                ->where('assessor_id', $employee->id)
                ->where('employee_id', $peer->id)
                ->where('assessment_type', 'PEER')
                ->where('status', 'SUBMITTED')
                ->exists();

            if ($alreadyAssessedByMe) {
                continue; // Skip, already assessed
            }

            // Check how many peer assessments this peer has received
            $receivedPeerAssessments = Assessment::where('period_id', $activePeriod->id)
                ->where('employee_id', $peer->id)
                ->where('assessment_type', 'PEER')
                ->where('status', 'SUBMITTED')
                ->count();

            if ($receivedPeerAssessments < 3) {
                $eligiblePeers->push($peer);
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
