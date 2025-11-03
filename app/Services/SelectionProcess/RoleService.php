<?php

namespace App\Services\SelectionProcess;

use App\Models\User;
use App\Models\UserRole;
use Carbon\Carbon;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;

class RoleService
{
    /** Get the user’s current Spatie Role model. */
    public function getUserRole(User $user): ?Role
    {
        return $user->roles()->first();
    }

    /** Get the user’s latest UserRole record. */
    public function getUserRoleInfo(User $user): ?UserRole
    {
        return $user->userRoles()->latest('created_at')->first();
    }

    /** Assign a new role to the user, revoking any previous one. */
    public function assignUserRole(User $user, int $roleId, string $createDoc, int $duration): UserRole
    {
        return DB::transaction(function () use ($user, $roleId, $createDoc, $duration) {
            $this->revokeUserRole($this->getUserRoleInfo($user));

            $role = Role::findOrFail($roleId);
            $user->syncRoles([$role]);

            return UserRole::create([
                'user_id'    => $user->id,
                'role_id'    => $roleId,
                'create_doc' => $createDoc,
                'expires_at' => now()->addDays($duration)
            ]);
        });
    }

    /** Revoke a user role if not already revoked. */
    public function revokeUserRole(?UserRole $userRole, string $reason = 'Expirado automaticamente pelo sistema'): void
    {
        if (!$userRole) {
            return;
        }

        $userRole->update([
            'expires_at' => now(),
            'revoke_doc' => $reason,
        ]);

        $userRole->user->removeRole($userRole->role);
    }

    /** Revoke all expired roles for the user and notify if their current one is expired. */
    public function revokeExpiredUserRoles(User $user): void
    {
        $user->userRoles()
            ->whereNull('expires_at')
            ->get()
            ->each(fn($role) => $role->isExpired() && $this->revokeUserRole($role));

        $lastRole = $this->getUserRoleInfo($user);

        if ($lastRole?->isExpired()) {
            $this->handleExpiredRoleNotification($lastRole);
        }
    }

    /** Notify and log out the user if their role is expired. */
    protected function handleExpiredRoleNotification(UserRole $role): void
    {
        $expiryDate = Carbon::parse($role->expires_at)->format('d/m/Y');

        Auth::logout();

        Notification::make()
            ->title('Permissão Expirada')
            ->body("Sua permissão de acesso expirou em {$expiryDate}, contacte a DINFO para renovar.")
            ->danger()
            ->persistent()
            ->send();
    }
}
