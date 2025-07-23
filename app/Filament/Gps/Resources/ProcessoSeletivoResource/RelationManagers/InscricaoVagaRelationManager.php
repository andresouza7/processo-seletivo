<?php

namespace App\Filament\Gps\Resources\ProcessoSeletivoResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class InscricaoVagaRelationManager extends RelationManager
{
    protected static ?string $modelLabel = 'Vaga';
    protected static string $relationship = 'inscricao_vaga';
    protected static ?string $title = 'Cadastro de Vagas';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('codigo')
                    ->label('Código')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('descricao')
                    ->label('Descrição')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('codigo')
            ->paginated(false)
            ->columns([
                Tables\Columns\TextColumn::make('codigo')
                    ->label('Código')
                    ->searchable(),
                Tables\Columns\TextColumn::make('descricao')
                    ->label('Descrição')
                    ->searchable(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->mutateFormDataUsing(function (array $data, $record): array {
                        $parentRecord = $this->getOwnerRecord();

                        // Set the ID of the parent resource on the form data
                        $data['idprocesso_seletivo'] = $parentRecord->id;

                        return $data;
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    // Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
