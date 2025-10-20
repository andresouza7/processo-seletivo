<?php

namespace App\Filament\Gps\Resources\Appeals;

use Filament\Schemas\Schema;
use App\Filament\Gps\Resources\Appeals\Pages\ListAppeals;
use App\Filament\Gps\Resources\Appeals\Pages\EditAppeal;
use App\Filament\Gps\Resources\AppealResource\Pages;
use App\Filament\Gps\Resources\AppealResource\RelationManagers;
use App\Filament\Gps\Resources\Appeals\Schemas\AppealForm;
use App\Filament\Gps\Resources\Appeals\Tables\AppealsTable;
use App\Models\Appeal;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;

class AppealResource extends Resource
{
    protected static ?string $model = Appeal::class;
    protected static ?string $modelLabel = 'Recurso';
    protected static ?string $slug = 'recursos';
    protected static string | \BackedEnum | null $navigationIcon = Heroicon::OutlinedChatBubbleBottomCenterText;
    protected static string | \UnitEnum | null $navigationGroup = 'Gerenciar';
    protected static ?int $navigationSort = 3;

    // Acesso restrito a usuários com perfil de avaliador
    
    public static function canAccess(): bool
    {
        return Auth::user()->hasRole('admin|avaliador');
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);

        $userId = auth()->id();

        $query->where('evaluator_id', $userId);

        return $query;
    }

    public static function form(Schema $schema): Schema
    {
        return AppealForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AppealsTable::configure($table);
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
            'index' => ListAppeals::route('/'),
            // 'view' => Pages\ViewAppeal::route('/{record}'),
            'edit' => EditAppeal::route('/{record}/edit'),
        ];
    }
}
