<?php

namespace App\Notifications;

namespace App\Notifications;

use App\Models\Application;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class NovaInscricaoNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Application $application) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $name = $notifiable->getFilamentName();

        return (new MailMessage)
            ->subject('Recebimento de Inscrição')
            ->greeting('Prezado(a) Candidato(a)')
            ->line('Confirmamos o recebimento de sua inscrição no sistema de processos seletivos da UEAP. Confira abaixo os detalhes:')
            ->line('Código da inscrição: ' . $this->application->code)
            ->line('Processo Seletivo: ' . $this->application->process->title)
            ->line('Vaga: ' . $this->application->position->description)
            ->line('Tipo: ' . $this->application->quota->description)
            ->action('Visualizar Inscrição', route('filament.candidato.resources.inscricoes.view', $this->application))->success()
            ->line('Para qualquer dúvida, entrar em contato com dips@ueap.edu.br');
    }
}
