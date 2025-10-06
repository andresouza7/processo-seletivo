<?php

namespace App\Filament\App\Resources\ProcessoSeletivos\RelationManagers;

use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class AnexosRelationManager extends RelationManager
{
    protected static string $relationship = 'anexos';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('descricao')
            ->heading('Publicações')
            ->description('Utilize o campo de pesquisa para filtrar uma informação')
            ->defaultSort('idprocesso_seletivo_anexo', 'desc')
            ->columns([
                Stack::make([
                    TextColumn::make('data_publicacao')
                        ->formatStateUsing(fn($record) => sprintf(
                            "<div class='text-xs font-semibold flex gap-3'><span>%s</span></div>",
                            Carbon::parse($record->data_publicacao)->format('d/m/Y'),
                        ))->html()
                        ->color('gray'),
                    TextColumn::make('descricao')
                        ->label('Descrição')
                        ->color('primary')
                        ->url(fn($record) => $record->url_arquivo, shouldOpenInNewTab: true)
                        ->searchable(),
                ])
            ])
            ->filters([
                //
            ])
            ->headerActions([
                // 
            ])
            ->recordActions([
                // 
            ]);
    }
}
