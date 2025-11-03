<?php

namespace App\Filament\Candidato\Resources\Applications\Pages;

use App\Filament\Candidato\Resources\Applications\ApplicationResource;
use Filament\Actions\Action;
use Filament\Resources\Pages\Concerns\InteractsWithRecord;
use Filament\Resources\Pages\Page;

class PrintApplication extends Page
{
    use InteractsWithRecord;

    protected static string $resource = ApplicationResource::class;

    protected string $view = 'filament.candidato.resources.applications.pages.print-application';

    protected static ?string $title = 'Visualizar Inscrição';

    public function mount(int|string $record): void
    {
        $this->record = $this->resolveRecord($record);
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('print')
                ->label('Imprimir')
                ->icon('heroicon-o-printer')
                ->action(fn () => $this->js('window.print()')), // usa o print do navegador
        ];
    }
}
