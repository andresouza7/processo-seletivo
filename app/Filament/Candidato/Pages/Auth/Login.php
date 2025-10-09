<?php

namespace App\Filament\Candidato\Pages\Auth;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Component;
use Filament\Auth\Http\Responses\Contracts\LoginResponse;
use App\Models\Candidate;
use App\Models\Pessoa;
use Filament\Forms;
use Filament\Pages\Page;
use App\Models\User;
use DanHarrin\LivewireRateLimiting\Exceptions\TooManyRequestsException;
use Exception;
use Filament\Facades\Filament;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Split;
use Filament\Forms\Components\TextInput;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Filament\Models\Contracts\FilamentUser;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\HtmlString;

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
        return TextInput::make('cpf')
            ->label('CPF')
            ->required()
            ->rules(['cpf'])
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

    public function authenticate(): ?LoginResponse
    {
        try {
            $this->rateLimit(5);
        } catch (TooManyRequestsException $exception) {
            $this->getRateLimitedNotification($exception)?->send();

            return null;
        }

        $data = $this->getCredentialsFromFormData($this->form->getState());

        $user = $this->useBcryptAuthenticationMethod($data);

        // Ensure the user can access the current Filament panel
        if ($user instanceof FilamentUser && ! $user->canAccessPanel(Filament::getCurrentOrDefaultPanel())) {
            Auth::guard('candidato')->logout();
            $this->throwFailureValidationException();
        }

        // Regenerate session to prevent session fixation attacks
        session()->regenerate();

        return app(LoginResponse::class);
    }

    protected function throwFailureValidationException(): never
    {
        throw ValidationException::withMessages([
            
            'data.cpf' => __('filament-panels::pages/auth/login.messages.failed'),
        ]);
    }

    private function useBcryptAuthenticationMethod(array $data)
    {
        // Attempt authentication with the 'candidato' guard
        if (!Auth::guard('candidato')->attempt($data, $data['remember'] ?? false)) {
            $this->throwFailureValidationException();
        }

        // Get the authenticated user
        $user = Auth::guard('candidato')->user();

        return $user;
    }
}
