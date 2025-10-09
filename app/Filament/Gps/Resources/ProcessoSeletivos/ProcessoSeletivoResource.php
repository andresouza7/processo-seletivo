<?php

namespace App\Filament\Gps\Resources\ProcessoSeletivos;

use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Actions\EditAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use App\Filament\Gps\Resources\ProcessoSeletivos\Pages\ListProcessoSeletivos;
use App\Filament\Gps\Resources\ProcessoSeletivos\Pages\CreateProcessoSeletivo;
use App\Filament\Gps\Resources\ProcessoSeletivoResource\Pages;
use App\Filament\Gps\Resources\ProcessoSeletivos\Pages\EditProcessoSeletivo;
use App\Filament\Gps\Resources\ProcessoSeletivos\Pages\ManageAnexos;
use App\Filament\Gps\Resources\ProcessoSeletivos\Pages\ManageAvaliadores;
use App\Filament\Gps\Resources\ProcessoSeletivos\Pages\ManageEtapaRecurso;
use App\Filament\Gps\Resources\ProcessoSeletivos\Pages\ManageInscritos;
use App\Filament\Gps\Resources\ProcessoSeletivos\Pages\ManageVagas;
use App\Filament\Gps\Resources\ProcessoSeletivos\Schemas\ProcessoSeletivoForm;
use App\Filament\Gps\Resources\ProcessoSeletivos\Tables\ProcessoSeletivoTable;
use App\Models\ProcessoSeletivo;
use Filament\Resources\Pages\Page;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;

class ProcessoSeletivoResource extends Resource
{
    protected static ?string $model = ProcessoSeletivo::class;
    protected static ?string $modelLabel = 'Processo Seletivo';
    protected static ?string $pluralModelLabel = 'Processos Seletivos';
    protected static ?string $slug = 'processos';
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-folder';
    protected static string | \UnitEnum | null $navigationGroup = 'Gerenciar';
    protected static ?int $navigationSort = 1;
    protected static ?string $recordTitleAttribute = 'title';
    protected static int $globalSearchResultsLimit = 20;

    public static function canAccess(): bool
    {
        return Auth::user()->hasAnyRole('gestor|admin');
    }

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        return [
            'Edital' => $record->number,
        ];
    }

    public static function getGlobalSearchEloquentQuery(): Builder
    {
        return parent::getGlobalSearchEloquentQuery()->orderBy('publication_start_date', 'desc');
    }

    public static function form(Schema $schema): Schema
    {
        return ProcessoSeletivoForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ProcessoSeletivoTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getRecordSubNavigation(Page $page): array
    {
        return $page->generateNavigationItems([
            EditProcessoSeletivo::class,
            ManageAnexos::class,
            ManageInscritos::class,
            ManageVagas::class,
            ManageEtapaRecurso::class,
            ManageAvaliadores::class,
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListProcessoSeletivos::route('/'),
            'create' => CreateProcessoSeletivo::route('/create'),
            'edit' => EditProcessoSeletivo::route('/{record}/edit'),
            'anexos' => ManageAnexos::route('/{record}/anexos'),
            'inscritos' => ManageInscritos::route('/{record}/inscritos'),
            'avaliadores' => ManageAvaliadores::route('/{record}/avaliadores'),
            'vagas' => ManageVagas::route('/{record}/vagas'),
            'etapas_recurso' => ManageEtapaRecurso::route('/{record}/etapas_recurso'),
        ];
    }
}
