<?php

namespace App\Filament\Gps\Resources\ProcessoSeletivos\Pages;

use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\TextColumn;
use Filament\Actions\CreateAction;
use Filament\Actions\EditAction;
use App\Filament\Gps\Resources\ProcessoSeletivos\ProcessoSeletivoResource;
use Filament\Actions;
use Filament\Forms;
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

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function getNavigationLabel(): string
    {
        return 'Vagas';
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('code')
                    ->label('Código')
                    ->required()
                    ->maxLength(255),
                TextInput::make('description')
                    ->label('Descrição')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('description')
            ->heading('Vagas')
            ->paginated(false)
            ->columns([
                TextColumn::make('code')
                    ->label('Código')
                    ->searchable(),
                TextColumn::make('description')
                    ->label('Descrição')
                    ->searchable(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                CreateAction::make()
                    ->label('Cadastrar vaga')
                    ->mutateDataUsing(function (array $data): array {
                        $parentRecord = $this->getOwnerRecord();

                        // Set the ID of the parent resource on the form data
                        $data['idprocesso_seletivo'] = $parentRecord->id;

                        return $data;
                    }),
            ])
            ->recordActions([
                // Tables\Actions\ViewAction::make(),
                EditAction::make(),
                // Tables\Actions\DeleteAction::make(),
            ])
            ->toolbarActions([
                // Tables\Actions\BulkActionGroup::make([
                //     Tables\Actions\DissociateBulkAction::make(),
                //     Tables\Actions\DeleteBulkAction::make(),
                // ]),
            ]);
    }
}
