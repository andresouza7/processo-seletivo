<?php

namespace App\Policies;

use App\Enums\PermissionsEnum;
use App\Enums\RolesEnum;
use App\Models\Application;
use App\Models\User;
use App\Models\Candidate; // Add this import if you need to check Candidate
use Illuminate\Auth\Access\Response;

class ApplicationPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny($user): bool
    {
        // If the user is an applicant (candidato guard)
        if ($user instanceof Candidate) {
            return true; // Adjust based on the applicant's permissions
        }

        return $user->hasPermissionTo(PermissionsEnum::CONSULTAR_INSCRICAO);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view($user, Application $application): bool
    {
        if ($user instanceof Candidate) {
            // Only allow viewing if the applicant is related to the Application
            return $user->id === $application->candidate_id;
        }

        return $user->hasPermissionTo(PermissionsEnum::CONSULTAR_INSCRICAO);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create($user): bool
    {
        return $user instanceof Candidate;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update($user, Application $application): bool
    {
        return $user->hasRole(RolesEnum::ADMIN);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete($user, Application $application): bool
    {
        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore($user, Application $application): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete($user, Application $application): bool
    {
        return false;
    }
}
