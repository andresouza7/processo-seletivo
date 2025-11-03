<?php

namespace App\Filament\Candidato\Pages\Auth;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Component;
use Filament\Facades\Filament;
use App\Filament\Components\StrictTextInput;
use Illuminate\Validation\ValidationException;

class Login extends \Filament\Auth\Pages\Login
{
    protected function getFormActions(): array
    {
        return [
            $this->getAuthenticateFormAction(),
            \Filament\Actions\Action::make('voltar')
                ->url('/')
                ->label('Voltar para o site')
                ->color('gray')
        ];
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                $this->getCpfFormComponent(),
                $this->getPasswordFormComponent(),
                $this->getRememberFormComponent()
            ]);
    }

    protected function getCpfFormComponent(): Component
    {
        return StrictTextInput::make('cpf')
            ->label('CPF')
            ->required()
            // ->rules(['cpf'])
            ->maxLength(11)
            ->extraInputAttributes(['tabindex' => 1]);
    }

    protected function getCredentialsFromFormData(array $data): array
    {
        return [
            'cpf' => $data['cpf'],
            'password' => $data['password'],
        ];
    }

    protected function throwFailureValidationException(): never
    {
        throw ValidationException::withMessages([
            // 'data.cpf' => __('filament-panels::auth/pages/login.messages.failed'),
            'data.cpf' => 'Usuário não encontrado',
        ]);
    }
}
