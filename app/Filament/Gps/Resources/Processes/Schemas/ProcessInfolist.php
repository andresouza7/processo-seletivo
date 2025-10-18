<?php

namespace App\Filament\Gps\Resources\Processes\Schemas;

use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use HtmlHelper;
use Illuminate\Support\HtmlString;

class ProcessInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make([
                    TextEntry::make('title')
                        ->label('Título')
                        ->extraAttributes(['class' => 'font-semibold text-gray-700']),
                    TextEntry::make('number')
                        ->label('Número')
                        ->extraAttributes(['class' => 'font-semibold text-gray-600']),
                    TextEntry::make('application_start_date')
                        ->label('Período de Inscrições')
                        ->icon('heroicon-o-calendar')
                        ->formatStateUsing(fn($record) => sprintf(
                            "%s a %s",
                            Carbon::parse($record->application_start_date)->format('d/m/Y'),
                            Carbon::parse($record->application_end_date)->format('d/m/Y')
                        ))
                        ->extraAttributes(['class' => 'font-semibold text-gray-700']),
                    TextEntry::make('description')
                        ->hiddenLabel()
                        ->formatStateUsing(fn(string $state): HtmlString => new HtmlString(HtmlHelper::sliceBodyContent($state))),

                ])
            ]);
    }
}
