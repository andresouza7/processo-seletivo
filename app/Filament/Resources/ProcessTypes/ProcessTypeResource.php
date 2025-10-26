<?php

namespace App\Filament\Resources\ProcessTypes;

use App\Filament\Resources\ProcessTypes\Pages\CreateProcessType;
use App\Filament\Resources\ProcessTypes\Pages\EditProcessType;
use App\Filament\Resources\ProcessTypes\Pages\ListProcessTypes;
use App\Filament\Resources\ProcessTypes\Schemas\ProcessTypeForm;
use App\Filament\Resources\ProcessTypes\Tables\ProcessTypesTable;
use App\Models\ProcessType;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ProcessTypeResource extends Resource
{
    protected static ?string $model = ProcessType::class;
    protected static ?string $modelLabel = 'Tipo de Processo';
    protected static ?string $pluralModelLabel = 'Tipos de Processo';
    protected static ?string $slug = 'tipo-processo';
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedTag;
    protected static string | \UnitEnum | null $navigationGroup = 'Gerenciar';
    protected static ?int $navigationSort = 4;

    protected static ?string $recordTitleAttribute = 'description';

    public static function form(Schema $schema): Schema
    {
        return ProcessTypeForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ProcessTypesTable::configure($table);
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
            'index' => ListProcessTypes::route('/'),
            'create' => CreateProcessType::route('/create'),
            'edit' => EditProcessType::route('/{record}/edit'),
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
