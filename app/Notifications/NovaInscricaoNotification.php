<?php

namespace App\Notifications;

namespace App\Notifications;

use App\Models\Inscricao;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class NovaInscricaoNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Inscricao $inscricao) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $primeiroNome = explode(' ', trim($notifiable->name))[0];

        return (new MailMessage)
            ->subject('Recebimento de Inscrição')
            ->greeting('Prezado(a) ' . $primeiroNome . ',')
            ->line('Informamos que sua inscrição foi recebida pelo sistema.')
            ->line('Código da inscrição: ' . $this->inscricao->code)
            ->line('Processo Seletivo: ' . $this->inscricao->processo_seletivo->title)
            ->line('Vaga: ' . $this->inscricao->inscricao_vaga->description)
            ->line('Tipo: ' . $this->inscricao->tipo_vaga->description)
            ->action('Visualizar Inscrição', route('filament.candidato.resources.inscricoes.view', $this->inscricao))->success()
            ->line('Para qualquer dúvida, entrar em contato com dips@ueap.edu.br');
    }
}
