<?php

namespace App\Filament\Gps\Resources\ProcessoSeletivos\Pages;

use App\Filament\Gps\Resources\ProcessoSeletivos\ProcessoSeletivoResource;
use Filament\Resources\Pages\Concerns\InteractsWithRecord;
use Filament\Resources\Pages\Page;
use Filament\Support\Icons\Heroicon;

class ManageRecursos extends Page
{
    use InteractsWithRecord;

    protected static string $resource = ProcessoSeletivoResource::class;

     protected static ?string $title = 'Recursos';

    protected static string | \BackedEnum | null $navigationIcon = Heroicon::OutlinedChatBubbleBottomCenterText;

    protected string $view = 'filament.gps.resources.processo-seletivos.pages.manage-recursos';

    public function mount(int|string $record): void
    {
        $this->record = $this->resolveRecord($record);
    }
}
