<?php

namespace App\Filament\App\Resources\ProcessoSeletivos;

use Filament\Schemas\Schema;
use App\Filament\App\Resources\ProcessoSeletivos\Pages\ListProcessoSeletivos;
use App\Filament\App\Resources\ProcessoSeletivos\Pages\ViewProcessoSeletivo;
use App\Filament\App\Resources\ProcessoSeletivoResource\Pages;
use App\Filament\App\Resources\ProcessoSeletivos\RelationManagers\AnexosRelationManager;
use App\Filament\App\Resources\ProcessoSeletivos\Schemas\ProcessoSeletivoForm;
use App\Filament\App\Resources\ProcessoSeletivos\Schemas\ProcessoSeletivoInfolist;
use App\Filament\App\Resources\ProcessoSeletivos\Tables\ProcessoSeletivoTable;
use App\Models\ProcessoSeletivo;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ProcessoSeletivoResource extends Resource
{
    protected static ?string $model = ProcessoSeletivo::class;

    protected static string | BackedEnum | null $navigationIcon = Heroicon::RectangleStack;

    protected static bool $shouldRegisterNavigation = false;

    protected static ?string $recordTitleAttribute = 'titulo';

    protected static int $globalSearchResultsLimit = 20;

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        return [
            'Edital' => $record->numero,
        ];
    }
    public static function getGloballySearchableAttributes(): array
    {
        return ['titulo', 'numero'];
    }

    public static function getGlobalSearchEloquentQuery(): Builder
    {
        return parent::getGlobalSearchEloquentQuery()->orderBy('data_publicacao_inicio', 'desc');
    }

    public static function form(Schema $schema): Schema
    {
        return ProcessoSeletivoForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return ProcessoSeletivoInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ProcessoSeletivoTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            AnexosRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListProcessoSeletivos::route('/'),
            'view' => ViewProcessoSeletivo::route('/{record}'),
        ];
    }
}
