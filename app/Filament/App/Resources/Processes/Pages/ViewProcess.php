<?php

namespace App\Filament\App\Resources\Processes\Pages;

use App\Filament\App\Resources\Processes\ProcessResource;
use Filament\Actions\EditAction;
use Filament\Facades\Filament;
use Filament\Resources\Pages\ViewRecord;
use Filament\Support\Facades\FilamentView;
use Filament\View\PanelsRenderHook;

class ViewProcess extends ViewRecord
{
    protected static string $resource = ProcessResource::class;

    public function mount(int | string $record): void
    {
        $this->record = $this->resolveRecord($record);

        $this->authorizeAccess();

        if (! $this->hasInfolist()) {
            $this->fillForm();
        }

        FilamentView::registerRenderHook(
            PanelsRenderHook::HEAD_START,
            fn() => "
            <meta name=\"robots\" content=\"index, follow\">
            <meta name=\"description\" content=\"{$this->record->title}\">
            <meta name=\"keywords\" content=\"processos, seletivo\">
            "
        );
    }


    public function getTitle(): string
    {
        return 'Consultar Processo';
    }

    protected function getHeaderActions(): array
    {
        return [
            // EditAction::make(),
        ];
    }
}
