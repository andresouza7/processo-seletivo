<?php

namespace App\Filament\Gps\Resources\ProcessoSeletivoResource\Pages;

use App\Filament\Gps\Resources\ProcessoSeletivoResource;
use App\Models\EtapaRecurso;
use Filament\Actions;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Pages\ManageRelatedRecords;
use Filament\Tables;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ManageEtapaRecurso extends ManageRelatedRecords
{
    protected static string $resource = ProcessoSeletivoResource::class;
    protected static ?string $title = 'Gerenciar Etapas de Recurso';
    protected static string $relationship = 'etapa_recurso';

    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-bottom-center-text';

    public static function getNavigationLabel(): string
    {
        return 'Etapas de Recurso';
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('descricao')
                    ->placeholder('Ex: Resultado preliminar')
                    ->columnSpanFull(),
                Forms\Components\DatePicker::make('data_inicio_recebimento')
                    ->helperText('Período para recebimento dos recursos'),
                Forms\Components\DatePicker::make('data_fim_recebimento'),
                Forms\Components\DatePicker::make('data_inicio_resultado')
                    ->helperText('Período para consulta dos resultados'),
                Forms\Components\DatePicker::make('data_fim_resultado'),
                Forms\Components\Checkbox::make('requer_anexos')
                    ->label('Requer envio de anexos?')
                    ->helperText('Será disponibilizado campo de upload de pdf ao usuário')
                    ->columnSpanFull(),
                Forms\Components\Checkbox::make('permite_multiplos_recursos')
                    ->label('Permitir mais de um recurso por candidato')
                    ->columnSpanFull()
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('idrecurso')
            ->heading('Etapas')
            ->defaultSort('idetapa_recurso', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('descricao'),
                Tables\Columns\TextColumn::make('data_inicio_recebimento'),
                Tables\Columns\TextColumn::make('data_fim_recebimento'),
            ])
            ->filters([])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->createAnother(false)
                    ->before(function (CreateAction $action) {
                        $exists = \App\Models\EtapaRecurso::whereDate('data_fim_recebimento', '>=', now())->exists();

                        if ($exists) {
                            \Filament\Notifications\Notification::make()
                                ->title('Já existe uma etapa em andamento.')
                                ->body('Você não pode criar outra enquanto houver uma ainda não finalizada.')
                                ->danger()
                                ->send();

                            $action->halt();
                        }
                    }),

            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                // Tables\Actions\BulkActionGroup::make([
                //     Tables\Actions\DissociateBulkAction::make(),
                //     Tables\Actions\DeleteBulkAction::make(),
                // ]),
            ]);
    }
}
