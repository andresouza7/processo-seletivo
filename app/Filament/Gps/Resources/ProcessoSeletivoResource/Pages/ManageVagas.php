<?php

namespace App\Filament\Gps\Resources\ProcessoSeletivoResource\Pages;

use App\Filament\Gps\Resources\ProcessoSeletivoResource;
use Filament\Actions;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Pages\ManageRelatedRecords;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ManageVagas extends ManageRelatedRecords
{
    protected static string $resource = ProcessoSeletivoResource::class;
    protected static ?string $title = 'Gerenciar Vagas';
    protected static string $relationship = 'inscricao_vaga';

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function getNavigationLabel(): string
    {
        return 'Vagas';
    }

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
            ->recordTitleAttribute('descricao')
            ->heading('Vagas')
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
                    ->label('Cadastrar vaga')
                    ->mutateFormDataUsing(function (array $data): array {
                        $parentRecord = $this->getOwnerRecord();

                        // Set the ID of the parent resource on the form data
                        $data['idprocesso_seletivo'] = $parentRecord->id;

                        return $data;
                    }),
            ])
            ->actions([
                // Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                // Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                // Tables\Actions\BulkActionGroup::make([
                //     Tables\Actions\DissociateBulkAction::make(),
                //     Tables\Actions\DeleteBulkAction::make(),
                // ]),
            ]);
    }
}
