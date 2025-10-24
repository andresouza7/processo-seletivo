<?php

namespace App\Filament\Gps\Resources\Candidates;

use Filament\Schemas\Schema;
use App\Filament\Gps\Resources\Candidates\Pages\ListCandidates;
use App\Filament\Gps\Resources\Candidates\Pages\ViewCandidate;
use App\Filament\Gps\Resources\CandidateResource\Pages;
use App\Filament\Gps\Resources\Candidates\Pages\EditCandidate;
use App\Filament\Gps\Resources\Candidates\Schemas\CandidateForm;
use App\Filament\Gps\Resources\Candidates\Schemas\CandidateInfolist;
use App\Filament\Gps\Resources\Candidates\Tables\CandidatesTable;
use App\Filament\Resources\CandidateResource\RelationManagers;
use App\Models\Candidate;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;

class CandidateResource extends Resource
{
    protected static ?string $model = Candidate::class;
    protected static ?string $modelLabel = 'Candidato';
    protected static ?string $slug = 'candidatos';
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-users';
    protected static string | \UnitEnum | null $navigationGroup = 'Gerenciar';
    protected static ?int $navigationSort = 2;
    protected static ?string $recordTitleAttribute = 'name';

    public static function infolist(Schema $schema): Schema
    {
        return CandidateInfolist::configure($schema);
    }

    public static function form(Schema $schema, ?Candidate $record = null): Schema
    {
        return CandidateForm::configure($schema, $record);
    }

    public static function table(Table $table): Table
    {
        return CandidatesTable::configure($table);
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
            'index' => ListCandidates::route('/'),
            // 'edit' => EditCandidate::route('/{record}/edit'),
            'view' => ViewCandidate::route('/{record}/view'),
        ];
    }
}
