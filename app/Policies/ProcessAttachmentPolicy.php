<?php

namespace App\Policies;

use App\Models\ProcessAttachment;
use App\Models\User;
use Illuminate\Auth\Access\Response;
use Spatie\Permission\Models\Role;

class ProcessAttachmentPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('view attachment');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, ProcessAttachment $processAttachment): bool
    {
        return $user->hasPermissionTo('view attachment');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasPermissionTo('create attachment');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, ProcessAttachment $processAttachment): bool
    {
        return $user->hasPermissionTo('edit attachment');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, ProcessAttachment $processAttachment): bool
    {
        return $user->hasRole('admin');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, ProcessAttachment $processAttachment): bool
    {
        return $user->hasRole('admin');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, ProcessAttachment $processAttachment): bool
    {
        return $user->hasRole('admin');
    }
}
