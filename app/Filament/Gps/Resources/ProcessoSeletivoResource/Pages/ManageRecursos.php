<?php

namespace App\Filament\Gps\Resources\ProcessoSeletivoResource\Pages;

use App\Filament\Gps\Resources\ProcessoSeletivoResource;
use Filament\Actions;
use Filament\Forms;
use Filament\Forms\Components\Actions as ComponentsActions;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Form;
use Filament\Resources\Pages\ManageRelatedRecords;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ManageRecursos extends ManageRelatedRecords
{
    protected static string $resource = ProcessoSeletivoResource::class;

    protected static ?string $title = 'Gerenciar Recursos';

    protected static string $relationship = 'recursos';

    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-bottom-center-text';

    public static function getNavigationLabel(): string
    {
        return 'Recursos';
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Textarea::make('descricao')
                    ->columnSpanFull()
                    ->disabled(),
                ComponentsActions::make([
                    Action::make('ver_anexo')
                        ->visible(fn($record) => $record->hasMedia('anexo_recurso'))
                        ->url(fn($record) => route('recurso.anexo', $record->idrecurso))
                        ->openUrlInNewTab()
                ])->columnSpanFull(),
                Forms\Components\Select::make('situacao')
                    ->required()
                    ->options([
                        'D' => 'Deferido',
                        'I' => 'Indeferido',
                        'P' => 'Parcialmente Deferido',
                    ]),
                Forms\Components\Textarea::make('resposta')
                    ->columnSpanFull()
                    ->required()
                    ->maxLength(255),

                SpatieMediaLibraryFileUpload::make('anexo_reposta_recurso')
                    ->columnSpanFull()
                    ->maxFiles(1)
                    ->disk('public')
                    ->collection('anexo_resposta_recurso')
                    ->rules(['file', 'mimes:pdf', 'max:10240'])
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('idrecurso')
            ->heading('Recursos')
            ->defaultSort('idrecurso', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('idrecurso'),
                Tables\Columns\TextColumn::make('etapa_recurso.descricao')
                    ->label('Etapa'),
                Tables\Columns\TextColumn::make('descricao')
                    ->label('Justificativa'),
                Tables\Columns\TextColumn::make('situacao')
                    ->badge()
            ])
            ->filters([
                Tables\Filters\Filter::make('situacao_null')
                    ->label('Pendentes')
                    ->query(fn(Builder $query): Builder => $query->whereNull('situacao'))
                    ->default(true),
            ])
            ->headerActions([
                // Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make()->label('Responder'),
                Tables\Actions\Action::make('resposta_anexo')->label('Resposta Anexo')
                    ->url(fn($record) => $record->getFirstMediaUrl('anexo_resposta_recurso'))
                    ->openUrlInNewTab()
                    ->visible(fn($record) => $record->hasMedia('anexo_resposta_recurso')),
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
