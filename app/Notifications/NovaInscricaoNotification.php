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
        $primeiroNome = explode(' ', trim($notifiable->nome))[0];

        return (new MailMessage)
            ->subject('Recebimento de Inscrição')
            ->greeting('Prezado(a) ' . $primeiroNome . ',')
            ->line('Informamos que sua inscrição foi recebida pelo sistema.')
            ->line('Código da inscrição: ' . $this->inscricao->cod_inscricao)
            ->line('Processo Seletivo: ' . $this->inscricao->processo_seletivo->titulo)
            ->line('Vaga: ' . $this->inscricao->inscricao_vaga->descricao)
            ->line('Tipo: ' . $this->inscricao->tipo_vaga->descricao)
            ->action('Visualizar Inscrição', route('filament.candidato.resources.inscricoes.view', $this->inscricao))->success()
            ->line('Para qualquer dúvida, entrar em contato com dips@ueap.edu.br');
    }
}
