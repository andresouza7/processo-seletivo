<?php

namespace App\Filament\Gps\Resources\InscricaoPessoas\Tables;

use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class InscricaoPessoasTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->heading('Contas de candidatos')
            ->description('Gerência dos dados cadastrais dos candidatos e redefinição de senha.')
            ->defaultSort('name')
            ->columns([
                TextColumn::make('name')
                    ->searchable(),
                TextColumn::make('sex')
                    ->badge()
                    ->searchable(),
                TextColumn::make('rg')
                    ->label('RG')
                    ->searchable(),
                TextColumn::make('cpf')
                    ->label('CPF')
                    ->searchable(),
                TextColumn::make('email')
                    ->searchable(),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                ViewAction::make(),
            ]);
    }
}
