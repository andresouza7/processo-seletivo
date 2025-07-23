<?php

namespace App\Filament\Gps\Resources\ProcessoSeletivoResource\Pages;

use App\Filament\Gps\Resources\ProcessoSeletivoResource;
use App\Models\ProcessoSeletivo;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateProcessoSeletivo extends CreateRecord
{
    protected static string $resource = ProcessoSeletivoResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // $idprocesso_seletivo = ProcessoSeletivo::latest('idprocesso_seletivo')->value('idprocesso_seletivo') ?? 0;
        // $data['idprocesso_seletivo'] = $idprocesso_seletivo + 1;
        
        $data['situacao'] = $data['situacao'] ?? '';
        $data['acessos'] = 0;

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
