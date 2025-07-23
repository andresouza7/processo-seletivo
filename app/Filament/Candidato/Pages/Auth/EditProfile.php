<?php

namespace App\Filament\Candidato\Pages\Auth;

use Filament\Forms\Form;
use Filament\Pages\Auth\EditProfile as BaseEditProfile;

class EditProfile extends BaseEditProfile
{
    protected static ?string $navigationIcon = 'heroicon-o-document-text';

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
