<?php

namespace App\Filament\Gps\Resources\ProcessoSeletivos\Pages;

use Filament\Schemas\Schema;
use Filament\Forms\Components\Textarea;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Actions\EditAction;
use App\Filament\Gps\Resources\ProcessoSeletivos\ProcessoSeletivoResource;
use Filament\Actions;
use Filament\Forms;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
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

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-chat-bubble-bottom-center-text';

    public static function getNavigationLabel(): string
    {
        return 'Recursos';
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Textarea::make('descricao')
                    ->columnSpanFull()
                    ->disabled(),
                \Filament\Schemas\Components\Actions::make([
                    Action::make('ver_anexo')
                        ->visible(fn($record) => $record->hasMedia('anexo_recurso'))
                        ->url(fn($record) => route('recurso.anexo', $record->idrecurso))
                        ->openUrlInNewTab()
                ])->columnSpanFull(),
                Select::make('situacao')
                    ->required()
                    ->options([
                        'D' => 'Deferido',
                        'I' => 'Indeferido',
                        'P' => 'Parcialmente Deferido',
                    ]),
                Textarea::make('resposta')
                    ->columnSpanFull()
                    ->required()
                    ->maxLength(255),

                SpatieMediaLibraryFileUpload::make('anexo_reposta_recurso')
                    ->columnSpanFull()
                    ->maxFiles(1)
                    ->disk('public')
                    ->collection('anexo_resposta_recurso')
                    ->rules(['file', 'mimes:pdf', 'max:2048'])
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('idrecurso')
            ->heading('Recursos')
            ->defaultSort('idrecurso', 'desc')
            ->columns([
                TextColumn::make('idrecurso'),
                TextColumn::make('etapa_recurso.descricao')
                    ->label('Etapa'),
                TextColumn::make('descricao')
                    ->label('Justificativa'),
                TextColumn::make('situacao')
                    ->badge()
            ])
            ->filters([
                Filter::make('situacao_null')
                    ->label('Pendentes')
                    ->query(fn(Builder $query): Builder => $query->whereNull('situacao'))
                    ->default(true),
            ])
            ->headerActions([
                // Tables\Actions\CreateAction::make(),
            ])
            ->recordActions([
                EditAction::make()->label('Responder'),
                Action::make('resposta_anexo')->label('Resposta Anexo')
                    ->url(fn($record) => tempMediaUrl($record, 'anexo_candidato'))
                    ->openUrlInNewTab()
                    ->visible(fn($record) => $record->hasMedia('anexo_candidato')),
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
