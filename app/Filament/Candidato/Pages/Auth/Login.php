<?php

namespace App\Filament\Candidato\Pages\Auth;

use App\Models\InscricaoPessoa;
use App\Models\Pessoa;
use Filament\Forms;
use Filament\Pages\Page;
use App\Models\User;
use DanHarrin\LivewireRateLimiting\Exceptions\TooManyRequestsException;
use Exception;
use Filament\Facades\Filament;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\Split;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Filament\Pages\Auth\Login as BaseLogin;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Filament\Http\Responses\Auth\Contracts\LoginResponse;
use Filament\Models\Contracts\FilamentUser;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\HtmlString;

class Login extends BaseLogin
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

    public function form(Form $form): Form
    {
        return $form
            ->schema([
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
        if ($user instanceof FilamentUser && ! $user->canAccessPanel(Filament::getCurrentPanel())) {
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

    private function useMd5AuthenticationMethod(array $data)
    {
        // Attempt to find the user by cpf
        $user = InscricaoPessoa::where('cpf', $data['cpf'])->first();

        if (!$user || $user->senha !== md5($data['senha'])) {
            $this->throwFailureValidationException();
        }

        // Log the user in manually
        Auth::guard('candidato')->login($user, $data['remember'] ?? false);

        return $user;
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
