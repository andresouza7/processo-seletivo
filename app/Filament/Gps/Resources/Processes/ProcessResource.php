<?php

namespace App\Filament\Gps\Resources\Processes;

use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Actions\EditAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use App\Filament\Gps\Resources\Processes\Pages\ListProcesses;
use App\Filament\Gps\Resources\Processes\Pages\CreateProcess;
use App\Filament\Gps\Resources\ProcessResource\Pages;
use App\Filament\Gps\Resources\Processes\Pages\EditProcess;
use App\Filament\Gps\Resources\Processes\Pages\ManageAttachments;
use App\Filament\Gps\Resources\Processes\Pages\ManageEvaluators;
use App\Filament\Gps\Resources\Processes\Pages\ManageAppealStage;
use App\Filament\Gps\Resources\Processes\Pages\ManageApplications;
use App\Filament\Gps\Resources\Processes\Pages\ManageRecursos;
use App\Filament\Gps\Resources\Processes\Pages\ManagePositions;
use App\Filament\Gps\Resources\Processes\Pages\ViewProcess;
use App\Filament\Gps\Resources\Processes\Schemas\ProcessForm;
use App\Filament\Gps\Resources\Processes\Schemas\ProcessInfolist;
use App\Filament\Gps\Resources\Processes\Tables\ProcessesTable;
use App\Http\Middleware\CheckProcessRole;
use App\Models\Process;
use Filament\Navigation\NavigationGroup;
use Filament\Resources\Pages\Page;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Role;

class ProcessResource extends Resource
{
    protected static ?string $model = Process::class;
    protected static ?string $modelLabel = 'Processo Seletivo';
    protected static ?string $pluralModelLabel = 'Processos Seletivos';
    protected static ?string $slug = 'processos';
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-folder';
    protected static string | \UnitEnum | null $navigationGroup = 'Gerenciar';
    protected static ?int $navigationSort = 1;
    protected static ?string $recordTitleAttribute = 'title';
    protected static int $globalSearchResultsLimit = 20;

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
            //
        ];
    }

    public static function getRecordSubNavigation(Page $page): array
    {
        return [
            NavigationGroup::make()
                ->label('Processo')
                ->items([
                    ...$page->generateNavigationItems([
                        ViewProcess::class,
                        EditProcess::class,
                        ManageAttachments::class,
                        ManageApplications::class,
                        ManagePositions::class,
                    ]),
                ]),

            // Grouped section for Recursos
            NavigationGroup::make()
                ->label('Recursos')
                ->items([
                    ...$page->generateNavigationItems([
                        ManageAppealStage::class,
                        ManageEvaluators::class,
                    ]),
                ]),
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListProcesses::route('/'),
            'create' => CreateProcess::route('/create'),
            'edit' => EditProcess::route('/{record}/edit'),
            'view' => ViewProcess::route('/{record}'),
            'anexos' => ManageAttachments::route('/{record}/anexos'),
            'inscritos' => ManageApplications::route('/{record}/inscritos'),
            'evaluators' => ManageEvaluators::route('/{record}/evaluators'),
            'vagas' => ManagePositions::route('/{record}/vagas'),
            'etapas_recurso' => ManageAppealStage::route('/{record}/etapas_recurso'),
        ];
    }
}
