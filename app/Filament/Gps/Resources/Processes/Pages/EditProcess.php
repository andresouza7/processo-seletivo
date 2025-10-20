<?php

namespace App\Filament\Gps\Resources\Processes\Pages;

use Filament\Actions\DeleteAction;
use App\Filament\Gps\Resources\Processes\ProcessResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditProcess extends EditRecord
{
    protected static string $resource = ProcessResource::class;
    protected static ?string $navigationLabel = 'Editar';

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
