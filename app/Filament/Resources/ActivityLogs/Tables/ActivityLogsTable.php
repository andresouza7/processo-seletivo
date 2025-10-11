<?php

namespace App\Filament\Resources\ActivityLogs\Tables;

use App\Models\ActivityLog;
use App\Models\User;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class ActivityLogsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('id', 'desc')
            ->columns([
                TextColumn::make('id')
                    ->sortable()
                    ->label('#')
                    ->toggleable()
                    ->grow(false),

                TextColumn::make('event')
                    ->label('Tipo de Ação')
                    ->badge()
                    ->colors([
                        'success' => 'created',
                        'warning' => 'updated',
                        'danger'  => 'deleted',
                    ])
                    ->sortable(),

                TextColumn::make('subject_type')
                    ->label('Entidade Afetada')
                    ->formatStateUsing(fn($state, $record) => class_basename($state) . " (#{$record->subject_id})")
                    ->color('primary')
                    ->sortable(),

                TextColumn::make('causer_type')
                    ->label('Responsável')
                    ->formatStateUsing(fn($state, $record) => $record->causer
                        ? class_basename($state) . " (#{$record->causer->name})"
                        : 'Sistema')
                    ->color('secondary'),

                TextColumn::make('created_at')
                    ->label('Data')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('subject_type')
                    ->label('Entidade Afetada')
                    ->options(fn() => ActivityLog::distinct('subject_type')->pluck('subject_type','subject_type'))
                    ->searchable(),

                SelectFilter::make('causer_id')
                    ->label('Responsável')
                    ->options(fn() => User::all()->pluck('name', 'id'))
                    ->searchable(),

                SelectFilter::make('event')
                    ->label('Tipo de Ação')
                    ->options([
                        'created' => 'Criado',
                        'updated' => 'Atualizado',
                        'deleted' => 'Deletado',
                    ]),
            ])
            ->recordActions([
                ViewAction::make()->label('Visualizar'),
                // EditAction::make()->label('Editar'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    // DeleteBulkAction::make()->label('Excluir'),
                ]),
            ]);
    }
}
