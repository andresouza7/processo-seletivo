<?php

namespace App\Filament\Gps\Resources\Recursos\Tables;

use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class RecursosTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('idrecurso'),
                TextColumn::make('etapa_recurso.descricao')
                    ->label('Etapa'),
                TextColumn::make('description')
                    ->label('Justificativa'),
                TextColumn::make('result')
                    ->badge()
            ])
            ->filters([
                Filter::make('situacao_null')
                    ->label('Pendentes')
                    ->query(fn(Builder $query): Builder => $query->whereNull('result'))
                    ->default(true),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    // Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
