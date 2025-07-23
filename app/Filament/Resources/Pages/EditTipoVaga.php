<?php

namespace App\Filament\Resources\TipoVagaResource\Pages;

use App\Filament\Resources\TipoVagaResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTipoVaga extends EditRecord
{
    protected static string $resource = TipoVagaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
