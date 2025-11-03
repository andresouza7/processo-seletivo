<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Filament\Facades\Filament;
use Illuminate\Support\Facades\Lang;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class ResetPasswordNotification extends Notification
{
    use Queueable;

    public function __construct(private readonly string $token) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $name = $notifiable->getFilamentName();

        return (new MailMessage)
            ->subject(Lang::get('Notificação de Redefinição de Senha'))
            ->greeting(Lang::get('Olá') . " {$name},")
            ->line(Lang::get('Você está recebendo este e-mail porque recebemos uma solicitação de redefinição de senha para a sua conta.'))
            ->action(Lang::get('Redefinir Senha'), $this->resetUrl($notifiable))->success()
            ->line(Lang::get('Este link de redefinição de senha expirará em :count minutos.', ['count' => config('auth.passwords.' . config('auth.defaults.passwords') . '.expire')]));
        // ->line(Lang::get('Se você não solicitou a redefinição de senha, nenhuma ação adicional é necessária.'));
    }

    protected function resetUrl(mixed $notifiable): string
    {
        return Filament::getResetPasswordUrl($this->token, $notifiable);
    }
}
