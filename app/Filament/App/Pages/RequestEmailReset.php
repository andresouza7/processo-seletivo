<?php

namespace App\Filament\App\Pages;

use App\Actions\ResetCandidatoEmailAction;
use App\Models\InscricaoPessoa;
use App\Notifications\ResetEmailNotification;
use DanHarrin\LivewireRateLimiting\Exceptions\TooManyRequestsException;
use DanHarrin\LivewireRateLimiting\WithRateLimiting;
use Filament\Facades\Filament;
use Filament\Forms\Components\Actions;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Str;

class RequestEmailReset extends Page implements HasForms
{
    use InteractsWithForms, WithRateLimiting;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $title = 'Redefinir Email';

    protected static string $view = 'filament.app.pages.request-email-reset';

    protected static bool $shouldRegisterNavigation = false;

    protected static ?string $slug = 'redefinir-email';

    public array $data;

    public function form(Form $form): Form
    {
        return $form
            ->statePath('data')
            ->schema([
                Section::make('Solicitação de Redefinição de E-mail')
                    ->description('Informe seus dados pessoais para validarmos sua identidade')
                    ->columns(2)
                    ->schema([
                        TextInput::make('nome')
                            ->columnSpanFull()
                            ->label('Nome completo')
                            ->required()
                            ->reactive(),

                        TextInput::make('cpf')
                            ->label('CPF')
                            ->helperText('apenas números')
                            ->required()
                            ->reactive(),

                        TextInput::make('ci')
                            ->label('Documento de Identidade (RG)')
                            ->helperText('apenas números')
                            ->required()
                            ->reactive(),

                        TextInput::make('email')
                            ->columnSpanFull()
                            ->label('Novo e-mail')
                            ->helperText('Informe um endereço de e-mail válido')
                            ->required()
                            ->email(),

                        Actions::make([
                            Action::make('submit')
                                ->label('Redefinir Email')
                                ->submit('submit') // This triggers the Livewire `submit()` method
                                ->color('primary')
                                // ->icon('heroicon-o-check'),
                        ])
                    ]),
            ]);
    }

    public function submit()
    {
        try {
            $this->rateLimit(3);
        } catch (TooManyRequestsException $exception) {
            $this->getRateLimitedNotification($exception)?->send();

            return null;
        }

        $usuario = InscricaoPessoa::query()
            ->where('nome', $this->data['nome'])
            ->where('cpf', $this->data['cpf'])
            ->whereRaw('REGEXP_REPLACE(ci, "[^0-9]", "") = ?', $this->data['ci'])
            ->first();

        if (!$usuario) {
            Notification::make()
                ->title('Erro')
                ->body('Nenhum usuário encontrado com esses dados!')
                ->danger()
                ->send();
            return;
        }

        $action = new ResetCandidatoEmailAction();
        $action->reset($usuario, $this->data['email'], function () {
            Notification::make()
                ->title('Seu email foi alterado com sucesso')
                ->body('Uma senha temporária foi enviada para seu novo email')
                ->success()
                ->duration(10000)
                ->send();

            $this->redirectRoute('filament.candidato.pages.dashboard');
        });
    }

    protected function getRateLimitedNotification(TooManyRequestsException $exception): ?Notification
    {
        return Notification::make()
            ->title('Muitas tentativas de redefinição')
            ->body(array_key_exists('body', __('filament-panels::pages/auth/register.notifications.throttled') ?: []) ? __('filament-panels::pages/auth/register.notifications.throttled.body', [
                'seconds' => $exception->secondsUntilAvailable,
                'minutes' => $exception->minutesUntilAvailable,
            ]) : null)
            ->danger();
    }
}
