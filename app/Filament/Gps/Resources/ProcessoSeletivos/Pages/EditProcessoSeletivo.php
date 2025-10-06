<?php

namespace App\Filament\Gps\Resources\ProcessoSeletivos\Pages;

use Filament\Actions\DeleteAction;
use App\Filament\Gps\Resources\ProcessoSeletivos\ProcessoSeletivoResource;
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
