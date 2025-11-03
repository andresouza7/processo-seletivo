<?php

namespace App\Filament\Admin\Resources\Arquivos\Pages;

use Filament\Actions\CreateAction;
use App\Filament\Admin\Resources\Arquivos\ArquivoResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListArquivos extends ListRecords
{
    protected static string $resource = ArquivoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
