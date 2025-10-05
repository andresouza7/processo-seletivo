<?php

namespace App\Filament\Gps\Resources\ProcessoSeletivoResource\Pages;

use Filament\Actions\DeleteAction;
use App\Filament\Gps\Resources\ProcessoSeletivoResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditProcessoSeletivo extends EditRecord
{
    protected static string $resource = ProcessoSeletivoResource::class;
    protected static ?string $navigationLabel = 'Editar';

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
