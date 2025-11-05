<?php

namespace App\Filament\Admin\Resources\Users\Pages;

use Filament\Actions\CreateAction;
use App\Filament\Admin\Resources\Users\UserResource;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Filament\Support\Icons\Heroicon;

class ListUsers extends ListRecords
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('uploadManual')
                ->color('info')
                ->label('Manual Usuário')
                ->icon(Heroicon::PencilSquare)
                ->schema([
                    FileUpload::make('file')
                        ->label('Upload Manual do Usuário')
                        ->disk('public')
                        ->getUploadedFileNameForStorageUsing(fn() => 'manual-usuario.pdf')
                ])
                ->successNotification(
                    Notification::make()
                        ->title('Manual atualizado!')
                        ->success()
                ),
            CreateAction::make(),
        ];
    }
}
