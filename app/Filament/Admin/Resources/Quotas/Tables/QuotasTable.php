<?php

namespace App\Filament\Admin\Resources\Quotas\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class QuotasTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->heading('Gerenciar tipo de vaga')
            ->description('Condição na qual um candidato poderá concorrer a uma vaga.')
            ->columns([
                TextColumn::make('id')->label('ID'),
                TextColumn::make('description')
                    ->label('Descrição')
                    ->searchable(),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
