<?php

namespace App\Filament\Candidato\Resources\EtapaRecursos;

use Filament\Schemas\Schema;
use Filament\Forms\Components\Placeholder;
use Filament\Tables\Columns\TextColumn;
use Filament\Actions\ViewAction;
use App\Filament\Candidato\Resources\EtapaRecursos\Pages\ListEtapaRecursos;
use App\Filament\Candidato\Resources\EtapaRecursos\Pages\EditEtapaRecurso;
use App\Filament\Candidato\Resources\EtapaRecursoResource\Pages;
use App\Filament\Candidato\Resources\EtapaRecursos\RelationManagers\RecursosRelationManager;
use App\Models\AppealStage;
use Filament\Forms;
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
    protected static ?string $model = AppealStage::class;

    protected static ?string $modelLabel = 'Recursos';

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-chat-bubble-bottom-center-text';

    protected static string | \UnitEnum | null $navigationGroup = 'Área do Candidato';

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
        $validProcessoIds = $user->applications()
            ->whereHas('process', function ($query) {
                $query->whereHas('appeal_stage', function ($subquery) {
                    $today = now()->toDateString();
                    $subquery->whereDate('submission_start_date', '<=', $today)
                        ->whereDate('submission_end_date', '>=', $today);
                });
            })
            ->pluck('id');

        // Filter the query
        return $query->whereIn('id', $validProcessoIds)->orderBy('idetapa_recurso', 'desc')->limit(1);
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Placeholder::make('process')
                    ->helperText(fn($record) => $record->process->title)
                    ->label('Processo Seletivo')
                    ->inlineLabel()
                    ->columnSpanFull(),
                Placeholder::make('description')
                    ->helperText(fn($record) => $record->description)
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
                TextColumn::make('process.titulo'),
                TextColumn::make('description')->label('Etapa'),
            ])
            ->paginated(false)
            ->filters([
                //
            ])
            ->recordActions([
                ViewAction::make(),
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
            'index' => ListEtapaRecursos::route('/'),
            // 'create' => Pages\CreateEtapaRecurso::route('/create'),
            // 'view' => Pages\ViewEtapaRecurso::route('/{record}'),
            'edit' => EditEtapaRecurso::route('/{record}/edit'),
        ];
    }
}
