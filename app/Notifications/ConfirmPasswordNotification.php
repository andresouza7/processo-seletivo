<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Filament\Facades\Filament;
use Illuminate\Support\Facades\Lang;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
 
class ConfirmEmailNotification extends Notification
{
    use Queueable;
 
    public function __construct(private readonly string $token)
    {}
 
    public function via(object $notifiable): array
    {
        return ['mail'];
    }
 
    public function toMail(object $notifiable): MailMessage
    {
         app()->setLocale('pt_BR');

        $firstName = explode(' ', trim($notifiable->name))[0];

        return (new MailMessage)
            ->subject(Lang::get('Notificação de confirmação de email'))
            ->greeting(Lang::get('Olá') . " {$firstName},")
            ->line(Lang::get('Por favor, clique no botão abaixo para confirmar o seu email.'))
            ->action(Lang::get('Confirmar email'), $this->confirmEmailUrl($notifiable))->success();
            // ->line(Lang::get('Se você não se cadastrou no site Processo Seletivo UEAP, nenhuma ação adicional é necessária.'));
    }
 
    protected function confirmEmailUrl(mixed $notifiable): string
    {
        return Filament::getVerifyEmailUrl($notifiable);
    }
 
}
