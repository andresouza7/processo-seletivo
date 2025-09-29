<?php

namespace App\Filament\Gps\Resources\RecursoResource\Pages;

use App\Filament\Gps\Resources\RecursoResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditRecurso extends EditRecord
{
    protected static string $resource = RecursoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
