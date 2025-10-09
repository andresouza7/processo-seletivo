<?php

namespace App\Filament\Gps\Resources\ProcessoSeletivos\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class ProcessoSeletivoTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->heading('Consultar Processos Seletivos')
            ->description('Informações sobre todos os processos seletivos cadastrados. Consulte, atualize ou crie um novo registro.')
            ->defaultSort('id', 'desc')
            ->columns([
                TextColumn::make('id')
                    ->label('ID'),
                TextColumn::make('type.descricao'),
                TextColumn::make('title')
                    ->searchable()
                    ->limit(50),
                TextColumn::make('number')->searchable(),
                TextColumn::make('is_published')
                    ->badge()
                    ->color(fn($state) => $state === 'S' ? 'success' : 'danger'),
            ])
            ->filters([
                SelectFilter::make('type')
                    ->label('Tipo')
                    ->relationship('type', 'description'),
            ])
            ->recordActions([
                EditAction::make()
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
