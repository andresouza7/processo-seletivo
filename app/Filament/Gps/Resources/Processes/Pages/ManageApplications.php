<?php

namespace App\Filament\Gps\Resources\Processes\Pages;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Tables\Columns\TextColumn;
use Filament\Actions\ExportAction;
use Filament\Actions\ViewAction;
use Filament\Actions\DeleteAction;
use App\Filament\Exports\ApplicationExporter;
use App\Filament\Gps\Resources\Processes\ProcessResource;
use App\Services\SelectionProcess\ApplicationService;
use Filament\Actions;
use Filament\Forms;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Pages\ManageRelatedRecords;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;

class ManageApplications extends ManageRelatedRecords
{
    protected static string $resource = ProcessResource::class;
    protected static ?string $title = 'Gerenciar Inscrições';
    protected static string $relationship = 'applications';
    protected static ?string $navigationLabel = 'Inscrições';
    protected static ?string $breadcrumb = 'Inscrições';
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-users';

    public function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                // Candidato Section
                Section::make('Candidato')
                    ->schema([
                        TextEntry::make('candidate.name')->label('Nome'),
                        TextEntry::make('candidate.rg')->label('Documento Identidade'),
                        TextEntry::make('candidate.cpf')->label('CPF'),
                        TextEntry::make('candidate.address')->label('Endereço'),
                        TextEntry::make('candidate.district')->label('Bairro'),
                        TextEntry::make('candidate.city')->label('Cidade'),
                        TextEntry::make('candidate.email')->label('Email'),
                    ])
                    ->columns(2),

                // Inscrição Section
                Section::make('Inscrição')
                    ->schema([
                        TextEntry::make('code')->label('Cód. Inscrição'),
                        TextEntry::make('position.description')->label('Vaga'),
                        TextEntry::make('quota.description')->label('Tipo de Vaga'),
                    ])
                    ->columns(2),

                TextEntry::make('code')
                    ->label('Documentos')
                    ->formatStateUsing(function ($record) {
                        // $record é a instância do modelo
                        $links = $record->getMedia()->map(function ($media) {
                            $route = route('media.temp', $media?->uuid);
                            return '<a href="' . $route . '" target="_blank" class="text-primary hover:underline">' . $media->name . '</a>';
                        })->implode('<br>');

                        return $links ?: '-';
                    })
                    ->color('primary')
                    ->html(),

                TextEntry::make('code')
                    ->label('Laudo Médico')
                    ->formatStateUsing(function ($record) {
                        $link = tempMediaUrl($record, 'laudo_medico');
                        $text = '<a href="' . $link . '" target="_blank" class="text-primary hover:underline">Abrir</a>';

                        return $link ? $text : '-';
                    })
                    ->color('primary')
                    ->html(),

                TextEntry::make('code')
                    ->label('Isenção Taxa')
                    ->formatStateUsing(function ($record) {
                        $link = tempMediaUrl($record, 'isencao_taxa');
                        $text = '<a href="' . $link . '" target="_blank" class="text-primary hover:underline">Abrir</a>';

                        return $link ? $text : '-';
                    })
                    ->color('primary')
                    ->html(),
            ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('code')
            ->heading('Inscrições')
            ->query(
                fn(ApplicationService $service) =>
                $service->fetchFinalApplicationsForProcess($this->getOwnerRecord())
            )
            ->columns([
                TextColumn::make('code')
                    ->label('Cód. Inscrição')
                    ->searchable(),
                TextColumn::make('candidate.name')
                    ->label('Candidato')
                    ->searchable(),
                TextColumn::make('position.description')
                    ->label('Vaga')
                    ->searchable(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                // Tables\Actions\CreateAction::make(),
                ExportAction::make()
                    ->label('Exportar para planilha')
                    ->color('primary')
                    ->exporter(ApplicationExporter::class)
                    ->options(['process_id' => $this->getOwnerRecord()->id])
            ])
            ->recordActions([
                ViewAction::make(),
                // Tables\Actions\EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                // Tables\Actions\BulkActionGroup::make([
                //     Tables\Actions\DissociateBulkAction::make(),
                //     Tables\Actions\DeleteBulkAction::make(),
                // ]),
            ]);
    }
}
