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
use App\Models\ProcessAttachment;
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
    protected static string $relationship = 'attachments';
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
                    ->label('Descrição')
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
            ->modelLabel('Anexo')
            ->columns([
                TextColumn::make('description')
                    ->label('Descrição')
                    ->searchable()
                    ->limit(70),
            ])
            ->headerActions([
                CreateAction::make()
                    ->label('Novo Anexo')
                    ->createAnother(false)
            ])
            ->recordActions([
                EditAction::make()
                    ->hidden(fn($record) => Carbon::parse($record->created_at)->lt(Carbon::parse('2024-11-01'))),
                DeleteAction::make(),
                Action::make('download')
                    ->disabled(fn($record) => !$record->file_url)
                    ->label('Download')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->url(fn($record) => $record->file_url)
                    ->openUrlInNewTab(),
            ]);
    }
}
