<?php

namespace App\Policies;

use App\Models\Appeal;
use App\Models\Candidate;
use App\Models\User;
use Illuminate\Auth\Access\Response;
use Illuminate\Foundation\Auth\User as AuthUser;

use function PHPUnit\Framework\isNull;

class AppealPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(AuthUser $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(AuthUser $user, Appeal $appeal): bool
    {
        return true;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(AuthUser $user): bool
    {
        return $user instanceof Candidate;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Appeal $appeal): bool
    {
        return $user->hasRole('admin|gestor|avaliador') && !$appeal->result;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Appeal $appeal): bool
    {
        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Appeal $appeal): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Appeal $appeal): bool
    {
        return false;
    }
}
