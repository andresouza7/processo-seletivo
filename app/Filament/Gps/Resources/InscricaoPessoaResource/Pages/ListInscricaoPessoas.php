<?php

namespace App\Filament\Gps\Resources\InscricaoPessoaResource\Pages;

use App\Filament\Gps\Resources\InscricaoPessoaResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListInscricaoPessoas extends ListRecords
{
    protected static string $resource = InscricaoPessoaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Actions\CreateAction::make(),
        ];
    }
}
