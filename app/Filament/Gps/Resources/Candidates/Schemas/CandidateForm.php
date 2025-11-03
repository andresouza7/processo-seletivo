<?php

namespace App\Filament\Gps\Resources\Candidates\Schemas;

use App\Filament\Candidato\Pages\Auth\Cadastro;
use App\Models\Candidate;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class CandidateForm
{
    public static function configure(Schema $schema, ?Candidate $record = null): Schema
    {
        return $schema
            ->components(Cadastro::getProfileSections($record));
    }
}
