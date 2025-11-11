<?php

namespace App\Filament\Gps\Resources\Processes\Pages;

use App\Filament\Components\AttachmentUpload;
use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\TextColumn;
use Filament\Actions\CreateAction;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\Action;
use App\Filament\Gps\Resources\Processes\ProcessResource;
use Filament\Forms\Components\DatePicker;
use Filament\Resources\Pages\ManageRelatedRecords;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class ManageAttachments extends ManageRelatedRecords
{
    protected static string $resource = ProcessResource::class;
    protected static ?string $title = 'Gerenciar Anexos';
    protected static ?string $navigationLabel = 'Anexos';
    protected static string $relationship = 'attachments';
    protected static ?string $breadcrumb = 'Anexos';
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-paper-clip';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                TextInput::make('description')
                    ->label('Descrição')
                    ->required()
                    ->maxLength(255),
                DatePicker::make('publication_date')
                    ->label('Data de publicação')
                    ->helperText('Será publicado apenas nessa data')
                    ->required()
                    ->default(fn() => now()->toDateString()),
                AttachmentUpload::make('arquivo')
                    ->label('Arquivo')
                    ->helperText('É necessário salvar as alterações após a inclusão do arquivo.')
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('description')
            ->heading('Anexos')
            ->modelLabel('Anexo')
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('description')
                    ->label('Descrição')
                    ->searchable()
                    ->limit(70),
                TextColumn::make('publication_date')
                    ->label('Data de Publicação')
                    ->date('d/m/y')
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
