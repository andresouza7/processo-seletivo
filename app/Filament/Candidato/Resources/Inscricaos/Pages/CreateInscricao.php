<?php

namespace App\Filament\Candidato\Resources\Inscricaos\Pages;

use App\Filament\Candidato\Resources\Inscricaos\InscricaoResource;
use App\Models\Process;
use App\Services\SelectionProcess\ApplicationService;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;

class CreateInscricao extends CreateRecord
{
    protected static string $resource = InscricaoResource::class;
    protected static ?string $title = 'Nova Inscrição';
    protected static bool $canCreateAnother = false;

    protected ApplicationService $service;

    public function boot(): void
    {
        $this->service = app(ApplicationService::class);
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        return $this->service->prepareFormData($data);
    }

    protected function beforeCreate(): void
    {
        $candidate = Auth::guard('candidato')->user();

        // 🚨 Check missing candidate data
        if ($candidate && $candidate->hasMissingData()) {
            Notification::make()
                ->danger()
                ->title('Dados incompletos!')
                ->body('Você precisa completar seus dados antes de realizar a inscrição.')
                ->persistent()
                ->actions([
                    Action::make('editarPerfil')
                        ->button()
                        ->url(route('filament.candidato.pages.meus-dados')),
                ])
                ->send();

            $this->halt();
            return;
        }

        $data = $this->service->prepareFormData($this->form->getState());
        $process = Process::find($data['process_id']); // supondo que você tenha um método para buscar o processo

        // 🚨 Verifica se o processo restringe a inscrição para mais de um tipo de vaga
        if ($process->single_application) {
            $existing = $this->service->checkExistingDifferentPosition($candidate->id, $data);

            if ($existing) {
                Notification::make()
                    ->warning()
                    ->title('Inscrição única por vaga')
                    ->body('Você já se inscreveu em outra vaga. Veja sua inscrição abaixo.')
                    ->persistent()
                    ->actions([
                        Action::make('verInscricao')
                            ->label('Ver Inscrição')
                            ->button()
                            ->color('primary')
                            ->url(static::getResource()::getUrl('view', ['record' => $existing])),
                    ])
                    ->send();

                $this->halt();
                return;
            }
        }
    }

    protected function afterCreate(): void
    {
        $this->service->notifyApplicationCreated($this->record);
    }

    protected function getCreateFormAction(): Action
    {
        return Action::make('create')
            ->label('Realizar Inscrição')
            ->submit('create')
            ->keyBindings(['mod+s']);
    }

    public function getBreadcrumb(): string
    {
        return 'Nova Inscrição';
    }

    protected function getCreatedNotificationTitle(): ?string
    {
        return 'Inscrição realizada com sucesso!';
    }
}
