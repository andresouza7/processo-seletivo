<?php

namespace App\Filament\App\Resources\Processes\Tables;

use Carbon\Carbon;
use Filament\Tables\Columns\Layout\Split;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ProcessesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->heading('Consultar Processos Seletivos')
            ->description('Use o campo de busca para filtrar uma informação')
            ->defaultSort('publication_start_date', 'desc')
            ->columns([
                Stack::make([
                    Split::make([
                        TextColumn::make('number')
                            ->searchable()
                            ->formatStateUsing(fn($record) => sprintf(
                                "<div class='text-xs font-semibold flex gap-3'><span>Publicação: %s</span><span>Nº do Edital: %s</span></div>",
                                Carbon::parse($record->created_at)->format('d/m/Y'),
                                $record->number
                            ))->html()
                            ->color('gray')
                            ->grow(false),
                    ]),
                    TextColumn::make('title')
                        ->color('primary')
                        ->searchable()
                        ->limit()
                ])
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'inscricoes_abertas' => 'Inscrições Abertas',
                        'em_andamento' => 'Em Andamento',
                        'finalizados' => 'Finalizados',
                    ])
                    ->selectablePlaceholder(false)
                    ->query(function (Builder $query, array $data): Builder {
                        return match ($data['value'] ?? null) {
                            'inscricoes_abertas' => $query->inscricoesAbertas(),
                            'em_andamento' => $query->emAndamento(),
                            'finalizados' => $query->finalizados(),
                            default => $query->whereRaw('1 = 0'), // no results
                        };
                    }),
            ]);
    }
}
