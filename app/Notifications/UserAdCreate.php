<?php

namespace App\Notifications;

use App\Configuracoes;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class UserAdCreate extends Notification implements ShouldQueue
{
    use Queueable;

    private $user;
    private $pass;
    private $acao;
    private $email_suporte;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($user, $pass, $acao)
    {
        $this->user = $user;
        $this->pass = $pass;
        $this->acao = $acao;
        if ($this->user == NULL)
            abort(404, "Usuário não encontrado!");
            $configEmail = Configuracoes::where('nome', Configuracoes::CONFIGURACAO_EMAIL_SUPORTE)->first();
        if ($configEmail != NULL)
            $this->email_suporte = $configEmail->valor;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
                    ->from(env('MAIL_FROM_ADDRESS'), 'Ti Ead')
                    ->subject('Conta AD '.$this->acao.' : '.$this->user['cn'][0])
                    ->markdown('email.emailLdap',[
                        'user' => $this->user, 
                        'email' => $this->email_suporte, 
                        'pass' => $this->pass,
                        'acao' => $this->acao,
                ]);
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
