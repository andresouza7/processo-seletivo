<?php

namespace App\Filament\Resources\ProcessoSeletivoTipoResource\Pages;

use App\Filament\Resources\ProcessoSeletivoTipoResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditProcessoSeletivoTipo extends EditRecord
{
    protected static string $resource = ProcessoSeletivoTipoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
