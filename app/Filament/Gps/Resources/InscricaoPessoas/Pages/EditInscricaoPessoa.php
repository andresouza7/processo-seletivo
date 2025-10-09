<?php

namespace App\Filament\Gps\Resources\InscricaoPessoas\Pages;

use App\Actions\ResetCandidatoEmailAction;
use App\Filament\Gps\Resources\InscricaoPessoas\InscricaoPessoaResource;
use App\Models\Candidate;
use Filament\Actions\Action;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Filament\Actions\DeleteAction;

class EditInscricaoPessoa extends EditRecord
{
    protected static string $resource = InscricaoPessoaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
            Action::make('resetEmail')
                ->label('Redefinir Email')
                ->modalDescription('O email será alterado e uma senha temporária será enviada para o endereço fornecido.')
                ->modalSubmitActionLabel('Confirmar')
                ->schema([
                    TextInput::make('email')->email()->required()->unique('candidate', 'email', ignoreRecord: true)
                ])
                ->action(function (Candidate $record, array $data) {

                    $action = new ResetCandidatoEmailAction();
                    $action->reset($record, $data['email'], fn() => Notification::make()
                        ->title('Email alterado com sucesso')
                        ->body('Uma senha temporária foi enviada para o email do usuário')
                        ->success()
                        ->persistent()
                        ->send());
                })
                ->color('danger')
                ->icon('heroicon-o-key'),
        ];
    }
}
