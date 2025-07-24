<?php

namespace App\Filament\Candidato\Pages\Auth;

use Filament\Forms\Form;
use Filament\Pages\Auth\EditProfile as BaseEditProfile;
use Illuminate\Database\Eloquent\Model;

class EditProfile extends BaseEditProfile
{
    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $record->must_change_password = false;
        $record->update($data);

        return $record;
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
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

    // public static function getLabel(): string
    // {
    //     return 'Senha';
    // }
}
