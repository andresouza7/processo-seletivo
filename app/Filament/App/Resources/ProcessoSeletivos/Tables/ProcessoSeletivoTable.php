<?php

namespace App\Filament\App\Resources\ProcessoSeletivos\Tables;

use Carbon\Carbon;
use Filament\Actions\BulkActionGroup;
use Filament\Tables\Columns\Layout\Split;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ProcessoSeletivoTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->heading('Consultar Processos Seletivos')
            ->description('Utilize o campo de pesquisa para filtrar uma informação')
            ->defaultSort('data_publicacao_inicio', 'desc')
            ->columns([
                Stack::make([
                    Split::make([
                        TextColumn::make('numero')
                            ->searchable()
                            ->formatStateUsing(fn($record) => sprintf(
                                "<div class='text-xs font-semibold flex gap-3'><span>Publicação: %s</span><span>Nº do Edital: %s</span></div>",
                                Carbon::parse($record->data_criacao)->format('d/m/Y'),
                                $record->numero
                            ))->html()
                            ->color('gray')
                            ->grow(false),
                    ]),
                    TextColumn::make('titulo')
                        ->color('primary')
                        ->searchable()
                        ->limit()
                ])

            ])
            ->filters([
                // 
            ])
            ->recordActions([
                // Tables\Actions\ViewAction::make(),
                // Tables\Actions\EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    // Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
