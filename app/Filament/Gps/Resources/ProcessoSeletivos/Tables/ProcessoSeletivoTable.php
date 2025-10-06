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
            ->defaultSort('idprocesso_seletivo', 'desc')
            ->columns([
                TextColumn::make('idprocesso_seletivo')
                    ->label('ID'),
                TextColumn::make('tipo.descricao'),
                TextColumn::make('titulo')
                    ->searchable()
                    ->limit(50),
                TextColumn::make('numero')->searchable(),
                TextColumn::make('publicado')
                    ->badge()
                    ->color(fn($state) => $state === 'S' ? 'success' : 'danger'),
            ])
            ->filters([
                SelectFilter::make('tipo')
                    ->label('Tipo')
                    ->relationship('tipo', 'descricao'),
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
