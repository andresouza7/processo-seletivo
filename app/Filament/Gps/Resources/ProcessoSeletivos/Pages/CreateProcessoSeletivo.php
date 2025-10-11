<?php

namespace App\Filament\Gps\Resources\ProcessoSeletivos\Pages;

use Throwable;
use App\Filament\Gps\Resources\ProcessoSeletivos\ProcessoSeletivoResource;
use App\Models\Process;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class CreateProcessoSeletivo extends CreateRecord
{
    protected static string $resource = ProcessoSeletivoResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        try {
            $data['views'] = 0;
            $data['directory'] = str_replace('/', '_', $data['number']);
        } catch (Throwable $th) {
            Log::error("create processo seletivo failed: " . $th->getMessage());
            throw $th;
        }
            
        return $data;
    }
}
