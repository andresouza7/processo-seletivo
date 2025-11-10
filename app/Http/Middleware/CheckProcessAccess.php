<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Process;
use Illuminate\Support\Facades\Auth;

class CheckProcessAccess
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->routeIs([
            'filament.gps.resources.processos.index',
            'filament.admin.resources.processos.index',
            'filament.gps.resources.processos.create',
            'filament.admin.resources.processos.create',
        ])) {
            return $next($request);
        }

        $user = Auth::user();

        // Extract process ID from the URL (assuming /gps/processos/{id}/...)
        $processId = $request->record;

        if (!$user || !$processId) {
            abort(403, 'Acesso negado.');
        }

        $process = Process::find($processId);

        if (!$process) {
            abort(404, 'Processo não encontrado.');
        }

        if (!$this->canAccessRecord($user, $process)) {
            abort(403, 'Você não tem permissão para acessar este processo.');
        }

        return $next($request);
    }

    private function canAccessRecord($user, Process $process): bool
    {
        if ($user->hasRole(['admin', 'dips'])) return true;

        $userRoleIds = $user->roles->pluck('id');
        
        return $process->roles()->whereIn('roles.id', $userRoleIds)->exists();
    }
}
