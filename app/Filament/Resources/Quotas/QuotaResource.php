<?php

namespace App\Filament\Resources\Quotas;

use App\Filament\Resources\Quotas\Pages\CreateQuota;
use App\Filament\Resources\Quotas\Pages\EditQuota;
use App\Filament\Resources\Quotas\Pages\ListQuotas;
use App\Filament\Resources\Quotas\Schemas\QuotaForm;
use App\Filament\Resources\Quotas\Tables\QuotasTable;
use App\Models\Quota;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class QuotaResource extends Resource
{
    protected static ?string $model = Quota::class;
    protected static ?string $modelLabel = 'Tipo de Vaga';
    protected static ?string $pluralModelLabel = 'Tipos de Vaga';
    protected static ?string $slug = 'tipo-vaga';
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedTag;
    protected static string | \UnitEnum | null $navigationGroup = 'Gerenciar';
    protected static ?int $navigationSort = 5;

    protected static ?string $recordTitleAttribute = 'description';

    public static function form(Schema $schema): Schema
    {
        return QuotaForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return QuotasTable::configure($table);
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
            'index' => ListQuotas::route('/'),
            'create' => CreateQuota::route('/create'),
            'edit' => EditQuota::route('/{record}/edit'),
        ];
    }
}
