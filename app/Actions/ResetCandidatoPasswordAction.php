<?php

namespace App\Actions;

use App\Models\InscricaoPessoa;
use Closure;
use Filament\Notifications\Notification;
use Illuminate\Support\Str;

class ResetCandidatoPasswordAction
{
    public function reset(InscricaoPessoa $user)
    {
        try {
            // gera senha temporária
            $senha = Str::random(8);
            $hash = bcrypt($senha);
            $user->forceFill(['password' => $hash]);
            $user->save();

             Notification::make()
                    ->title('nova senha gerada')
                    ->body($senha)
                    ->success()
                    ->send();
        } catch (\Throwable $th) {
            //throw $th;
            Notification::make()
                ->title('Erro.')
                ->body('Não foi possível executar a operação.')
                ->danger()
                ->send();
        }
    }
}
