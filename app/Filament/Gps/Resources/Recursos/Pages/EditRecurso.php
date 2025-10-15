<?php

namespace App\Filament\Gps\Resources\Recursos\Pages;

use Filament\Actions\ViewAction;
use Filament\Actions\DeleteAction;
use App\Filament\Gps\Resources\Recursos\RecursoResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditRecurso extends EditRecord
{
    protected static string $resource = RecursoResource::class;
    protected static ?string $title = 'Responder Recurso';
    protected static ?string $breadcrumb = 'Responder';

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            // DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $data['evaluated_at'] = now();

        return $data;
    }
}
