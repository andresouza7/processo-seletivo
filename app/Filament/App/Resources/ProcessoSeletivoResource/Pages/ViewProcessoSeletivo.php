<?php

namespace App\Filament\App\Resources\ProcessoSeletivoResource\Pages;

use App\Filament\App\Resources\ProcessoSeletivoResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewProcessoSeletivo extends ViewRecord
{
    protected static string $resource = ProcessoSeletivoResource::class;

    public function getTitle(): string
    {
        return 'Consultar Processo';
    }

    protected function getHeaderActions(): array
    {
        return [
            // Actions\EditAction::make(),
        ];
    }
}
