<?php

namespace App\Filament\App\Resources\ProcessoSeletivos\Schemas;

use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use HtmlHelper;
use Illuminate\Support\HtmlString;

class ProcessoSeletivoInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make([
                    TextEntry::make('title')
                        ->label('Nome')
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
                    Actions::make([
                        Action::make('createInscricao')
                            ->visible(fn($record) => $record->aceita_inscricao)
                            ->label('Realizar Inscrição')
                            ->url(route('filament.candidato.resources.inscricoes.create'))
                            ->button()
                            ->color('primary'),
                        Action::make('createRecurso')
                            ->visible(fn($record) => $record->aceita_recurso)
                            ->label('Recursos')
                            ->url(fn($record) => $record->link_recurso)
                            ->button()
                            ->color('primary')
                    ]),
                    TextEntry::make('description')
                        ->hiddenLabel()
                        ->formatStateUsing(fn(string $state): HtmlString => new HtmlString(HtmlHelper::sliceBodyContent($state))),

                ])
            ]);
    }
}
