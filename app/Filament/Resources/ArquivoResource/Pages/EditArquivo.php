<?php

namespace App\Filament\Resources\ArquivoResource\Pages;

use App\Filament\Resources\ArquivoResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditArquivo extends EditRecord
{
    protected static string $resource = ArquivoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
