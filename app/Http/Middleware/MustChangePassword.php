<?php

namespace App\Http\Middleware;

use Closure;
use Filament\Notifications\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class MustChangePassword
{
    /**
     * Handle an incoming request.
     *
     * @param Closure(Request):Response $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::guard('candidato')->user();

        // Check if user is authenticated and must change password
        if ($user && $user->must_change_password) {
            Notification::make()
                ->title('Alteração de Senha Obrigatória')
                ->body('Por favor, defina uma nova senha para continuar.')
                ->duration(10000)
                ->danger()
                ->send();

            // Avoid redirect loop
            if (!$request->is('candidato/profile')) {
                return redirect()->route('filament.candidato.auth.profile');
            }
        }

        return $next($request);
    }
}
