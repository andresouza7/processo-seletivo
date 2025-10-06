<?php

namespace App\Filament\Candidato\Resources\Inscricaos;

use Filament\Schemas\Schema;
use App\Filament\Candidato\Resources\Inscricaos\Pages\ListInscricaos;
use App\Filament\Candidato\Resources\Inscricaos\Pages\CreateInscricao;
use App\Filament\Candidato\Resources\Inscricaos\Pages\ViewInscricao;
use App\Filament\Candidato\Resources\InscricaoResource\Pages;
use App\Filament\Candidato\Resources\Inscricaos\Schemas\InscricaoForm;
use App\Filament\Candidato\Resources\Inscricaos\Schemas\InscricaoInfolist;
use App\Filament\Candidato\Resources\Inscricaos\Tables\InscricaosTable;
use App\Models\Inscricao;
use Filament\Navigation\NavigationItem;
use Filament\Resources\Resource;
use Filament\Tables\Table;

class InscricaoResource extends Resource
{
    protected static ?string $model = Inscricao::class;
    protected static ?string $modelLabel = 'Inscrição';
    protected static ?string $pluralModelLabel = 'Minhas Inscrições';
    protected static ?string $slug = 'inscricoes';
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-list-bullet';
    protected static string | \UnitEnum | null $navigationGroup = 'Área do Candidato';
    protected static ?int $navigationSort = 1;

    public static function infolist(Schema $schema): Schema
    {
        return InscricaoInfolist::configure($schema);
    }

    public static function form(Schema $schema): Schema
    {
        return InscricaoForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return InscricaosTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListInscricaos::route('/'),
            'create' => CreateInscricao::route('/create'),
            'view' => ViewInscricao::route('/{record}'),
        ];
    }

    // override: impede que os links nova inscricao e minhas inscricoes fiquem ativos ao mesmo tempo
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
