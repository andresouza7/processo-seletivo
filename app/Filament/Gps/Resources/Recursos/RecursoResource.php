<?php

namespace App\Filament\Gps\Resources\Recursos;

use Filament\Schemas\Schema;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Actions;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Actions\ViewAction;
use Filament\Actions\EditAction;
use Filament\Actions\BulkActionGroup;
use App\Filament\Gps\Resources\Recursos\Pages\ListRecursos;
use App\Filament\Gps\Resources\Recursos\Pages\CreateRecurso;
use App\Filament\Gps\Resources\Recursos\Pages\EditRecurso;
use App\Filament\Gps\Resources\RecursoResource\Pages;
use App\Filament\Gps\Resources\RecursoResource\RelationManagers;
use App\Models\Recurso;
use Filament\Forms;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;

class RecursoResource extends Resource
{
    protected static ?string $model = Recurso::class;

    // protected static ?string $slug = 'processos';
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static string | \UnitEnum | null $navigationGroup = 'Gerenciar';
    protected static ?int $navigationSort = 3;

    public static function canAccess(): bool
    {
        return Auth::user()->hasAnyRole('admin|avaliador');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Textarea::make('descricao')
                    ->columnSpanFull()
                    ->disabled(),
                Actions::make([
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

                SpatieMediaLibraryFileUpload::make('anexo_avaliador')
                    ->columnSpanFull()
                    ->maxFiles(1)
                    ->disk('local')
                    ->collection('anexo_avaliador')
                    ->rules(['file', 'mimes:pdf', 'max:2048'])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
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
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                Action::make('anexo_candidato')->label('Anexo')
                    ->url(fn($record) => tempMediaUrl($record, 'anexo_candidato'))
                    ->openUrlInNewTab()
                    ->visible(fn($record) => $record->hasMedia('anexo_candidato')),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    // Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListRecursos::route('/'),
            'create' => CreateRecurso::route('/create'),
            // 'view' => Pages\ViewRecurso::route('/{record}'),
            'edit' => EditRecurso::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);

        $userId = auth()->id();

        $query->whereIn('idprocesso_seletivo', function ($query) use ($userId) {
            $query->select('idprocesso_seletivo')
                ->from('avaliador_processo_seletivo')
                ->where('user_id', $userId);
        });

        return $query;
    }
}
