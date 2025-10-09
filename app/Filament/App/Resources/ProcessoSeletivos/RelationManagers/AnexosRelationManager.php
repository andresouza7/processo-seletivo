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
    protected static string $relationship = 'attachments';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('description')
            ->heading('Publicações')
            ->description('Utilize o campo de pesquisa para filtrar uma informação')
            ->defaultSort('id', 'desc')
            ->columns([
                Stack::make([
                    TextColumn::make('publication_date')
                        ->formatStateUsing(fn($record) => sprintf(
                            "<div class='text-xs font-semibold flex gap-3'><span>%s</span></div>",
                            Carbon::parse($record->publication_date)->format('d/m/Y'),
                        ))->html()
                        ->color('gray'),
                    TextColumn::make('description')
                        ->label('Descrição')
                        ->color('primary')
                        ->url(fn($record) => $record->file_url, shouldOpenInNewTab: true)
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
