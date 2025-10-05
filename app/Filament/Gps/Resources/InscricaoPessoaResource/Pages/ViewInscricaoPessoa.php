<?php

namespace App\Filament\Gps\Resources\InscricaoPessoaResource\Pages;

use App\Filament\Gps\Resources\InscricaoPessoaResource;
use Filament\Resources\Pages\ViewRecord;
use App\Actions\ResetCandidatoEmailAction;
use App\Actions\ResetCandidatoPasswordAction;
use App\Models\InscricaoPessoa;
use Filament\Actions\Action;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Actions\DeleteAction;

class ViewInscricaoPessoa extends ViewRecord
{
    protected static string $resource = InscricaoPessoaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('resetEmail')
                ->label('Redefinir Email')
                ->modalDescription('O email será alterado e uma senha temporária será enviada para o endereço fornecido.')
                ->modalSubmitActionLabel('Confirmar')
                ->schema([
                    TextInput::make('email')->label('Novo Email')->email()->required()->unique('inscricao_pessoa', 'email', ignoreRecord: true)
                ])
                ->action(function (InscricaoPessoa $record, array $data, ResetCandidatoPasswordAction $resetAction) {

                    $resetAction->reset($record);

                    // $action = new ResetCandidatoEmailAction();
                    // $action->reset($record, $data['email'], fn() => Notification::make()
                    //     ->title('Email alterado com sucesso')
                    //     ->body('Uma senha temporária foi enviada para o email do usuário')
                    //     ->success()
                    //     ->persistent()
                    //     ->send());
                })
                ->color('danger')
                ->icon('heroicon-o-key'),
        ];
    }
}
