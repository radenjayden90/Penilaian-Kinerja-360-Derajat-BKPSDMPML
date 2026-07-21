<?php

namespace App\Policies;
use App\Models\User;
use App\Models\AssessmentIndicator;

class AssessmentIndicatorPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true; // For now, all authenticated users can view. Adjust based on exact Role logic later.
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, AssessmentIndicator $model): bool
    {
        return true;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, AssessmentIndicator $model): bool
    {
        return true;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, AssessmentIndicator $model): bool
    {
        return true;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, AssessmentIndicator $model): bool
    {
        return true;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, AssessmentIndicator $model): bool
    {
        return true;
    }
}
