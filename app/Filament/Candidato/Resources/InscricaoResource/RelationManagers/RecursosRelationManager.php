<?php

namespace App\Filament\Candidato\Resources\InscricaoResource\RelationManagers;

use Error;
use Filament\Forms;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Infolists\Components\Split;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class RecursosRelationManager extends RelationManager
{
    protected static string $relationship = 'recursos';

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist->schema([
            TextEntry::make('descricao')
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('descricao')
            ->emptyStateDescription('')
            ->columns([
                Tables\Columns\TextColumn::make('descricao')->limit(),
                Tables\Columns\TextColumn::make('data_hora')->date('m/d/Y H:m'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                // Tables\Actions\CreateAction::make(),
                Action::make('solicitarRecurso')
                    ->visible(fn() => $this->getOwnerRecord()->processo_seletivo->aceita_recurso)
                    ->button()
                    ->form([
                        Textarea::make('descricao')
                            ->label('Justificativa')
                            ->rows(15)
                    ])
                    ->action(function (array $data, string $model): ?Model {
                        try {
                            $inscricao = $this->getOwnerRecord();

                            if (!$inscricao->processo_seletivo->aceita_recurso) {
                                throw new Error("Fora do período de recursos");
                            }

                            // Attempt to find or create the record
                            $record = $model::firstOrCreate(
                                [
                                    'idprocesso_seletivo' => $inscricao->idprocesso_seletivo,
                                    'idinscricao_pessoa' => $inscricao->idinscricao_pessoa,
                                    'idinscricao' => $inscricao->idinscricao,
                                ],
                                [
                                    'descricao' => $data['descricao'],
                                    'resposta' => '',
                                    'data_hora' => now()
                                ]
                            );

                            if ($record->wasRecentlyCreated) {
                                // Send success notification for new record
                                Notification::make()
                                    ->title('Recurso cadastrado!')
                                    ->success()
                                    ->send();
                            } else {
                                // Send notification for existing record
                                Notification::make()
                                    ->title('Recurso já registrado!')
                                    ->warning()
                                    ->body('Este recurso já foi solicitado anteriormente.')
                                    ->send();
                            }

                            // Ensure the method returns the record, even if it already existed
                            return $record;
                        } catch (\Exception $e) {
                            // Send failure notification
                            Notification::make()
                                ->title('Erro ao cadastrar recurso!')
                                ->danger()
                                ->send();

                            // Optionally, you can rethrow or handle the exception
                            throw $e;
                        }
                    })
            ])
            ->actions([
                // Tables\Actions\EditAction::make(),
                // Tables\Actions\DeleteAction::make(),
                Action::make('viewRecord')
                    // ->visible(fn($record) => filled($record->resposta))
                    ->disabledForm()
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Fechar')
                    ->label('Visualizar')
                    ->icon('heroicon-o-eye')
                    ->modalHeading('Recurso')
                    // ->modalWidth('xl')
                    ->infolist(function (Model $record) {
                        return [
                            Split::make([

                                // Display the inscricao code as the top entry
                                TextEntry::make('inscricao.cod_inscricao')
                                    ->label('Código da Inscrição')
                                    ->columnSpanFull(), // Spans the entire width for better visibility

                                // Data e Hora entry
                                TextEntry::make('data_hora')
                                    ->label('Data e Hora')
                                    ->date('m/d/Y H:m')
                                    ->columnSpan(2),
                            ]),

                            // Description entry
                            TextEntry::make('descricao')
                                ->label('Descrição')
                                ->columnSpan(2), // Adjusts layout for better organization

                            // Situação entry
                            TextEntry::make('situacao')
                                ->visible(fn($record) => filled($record->situacao))
                                ->label('Situação')
                                ->badge()
                                ->colors([
                                    'success' => fn($state) => $state === 'D', // Green for "Deferido"
                                    'danger' => fn($state) => $state === 'I', // Red for "Indeferido"
                                ])
                                ->formatStateUsing(fn($state) => $state === 'D' ? 'Deferido' : ($state === 'I' ? 'Indeferido' : 'Não Definido'))
                                ->columnSpan(2),

                            // Resposta badge entry with condition
                            TextEntry::make('resposta')
                                ->visible(fn($record) => filled($record->resposta))
                                ->label('Resposta')
                                ->html()
                                ->columnSpanFull(), // Spans the width for emphasis
                        ];
                    })
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    // Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
