<?php

namespace App\Filament\Gps\Resources\ProcessoSeletivos\Pages;

use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\TextColumn;
use Filament\Actions\CreateAction;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\Action;
use App\Filament\Gps\Resources\ProcessoSeletivos\ProcessoSeletivoResource;
use App\Models\ProcessoSeletivoAnexo;
use Filament\Actions;
use Filament\Forms;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Resources\Pages\ManageRelatedRecords;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;

class ManageAnexos extends ManageRelatedRecords
{
    protected static string $resource = ProcessoSeletivoResource::class;
    protected static ?string $title = 'Gerenciar Anexos';
    protected static string $relationship = 'anexos';
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-paper-clip';

    public static function getNavigationLabel(): string
    {
        return 'Anexos';
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                TextInput::make('description')
                    ->required()
                    ->maxLength(255),
                SpatieMediaLibraryFileUpload::make('arquivo')
                    ->label('Arquivo')
                    ->required()
                    ->acceptedFileTypes(['application/pdf'])
                    ->helperText('* É necessário salvar as alterações após a inclusão do arquivo.')
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('description')
            ->heading('Anexos')
            ->columns([
                TextColumn::make('idprocesso_seletivo_anexo')->searchable()->limit(70),
                TextColumn::make('description')->searchable()->limit(70),
            ])
            ->headerActions([
                CreateAction::make()
                    ->label('Novo Anexo')
                    ->createAnother(false)
                    ->databaseTransaction()
                    ->mutateDataUsing(function (array $data) {

                        $id = ProcessoSeletivoAnexo::latest('idprocesso_seletivo_anexo')
                            ->value('idprocesso_seletivo_anexo') ?? 0;
                        $data['idprocesso_seletivo_anexo'] = $id + 1;
                        $data['publication_date'] = now();
                        $data['views'] = 0;

                        return $data;
                    }),
            ])
            ->recordActions([
                EditAction::make()
                    ->hidden(fn($record) => Carbon::parse($record->publication_date)->lt(Carbon::parse('2024-11-01'))),
                DeleteAction::make(),
                Action::make('download')
                    ->disabled(fn($record) => !$record->url_arquivo)
                    ->label('Download')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->url(fn($record) => $record->url_arquivo)
                    ->openUrlInNewTab(),
            ]);
    }
}
