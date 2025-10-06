<?php

namespace App\Filament\Candidato\Pages\Auth;

use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Model;

class EditPassword extends \Filament\Auth\Pages\EditProfile
{
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-document-text';

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $record->must_change_password = false;
        $record->update($data);

        return $record;
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                $this->getEmailFormComponent()->disabled(),
                $this->getPasswordFormComponent(),
                $this->getPasswordConfirmationFormComponent()->visible(),
            ]);
    }

    protected function getSavedNotificationTitle(): ?string
    {
        return "Sua senha foi redefinida com sucesso!";
    }

    protected function getRedirectUrl(): ?string
    {
        return route("filament.candidato.pages.dashboard");
    }
}
