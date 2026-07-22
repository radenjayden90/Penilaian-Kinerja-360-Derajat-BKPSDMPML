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
     * Get Total Tugas Penilaian for an employee
     */
    public function getTotalTugasPenilaian(Employee $employee)
    {
        $roleName = strtolower($employee->role?->name ?? '');
        $positionName = strtolower($employee->position?->name ?? '');
        $isKepalaBkpsdm = ($employee->position?->level == '1' || str_contains($positionName, 'kepala bkpsdm'));
        $isKabid = ($employee->position?->level == '2' || str_contains($positionName, 'kepala bidang') || str_contains($positionName, 'kabid') || str_contains($positionName, 'sekretaris'));

        if ($isKepalaBkpsdm) {
            // Count of Kabid
            return Employee::where('is_active', true)
                ->whereHas('position', function($q) {
                    $q->where('level', '2')
                      ->orWhere(\Illuminate\Support\Facades\DB::raw('LOWER(name)'), 'LIKE', '%kepala bidang%')
                      ->orWhere(\Illuminate\Support\Facades\DB::raw('LOWER(name)'), 'LIKE', '%kabid%')
                      ->orWhere(\Illuminate\Support\Facades\DB::raw('LOWER(name)'), 'LIKE', '%sekretaris%');
                })->count();
        } elseif ($isKabid) {
            // 3 peers + subordinates
            $subordinatesCount = $this->getSubordinates($employee)->count();
            return 3 + $subordinatesCount;
        } else {
            // 3 peers + 1 superior
            return 4;
        }
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
        
        // Kepala BKPSDM & Kabid (including Sekretaris) do not evaluate superiors
        if ($employee->position?->level == '1' || $employee->position?->level == '2' || str_contains($positionName, 'kepala bkpsdm') || str_contains($positionName, 'kepala bidang') || str_contains($positionName, 'kabid') || str_contains($positionName, 'sekretaris')) {
            return null;
        }

        return Employee::where('id', $employee->supervisor_id)
            ->where('is_active', true)
            ->whereDoesntHave('role', function($q) {
                $q->whereIn('name', ['ADMIN', 'SUPER_ADMIN']);
            })->first();
    }

    /**
     * Get Subordinates of an employee
     */
    public function getSubordinates(Employee $employee)
    {
        $roleName = strtolower($employee->role?->name ?? '');
        $positionName = strtolower($employee->position?->name ?? '');
        $isKepalaBkpsdm = ($employee->position?->level == '1' || str_contains($positionName, 'kepala bkpsdm'));
        $isKabid = ($roleName === 'kabid' || $roleName === 'head' || $employee->position?->level == '2' || str_contains($positionName, 'kepala bidang') || str_contains($positionName, 'kabid') || str_contains($positionName, 'sekretaris'));

        // 1. Kepala BKPSDM: Hanya menilai para Kabid (level 2), tidak menilai staff
        if ($isKepalaBkpsdm) {
            return Employee::where('is_active', true)
                ->where('id', '!=', $employee->id)
                ->whereDoesntHave('role', function($q) {
                    $q->whereIn('name', ['ADMIN', 'SUPER_ADMIN']);
                })
                ->whereHas('position', function($q) {
                    $q->where('level', '2')->orWhere(\Illuminate\Support\Facades\DB::raw('LOWER(name)'), 'LIKE', '%kepala bidang%')->orWhere(\Illuminate\Support\Facades\DB::raw('LOWER(name)'), 'LIKE', '%kabid%')->orWhere(\Illuminate\Support\Facades\DB::raw('LOWER(name)'), 'LIKE', '%sekretaris%');
                })
                ->with(['department', 'position'])
                ->get();
        }

        // 2. Kepala Bidang (Kabid): Menilai seluruh bawahan dalam satu bidang yang sama
        if ($isKabid) {
            return Employee::where('department_id', $employee->department_id)
                ->where('id', '!=', $employee->id)
                ->where('is_active', true)
                ->whereDoesntHave('role', function($q) {
                    $q->whereIn('name', ['ADMIN', 'SUPER_ADMIN']);
                })
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
            ->whereDoesntHave('role', function($q) {
                $q->whereIn('name', ['ADMIN', 'SUPER_ADMIN']);
            })
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
        $isKabid = ($employee->position?->level == '2' || str_contains($positionName, 'kepala bidang') || str_contains($positionName, 'kabid') || str_contains($positionName, 'sekretaris'));

        // Kepala BKPSDM dan Admin tidak memiliki peers (rekan sejawat)
        if ($isKepalaBkpsdm || $employee->isAdmin()) {
            return collect();
        }

        $query = Employee::where('id', '!=', $employee->id)
            ->where('is_active', true)
            ->whereDoesntHave('role', function($q) {
                $q->whereIn('name', ['ADMIN', 'SUPER_ADMIN']);
            });

        if ($isKabid) {
            // Peers are other Kabids (level 2)
            $query->whereHas('position', function($q) {
                $q->where('level', '2')
                  ->orWhere(\Illuminate\Support\Facades\DB::raw('LOWER(name)'), 'LIKE', '%kepala bidang%')
                  ->orWhere(\Illuminate\Support\Facades\DB::raw('LOWER(name)'), 'LIKE', '%kabid%')
                  ->orWhere(\Illuminate\Support\Facades\DB::raw('LOWER(name)'), 'LIKE', '%sekretaris%');
            });
        } else {
            // Peers are other staff members (level > 2 or null, and not level 1 or 2, and not kepala bkpsdm)
            $query->where(function($q) {
                $q->whereNull('position_id')
                  ->orWhereHas('position', function($p) {
                      $p->whereNotIn('level', ['1', '2'])
                        ->where(\Illuminate\Support\Facades\DB::raw('LOWER(name)'), 'NOT LIKE', '%kepala bidang%')
                        ->where(\Illuminate\Support\Facades\DB::raw('LOWER(name)'), 'NOT LIKE', '%kabid%')
                        ->where(\Illuminate\Support\Facades\DB::raw('LOWER(name)'), 'NOT LIKE', '%sekretaris%')
                        ->where(\Illuminate\Support\Facades\DB::raw('LOWER(name)'), 'NOT LIKE', '%kepala bkpsdm%');
                  });
            });
        }

        $peers = $query->with(['department', 'position'])->get();

        $eligiblePeers = collect();

        // How many peer assessments has the logged-in user already submitted
        $mySubmittedPeersCount = Assessment::where('period_id', $activePeriod->id)
            ->where('assessor_id', $employee->id)
            ->where('assessment_type', 'PEER')
            ->where('status', 'SUBMITTED')
            ->count();

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
                } elseif ($mySubmittedPeersCount >= 3) {
                    // User has reached their peer choice limit
                    $peer->assessment_status = 'LIMIT_REACHED';
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
