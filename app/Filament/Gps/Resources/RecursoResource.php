<?php

namespace App\Filament\Gps\Resources;

use App\Filament\Gps\Resources\RecursoResource\Pages;
use App\Filament\Gps\Resources\RecursoResource\RelationManagers;
use App\Models\Recurso;
use Filament\Forms;
use Filament\Forms\Components\Actions;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Form;
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
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = 'Gerenciar';
    protected static ?int $navigationSort = 3;

    public static function canAccess(): bool
    {
        return Auth::user()->hasRole('avaliador');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Textarea::make('descricao')
                    ->columnSpanFull()
                    ->disabled(),
                Actions::make([
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

    public static function table(Table $table): Table
    {
        return $table
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
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
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
            'index' => Pages\ListRecursos::route('/'),
            'create' => Pages\CreateRecurso::route('/create'),
            'view' => Pages\ViewRecurso::route('/{record}'),
            'edit' => Pages\EditRecurso::route('/{record}/edit'),
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
