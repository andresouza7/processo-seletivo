<?php

namespace App\Actions;

use Throwable;
use App\Models\InscricaoPessoa;
use App\Notifications\ResetEmailNotification;
use Closure;
use Filament\Notifications\Notification;
use Illuminate\Support\Str;

class ResetCandidatoEmailAction
{
    public function reset(InscricaoPessoa $user, string $newEmail, Closure $callback)
    {
        try {
            // checa se o email já está em uso
            $emailMatch = InscricaoPessoa::where('email', $newEmail)->first();

            if ($emailMatch) {
                Notification::make()
                    ->title('Erro')
                    ->body('Este email já está em uso!')
                    ->danger()
                    ->send();
                return;
            }

            // salva novo email
            $user->email = $newEmail;
            $user->email_verified_at = now();

            // gera senha temporária
            $senha = Str::random(8);
            $hash = bcrypt($senha);
            $user->forceFill(['password' => $hash]);
            $user->must_change_password = true;
            $user->save();

            // comunica por email
            $user->notify(new ResetEmailNotification($senha));

            $callback();
        } catch (Throwable $th) {
            //throw $th;
            Notification::make()
                ->title('Erro.')
                ->body('Não foi possível executar a operação.')
                ->danger()
                ->send();
        }
    }
}
