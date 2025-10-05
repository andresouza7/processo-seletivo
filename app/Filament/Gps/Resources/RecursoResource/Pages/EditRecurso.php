<?php

namespace App\Filament\Gps\Resources\RecursoResource\Pages;

use Filament\Actions\ViewAction;
use Filament\Actions\DeleteAction;
use App\Filament\Gps\Resources\RecursoResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditRecurso extends EditRecord
{
    protected static string $resource = RecursoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
