<?php

namespace App\Filament\Resources\ProcessoSeletivoTipos\Pages;

use Filament\Actions\DeleteAction;
use App\Filament\Resources\ProcessoSeletivoTipos\ProcessoSeletivoTipoResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditProcessoSeletivoTipo extends EditRecord
{
    protected static string $resource = ProcessoSeletivoTipoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
