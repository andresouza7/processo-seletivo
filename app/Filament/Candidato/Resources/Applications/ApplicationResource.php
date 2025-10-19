<?php

namespace App\Filament\Candidato\Resources\Applications;

use Filament\Schemas\Schema;
use App\Filament\Candidato\Resources\Applications\Pages\ListApplications;
use App\Filament\Candidato\Resources\Applications\Pages\CreateApplication;
use App\Filament\Candidato\Resources\Applications\Pages\ViewApplication;
use App\Filament\Candidato\Resources\ApplicationResource\Pages;
use App\Filament\Candidato\Resources\Applications\Pages\PrintApplication;
use App\Filament\Candidato\Resources\Applications\Schemas\ApplicationForm;
use App\Filament\Candidato\Resources\Applications\Schemas\ApplicationInfolist;
use App\Filament\Candidato\Resources\Applications\Tables\ApplicationsTable;
use App\Models\Application;
use Filament\Navigation\NavigationItem;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class ApplicationResource extends Resource
{
    protected static ?string $model = Application::class;
    protected static ?string $modelLabel = 'Inscrição';
    protected static ?string $pluralModelLabel = 'Minhas Inscrições';
    protected static ?string $slug = 'inscricoes';
    protected static string | \BackedEnum | null $navigationIcon = Heroicon::OutlinedListBullet;
    protected static string | \UnitEnum | null $navigationGroup = 'Área do Candidato';
    protected static ?int $navigationSort = 1;

    public static function infolist(Schema $schema): Schema
    {
        return ApplicationInfolist::configure($schema);
    }

    public static function form(Schema $schema): Schema
    {
        return ApplicationForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ApplicationsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListApplications::route('/'),
            'create' => CreateApplication::route('/create'),
            'view' => PrintApplication::route('/{record}'),
            // 'print' => PrintApplication::route('/{record}/print'),
        ];
    }

    // override: impede que os links nova application e minhas inscricoes fiquem ativos ao mesmo tempo
    public static function getNavigationItems(): array
    {
        return [
            NavigationItem::make(static::getNavigationLabel())
                ->group(static::getNavigationGroup())
                ->parentItem(static::getNavigationParentItem())
                ->icon(static::getNavigationIcon())
                ->activeIcon(static::getActiveNavigationIcon())
                ->isActiveWhen(fn() => request()->routeIs(static::getRouteBaseName() . '.*')
                    && !request()->routeIs(static::getRouteBaseName() . '.create'))
                ->badge(static::getNavigationBadge(), color: static::getNavigationBadgeColor())
                ->badgeTooltip(static::getNavigationBadgeTooltip())
                ->sort(static::getNavigationSort())
                ->url(static::getNavigationUrl()),
        ];
    }
}
