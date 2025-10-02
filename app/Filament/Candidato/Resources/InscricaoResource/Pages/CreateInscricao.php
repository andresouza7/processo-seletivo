<?php

namespace App\Filament\Candidato\Resources\InscricaoResource\Pages;

use App\Filament\Candidato\Resources\InscricaoResource;
use App\Models\Inscricao;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;
use Filament\Actions\Action;
use App\Notifications\NovaInscricaoNotification;
use Filament\Notifications\Notification;

class CreateInscricao extends CreateRecord
{
    protected static string $resource = InscricaoResource::class;

    protected static ?string $title = 'Nova Inscrição';

    protected static bool $canCreateAnother = false;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['cod_inscricao'] = Inscricao::gerarCodigoUnico();
        $data['idinscricao_pessoa'] = Auth::guard('candidato')->id();
        $data['data_hora'] = now();
        $data['qual_atendimento'] = $data['qual_atendimento'] ?? '';
        $data['observacao'] = $data['observacao'] ?? '';

        // tipos de vaga cadastrados na tabela de vagas
        $data['idtipo_vaga'] = $data['pcd'] ? 3 : 1;

        return $data;
    }

    protected function beforeCreate(): void
    {
        // 1. Verificar dados pendentes do usuário
        $pessoa = Auth::guard('candidato')->user();

        if ($pessoa && $pessoa->possuiDadosPendentes()) {
            Notification::make()
                ->danger()
                ->title('Dados incompletos!')
                ->body('Você precisa completar seus dados antes de realizar a inscrição.')
                ->persistent()
                ->actions([
                    \Filament\Notifications\Actions\Action::make('editarPerfil')
                        ->button()
                        ->url(route('filament.candidato.pages.meus-dados'), shouldOpenInNewTab: false),
                ])
                ->send();

            $this->halt();
        }
    }

    protected function getCreateFormAction(): Action
    {
        return Action::make('create')
            ->label('Realizar Inscrição')
            ->submit('create')
            ->keyBindings(['mod+s']);
    }

    // ✅ Change breadcrumb title
    public function getBreadcrumb(): string
    {
        return 'Nova Inscrição'; // Custom breadcrumb title
    }

    protected function getCreatedNotificationTitle(): ?string
    {
        return 'Inscrição realizada com sucesso!';
    }

    protected function afterCreate(): void
    {
        // Envia notificação ao usuário autenticado
        $this->record->inscricao_pessoa->notify(new NovaInscricaoNotification($this->record));
    }
}
