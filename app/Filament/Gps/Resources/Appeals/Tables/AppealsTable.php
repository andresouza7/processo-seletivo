<?php

namespace App\Filament\Gps\Resources\Appeals\Tables;

use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class AppealsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->heading('Avaliação de Recursos')
            ->description('Revise e avalie os recursos que foram atribuídos a você')
            ->columns([
                TextColumn::make('id')
                    ->label('ID Recurso'),
                TextColumn::make('appeal_stage.process.title')
                    ->label('Processo Seletivo')
                    ->searchable()
                    ->limit(),
                TextColumn::make('appeal_stage.description')
                    ->label('Etapa')
                    ->searchable()
                    ->limit(),
                TextColumn::make('application.position.description')
                    ->label('Vaga')
                    ->searchable()
                    ->limit(),
            ])
            ->filters([
                Filter::make('situacao_null')
                    ->label('Pendentes')
                    ->query(fn(Builder $query): Builder => $query->whereNull('result'))
                    ->default(true),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make()->label('Responder'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    // Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
