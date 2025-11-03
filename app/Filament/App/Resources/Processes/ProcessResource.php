<?php

namespace App\Filament\App\Resources\Processes;

use App\Filament\App\Resources\Processes\Pages\ListProcesses;
use App\Filament\App\Resources\Processes\Pages\ViewProcess;
use App\Filament\App\Resources\Processes\RelationManagers\ProcessAttachmentRelationManager;
use App\Filament\App\Resources\Processes\Schemas\ProcessForm;
use App\Filament\App\Resources\Processes\Schemas\ProcessInfolist;
use App\Filament\App\Resources\Processes\Tables\ProcessesTable;
use App\Models\Process;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ProcessResource extends Resource
{
     protected static ?string $model = Process::class;

     protected static ?string $modelLabel = 'Processo Seletivo';

     protected static ?string $pluralModelLabel = 'Processos Seletivos';

     protected static ?string $slug = 'processos';

    protected static string | BackedEnum | null $navigationIcon = Heroicon::RectangleStack;

    protected static bool $shouldRegisterNavigation = false;

    protected static bool $shouldSkipAuthorization = true;

    protected static ?string $recordTitleAttribute = 'title';

    protected static int $globalSearchResultsLimit = 20;

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        return [
            'Edital' => $record->number,
        ];
    }
    public static function getGloballySearchableAttributes(): array
    {
        return ['title', 'number'];
    }

    public static function getGlobalSearchEloquentQuery(): Builder
    {
        return parent::getGlobalSearchEloquentQuery()->orderBy('publication_start_date', 'desc');
    }

    public static function form(Schema $schema): Schema
    {
        return ProcessForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return ProcessInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ProcessesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            ProcessAttachmentRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListProcesses::route('/'),
            'view' => ViewProcess::route('/{record}'),
        ];
    }

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
