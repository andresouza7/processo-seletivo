<?php

namespace App\Filament\Components;

use Filament\Forms\Components\SpatieMediaLibraryFileUpload;

class AttachmentUpload extends SpatieMediaLibraryFileUpload
{
    public static function make(?string $name = null): static
    {
        $instance = parent::make($name);

        // Apply your defaults once, but still return a full component instance
        $instance
            ->maxFiles(1)
            ->required()
            ->hint('(máx. 2MB)')
            ->rules(['file', 'mimes:pdf', 'max:2048'])
            ->acceptedFileTypes(['application/pdf']);

        return $instance;
    }
}
