<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Assessment;

class AssessmentPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true; 
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Assessment $assessment): bool
    {
        // Users can only view their own given assessments or their own received assessments.
        // Wait, for received assessments we might want to restrict it based on rules later. 
        // For now, they can view assessments they made. Admin can view all.
        if ($user->role?->name === 'Super Admin' || $user->role?->name === 'Admin BKPSDM') {
            return true;
        }

        return $assessment->assessor_id === $user->id;
    }
}
