<?php

namespace App\Services\SelectionProcess;

use App\Models\User;
use App\Models\UserRole;
use Carbon\Carbon;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;

class PermissionService
{
    public function getUserRole(User $user): ?Role
    {
        return $user->roles()?->first();
    }

    public function getUserRoleInfo(User $user): ?UserRole
    {
        return $user->userRoles()?->latest('created_at')->first();
    }

    public function assignUserRole(User $user, $roleId, $createDoc): UserRole
    {
        // Assign a new role and revoke existing ones
        return DB::transaction(function () use ($user, $roleId, $createDoc) {
            $role = Role::find($roleId);
            $roleInfo = $this->getUserRoleInfo($user);

            $this->revokeUserRole($roleInfo);

            $user->syncRoles([$role]);

            return UserRole::create([
                'user_id' => $user->id,
                'role_id' => $roleId,
                'create_doc' => $createDoc
            ]);
        });
    }

    public function revokeUserRole(?UserRole $userRole, string $message = 'Expirado automaticamente pelo sistema'): void
    {
        if ($userRole && !$userRole->revoked_at) {
            $userRole->update([
                'revoked_at' => now(),
                'revoke_doc' => $message
            ]);

            // Detach the role from the user
            $userRole->user->removeRole($userRole->role);
        }
    }

    public function revokeExpiredUserRoles(User $user): void
    {
        foreach ($user->userRoles()->whereNull('revoked_at')->get() as $role) {
            if ($role->isExpired()) {
                $this->revokeUserRole($role);
            }
        }

        $lastRole = $this->getUserRoleInfo($user);
        
        if ($lastRole && $lastRole->isExpired()) {
            $expiryDate = Carbon::parse($lastRole->revoked_at)->format('d/m/Y');

            Auth::logout();

            Notification::make()
                ->title('Permissão Expirada')
                ->body("Sua permissão de acesso expirou em {$expiryDate}, contacte a DINFO para renovar")
                ->danger()
                ->persistent()
                ->send();
        }
    }
}
