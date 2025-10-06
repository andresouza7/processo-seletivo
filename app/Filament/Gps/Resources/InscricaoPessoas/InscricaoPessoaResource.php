<?php

namespace App\Filament\Gps\Resources\InscricaoPessoas;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Fieldset;
use Filament\Tables\Columns\TextColumn;
use Filament\Actions\ViewAction;
use App\Filament\Gps\Resources\InscricaoPessoas\Pages\ListInscricaoPessoas;
use App\Filament\Gps\Resources\InscricaoPessoas\Pages\ViewInscricaoPessoa;
use App\Filament\Gps\Resources\InscricaoPessoaResource\Pages;
use App\Filament\Gps\Resources\InscricaoPessoas\Schemas\InscricaoPessoaForm;
use App\Filament\Gps\Resources\InscricaoPessoas\Schemas\InscricaoPessoaInfolist;
use App\Filament\Gps\Resources\InscricaoPessoas\Tables\InscricaoPessoasTable;
use App\Filament\Resources\InscricaoPessoaResource\RelationManagers;
use App\Models\InscricaoPessoa;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Wizard;
use Filament\Forms\Components\Wizard\Step;
use Filament\Infolists\Components\TextEntry;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class InscricaoPessoaResource extends Resource
{
    protected static ?string $model = InscricaoPessoa::class;
    protected static ?string $modelLabel = 'Candidato';
    protected static ?string $slug = 'candidatos';
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-users';
    protected static string | \UnitEnum | null $navigationGroup = 'Gerenciar';
    protected static ?int $navigationSort = 2;
    protected static ?string $recordTitleAttribute = 'nome';

    public static function canAccess(): bool
    {
        return Auth::user()->hasRole('gestor|admin');
    }

    public static function infolist(Schema $schema): Schema
    {
        return InscricaoPessoaInfolist::configure($schema);
    }

    public static function form(Schema $schema): Schema
    {
        return InscricaoPessoaForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return InscricaoPessoasTable::configure($table);
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
            'index' => ListInscricaoPessoas::route('/'),
            // 'edit' => Pages\EditInscricaoPessoa::route('/{record}/edit'),
            'view' => ViewInscricaoPessoa::route('/{record}/view'),
        ];
    }
}
