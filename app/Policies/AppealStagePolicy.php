<?php

namespace App\Policies;

use App\Enums\PermissionsEnum;
use App\Enums\RolesEnum;
use App\Models\AppealStage;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class AppealStagePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo(PermissionsEnum::GERENCIAR_ETAPA_RECURSO);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, AppealStage $appealStage): bool
    {
        return $user->hasPermissionTo(PermissionsEnum::GERENCIAR_ETAPA_RECURSO);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasPermissionTo(PermissionsEnum::GERENCIAR_ETAPA_RECURSO);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, AppealStage $appealStage): bool
    {
        return $user->hasPermissionTo(PermissionsEnum::GERENCIAR_ETAPA_RECURSO);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, AppealStage $appealStage): bool
    {
        return $user->hasRole(RolesEnum::ADMIN);
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, AppealStage $appealStage): bool
    {
        return $user->hasRole(RolesEnum::ADMIN);
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, AppealStage $appealStage): bool
    {
        return $user->hasRole(RolesEnum::ADMIN);
    }
}
