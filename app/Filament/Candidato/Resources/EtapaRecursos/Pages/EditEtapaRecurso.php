<?php

namespace App\Filament\Candidato\Resources\EtapaRecursos\Pages;

use App\Filament\Candidato\Resources\EtapaRecursos\EtapaRecursoResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditEtapaRecurso extends EditRecord
{
    protected static string $resource = EtapaRecursoResource::class;
    protected static ?string $title = 'Meus Recursos';
    protected static ?string $breadcrumb = 'Meus Recursos';

    protected function getHeaderActions(): array
    {
        return [
            // Actions\ViewAction::make(),
            // Actions\DeleteAction::make(),
        ];
    }

    protected function getFormActions(): array
    {
        return [];
    }
}
