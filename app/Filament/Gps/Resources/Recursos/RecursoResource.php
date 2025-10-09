<?php

namespace App\Filament\Gps\Resources\Recursos;

use Filament\Schemas\Schema;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Actions;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Actions\ViewAction;
use Filament\Actions\EditAction;
use Filament\Actions\BulkActionGroup;
use App\Filament\Gps\Resources\Recursos\Pages\ListRecursos;
use App\Filament\Gps\Resources\Recursos\Pages\CreateRecurso;
use App\Filament\Gps\Resources\Recursos\Pages\EditRecurso;
use App\Filament\Gps\Resources\RecursoResource\Pages;
use App\Filament\Gps\Resources\RecursoResource\RelationManagers;
use App\Filament\Gps\Resources\Recursos\Schemas\RecursoForm;
use App\Filament\Gps\Resources\Recursos\Tables\RecursosTable;
use App\Models\Appeal;
use Filament\Forms;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;

class RecursoResource extends Resource
{
    protected static ?string $model = Appeal::class;

    protected static string | \BackedEnum | null $navigationIcon = Heroicon::OutlinedChatBubbleBottomCenterText;
    protected static string | \UnitEnum | null $navigationGroup = 'Gerenciar';
    protected static ?int $navigationSort = 3;

    // Acesso restrito a evaluators, que julgam recursos anonimamente apenas nos processos aos quais estão vinculados

    public static function canAccess(): bool
    {
        return Auth::user()->hasAnyRole('admin|avaliador');
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);

        $userId = auth()->id();

        $query->whereIn('id', function ($query) use ($userId) {
            $query->select('id')
                ->from('process_user')
                ->where('user_id', $userId);
        });

        return $query;
    }

    public static function form(Schema $schema): Schema
    {
        return RecursoForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return RecursosTable::configure($table);
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
            'index' => ListRecursos::route('/'),
            'create' => CreateRecurso::route('/create'),
            // 'view' => Pages\ViewRecurso::route('/{record}'),
            'edit' => EditRecurso::route('/{record}/edit'),
        ];
    }
}
