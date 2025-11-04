<?php

namespace App\Filament\Gps\Resources\Processes\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class ProcessesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->heading('Consultar Processos Seletivos')
            ->description('Pesquise, edite ou crie um novo processo.')
            ->modifyQueryUsing(function (Builder $query) {
                $user = Auth::user();

                if ($user->hasRole(['admin', 'dips'])) {
                    return $query;
                }

                // Get the IDs (or names) of the user's roles
                $userRoleIds = $user->roles->pluck('id');

                // Only include processes that have at least one of these roles
                $query->whereHas('roles', function ($subQuery) use ($userRoleIds) {
                    $subQuery->whereIn('roles.id', $userRoleIds);
                });
            })
            ->defaultSort('id', 'desc')
            ->columns([
                TextColumn::make('id')
                    ->label('ID'),
                TextColumn::make('type.description')
                    ->label('Tipo'),
                TextColumn::make('title')
                    ->label('Título')
                    ->searchable()
                    ->limit(50),
                TextColumn::make('number')
                    ->label('Número')
                    ->searchable(),
                IconColumn::make('is_published')
                    ->label('Publicado')
                    ->boolean()
            ])
            ->filters([
                SelectFilter::make('type')
                    ->label('Tipo')
                    ->relationship('type', 'description'),
                TrashedFilter::make('trash')
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                RestoreAction::make(),
                // ForceDeleteAction::make()
            ])
            ->toolbarActions([
                // BulkActionGroup::make([
                //     DeleteBulkAction::make(),
                // ]),
            ]);
    }
}
