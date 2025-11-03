<?php

namespace App\Filament\Candidato\Resources\Applications\Pages;

use App\Filament\Candidato\Resources\Applications\ApplicationResource;
use App\Models\Application;
use App\Models\Process;
use App\Services\SelectionProcess\ApplicationService;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;

class CreateApplication extends CreateRecord
{
    protected static string $resource = ApplicationResource::class;
    protected static ?string $title = 'Nova Inscrição';
    protected static bool $canCreateAnother = false;

    protected ApplicationService $service;

    public function boot(): void
    {
        $this->service = app(ApplicationService::class);
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['code'] = Application::generateUniqueCode();
        $data['candidate_id'] = Auth::guard('candidato')->id();

        return $data;
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
