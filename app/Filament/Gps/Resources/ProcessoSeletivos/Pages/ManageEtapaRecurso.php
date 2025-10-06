<?php

namespace App\Filament\Gps\Resources\ProcessoSeletivos\Pages;

use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Checkbox;
use Filament\Tables\Columns\TextColumn;
use Filament\Actions\CreateAction;
use Filament\Notifications\Notification;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use App\Filament\Gps\Resources\ProcessoSeletivos\ProcessoSeletivoResource;
use App\Models\EtapaRecurso;
use Filament\Actions;
use Filament\Forms;
use Filament\Resources\Pages\ManageRelatedRecords;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ManageEtapaRecurso extends ManageRelatedRecords
{
    protected static string $resource = ProcessoSeletivoResource::class;
    protected static ?string $title = 'Gerenciar Etapas de Recurso';
    protected static string $relationship = 'etapa_recurso';

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-chat-bubble-bottom-center-text';

    public static function getNavigationLabel(): string
    {
        return 'Etapas de Recurso';
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('descricao')
                    ->placeholder('Ex: Resultado preliminar')
                    ->columnSpanFull(),
                DatePicker::make('data_inicio_recebimento')
                    ->helperText('Período para recebimento dos recursos'),
                DatePicker::make('data_fim_recebimento'),
                DatePicker::make('data_inicio_resultado')
                    ->helperText('Período para consulta dos resultados'),
                DatePicker::make('data_fim_resultado'),
                Checkbox::make('requer_anexos')
                    ->label('Requer envio de anexos?')
                    ->helperText('Será disponibilizado campo de upload de pdf ao usuário')
                    ->columnSpanFull(),
                Checkbox::make('permite_multiplos_recursos')
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
                TextColumn::make('descricao'),
                TextColumn::make('data_inicio_recebimento'),
                TextColumn::make('data_fim_recebimento'),
            ])
            ->filters([])
            ->headerActions([
                CreateAction::make()
                    ->createAnother(false)
                    ->before(function (CreateAction $action) {
                        $processo = $this->getRecord();
                        
                        $exists = $processo->etapa_recurso()
                            ->whereDate('data_fim_recebimento', '>=', now())->exists();

                        if ($exists) {
                            Notification::make()
                                ->title('Já existe uma etapa em andamento.')
                                ->body('Você não pode criar outra enquanto houver uma ainda não finalizada.')
                                ->danger()
                                ->send();

                            $action->halt();
                        }
                    }),

            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                // Tables\Actions\BulkActionGroup::make([
                //     Tables\Actions\DissociateBulkAction::make(),
                //     Tables\Actions\DeleteBulkAction::make(),
                // ]),
            ]);
    }
}
