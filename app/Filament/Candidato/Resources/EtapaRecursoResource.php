<?php

namespace App\Filament\Candidato\Resources;

use App\Filament\Candidato\Resources\EtapaRecursoResource\Pages;
use App\Filament\Candidato\Resources\EtapaRecursoResource\RelationManagers\RecursosRelationManager;
use App\Models\EtapaRecurso;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;

class EtapaRecursoResource extends Resource
{
    protected static ?string $model = EtapaRecurso::class;

    protected static ?string $modelLabel = 'Recursos';

    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-bottom-center-text';

    protected static ?string $navigationGroup = 'Área do Candidato';

    protected static ?int $navigationSort = 2;

    // protected static bool $shouldRegisterNavigation = false;

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);

        $user = Auth::guard('candidato')->user();

        // filtra os PSs com periodo de recurso aberto e para os quais o usuario se candidatou
        $validProcessoIds = $user->inscricoes()
            ->whereHas('processo_seletivo', function ($query) {
                $query->whereHas('etapa_recurso', function ($subquery) {
                    $today = now()->toDateString();
                    $subquery->whereDate('data_inicio_recebimento', '<=', $today)
                        ->whereDate('data_fim_recebimento', '>=', $today);
                });
            })
            ->pluck('idprocesso_seletivo');

        // Filter the query
        return $query->whereIn('idprocesso_seletivo', $validProcessoIds)->orderBy('idetapa_recurso', 'desc')->limit(1);
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Placeholder::make('processo_seletivo')
                    ->helperText(fn($record) => $record->processo_seletivo->titulo)
                    ->label('Processo Seletivo')
                    ->inlineLabel()
                    ->columnSpanFull(),
                Forms\Components\Placeholder::make('descricao')
                    ->helperText(fn($record) => $record->descricao)
                    ->label('Etapa')
                    ->inlineLabel()
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->description('Consulte nesta seção os Processos Seletivos com período de recurso em andamento.')
            ->columns([
                Tables\Columns\TextColumn::make('processo_seletivo.titulo'),
                Tables\Columns\TextColumn::make('descricao')->label('Etapa'),
            ])
            ->paginated(false)
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                // Tables\Actions\EditAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RecursosRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListEtapaRecursos::route('/'),
            // 'create' => Pages\CreateEtapaRecurso::route('/create'),
            // 'view' => Pages\ViewEtapaRecurso::route('/{record}'),
            'edit' => Pages\EditEtapaRecurso::route('/{record}/edit'),
        ];
    }
}
