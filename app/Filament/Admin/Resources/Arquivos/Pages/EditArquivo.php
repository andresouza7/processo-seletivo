<?php

namespace App\Filament\Admin\Resources\Arquivos\Pages;

use Filament\Actions\DeleteAction;
use App\Filament\Admin\Resources\Arquivos\ArquivoResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditArquivo extends EditRecord
{
    protected static string $resource = ArquivoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
