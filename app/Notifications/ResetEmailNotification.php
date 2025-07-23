<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Filament\Facades\Filament;
use Illuminate\Support\Facades\Lang;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
 
class ResetEmailNotification extends Notification
{
    use Queueable;
 
    public function __construct(private readonly string $senha)
    {}
 
    public function via(object $notifiable): array
    {
        return ['mail'];
    }
 
    public function toMail(object $notifiable): MailMessage
    {
        $firstName = explode(' ', trim($notifiable->nome))[0];

        return (new MailMessage)
            ->subject(Lang::get('Notificação de Redefinição de Email'))
            ->greeting(Lang::get('Olá') . " {$firstName},")
            ->line(Lang::get('Você está recebendo este e-mail porque recebemos uma solicitação de redefinição de email para a sua conta.'))
            ->line(Lang::get('Seu novo email foi cadastrado com sucesso. Acesse a área do candidato pelo link abaixo'))
            ->action(Lang::get('Área do Candidato'), route('filament.candidato.pages.dashboard'))->success()
            ->line(Lang::get('Uma senha temporária foi gerada para você: :senha', ['senha' => $this->senha]));
    }
}
