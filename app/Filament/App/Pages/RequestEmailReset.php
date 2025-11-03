<?php

namespace App\Filament\App\Pages;

use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Actions;
use Filament\Actions\Action;
use App\Actions\ResetCandidatoEmailAction;
use App\Models\Candidate;
use App\Notifications\ResetEmailNotification;
use BackedEnum;
use DanHarrin\LivewireRateLimiting\Exceptions\TooManyRequestsException;
use DanHarrin\LivewireRateLimiting\WithRateLimiting;
use Filament\Facades\Filament;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Str;

class RequestEmailReset extends Page implements HasSchemas
{
    use InteractsWithSchemas, WithRateLimiting;

    protected static string | BackedEnum | null $navigationIcon = Heroicon::DocumentText;

    protected static ?string $title = 'Redefinir Email';

    protected string $view = 'filament.app.pages.request-email-reset';

    protected static bool $shouldRegisterNavigation = false;

    protected static ?string $slug = 'redefinir-email';

    public array $data;

    public function form(Schema $schema): Schema
    {
        return $schema
            ->statePath('data')
            ->components([
                Section::make('Redefinição de E-mail')
                    ->description('Preencha os campos abaixo com seus dados pessoais para validação de identidade e redefinição do endereço de e-mail cadastrado.')
                    ->columns(2)
                    ->schema([
                        TextInput::make('name')
                            ->label('Nome completo')
                            ->placeholder('Digite seu nome completo')
                            ->required()
                            ->reactive(),

                        TextInput::make('cpf')
                            ->label('CPF')
                            ->placeholder('Somente números')
                            // ->rules(['cpf'])
                            ->mask('99999999999')
                            ->required()
                            ->reactive(),

                        TextInput::make('rg')
                            ->label('Documento de Identidade (RG)')
                            ->placeholder('Somente números')
                            ->mask('99999999999999')
                            ->reactive(),

                        TextInput::make('mother_name')
                            ->label('Nome da mãe')
                            ->placeholder('Digite o nome completo da mãe')
                            ->reactive(),

                        DatePicker::make('birth_date')
                            ->label('Data de nascimento')
                            ->placeholder('Selecione a data de nascimento')
                            ->reactive(),

                        TextInput::make('email')
                            ->label('Novo e-mail')
                            ->placeholder('exemplo@dominio.com')
                            ->helperText('Informe um endereço de e-mail válido para atualização do cadastro.')
                            ->required()
                            ->email()
                            ->columnSpanFull(),

                        Actions::make([
                            Action::make('submit')
                                ->label('Enviar solicitação')
                                ->submit('submit')
                                ->color('primary')
                                ->icon('heroicon-o-paper-airplane'),
                        ]),
                    ]),
            ]);
    }

    // candidato deverá validar nome, cpf e mais um dado adicional para redefinir email
    public function submit()
    {
        try {
            $this->rateLimit(3);
        } catch (TooManyRequestsException $exception) {
            $this->getRateLimitedNotification($exception)?->send();
            return;
        }

        $this->form->validate();

        // Normalize inputs
        $cpf = preg_replace('/\D/', '', $this->data['cpf']);
        $rg  = preg_replace('/\D/', '', $this->data['rg'] ?? '');
        $birthDate = $this->data['birth_date'] ?? null;

        $normalizedName = $this->normalizeName($this->data['name']);
        $normalizedMotherName = isset($this->data['mother_name'])
            ? $this->normalizeName($this->data['mother_name'])
            : null;

        // Fetch candidate once
        $candidate = Candidate::query()
            ->where('cpf', $cpf)
            ->first();

        // If candidate exists, validate all personal data in memory
        $isValid = $candidate && $this->validateCandidateData(
            $candidate,
            $normalizedName,
            $normalizedMotherName,
            $birthDate,
            $rg
        );

        if (!$isValid) {
            return $this->notifyError('Os dados informados não correspondem a nenhum registro.');
        }

        // Proceed with email reset
        (new ResetCandidatoEmailAction())->reset($candidate, $this->data['email'], function () {
            Notification::make()
                ->title('E-mail redefinido com sucesso')
                ->body('Uma senha temporária foi enviada para o novo endereço de e-mail informado.')
                ->success()
                ->duration(10000)
                ->send();

            $this->redirectRoute('filament.candidato.pages.dashboard');
        });
    }

    /**
     * Normalize name by removing accents, non-letters except hyphen, and converting to lowercase
     */
    private function normalizeName(string $name): string
    {
        // Remove accents
        $name = \Illuminate\Support\Str::ascii($name);
        // Keep only letters and hyphen, lowercase
        return preg_replace('/[^a-zA-Z-]/', '', strtolower($name));
    }

    /**
     * Validate candidate data in memory
     */
    private function validateCandidateData(
        Candidate $candidate,
        string $normalizedName,
        ?string $normalizedMotherName,
        ?string $birthDate,
        string $rg
    ): bool {
        // Normalize candidate fields
        $candidateName = preg_replace('/[^a-zA-Z-]/', '', strtolower(\Illuminate\Support\Str::ascii($candidate->name)));
        $candidateMotherName = $candidate->mother_name
            ? preg_replace('/[^a-zA-Z-]/', '', strtolower(\Illuminate\Support\Str::ascii($candidate->mother_name)))
            : null;
        $candidateRg = preg_replace('/\D/', '', $candidate->rg ?? '');

        $nameMatches = $normalizedName === $candidateName;
        $motherNameMatches = $normalizedMotherName && $candidateMotherName
            ? $normalizedMotherName === $candidateMotherName
            : false;
        $birthDateMatches = $birthDate && $candidate->birth_date
            ? $birthDate === $candidate->birth_date->format('Y-m-d')
            : false;
        $rgMatches = $rg && $candidateRg ? $rg === $candidateRg : false;

        // Name must match, plus at least one of the others
        return $nameMatches && ($motherNameMatches || $birthDateMatches || $rgMatches);
    }

    /**
     * Send a standardized error notification.
     */
    protected function notifyError(string $message): void
    {
        Notification::make()
            ->title('Erro na validação dos dados')
            ->body($message)
            ->danger()
            ->send();
    }

    protected function getRateLimitedNotification(TooManyRequestsException $exception): ?Notification
    {
        $minutos = $exception->minutesUntilAvailable;
        $segundos = $exception->secondsUntilAvailable;

        // Exibe o tempo de forma amigável (prioriza minutos quando >= 1)
        $tempoRestante = $minutos >= 1
            ? "{$minutos} " . str('minuto')->plural($minutos)
            : "{$segundos} " . str('segundo')->plural($segundos);

        return Notification::make()
            ->title('Muitas tentativas de redefinição')
            ->body("Você excedeu o número máximo de tentativas. Aguarde {$tempoRestante} antes de tentar novamente.")
            ->danger();
    }
}
