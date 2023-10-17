<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use App\Sala;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Configuracoes;
use App\SalaOld;

class SendMailUserLdap extends Mailable
{
    use Queueable, SerializesModels;

    private $user;
    private $pass;
    private $email_suporte;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($user, $pass)
    {
        $this->user = $user;
        $this->pass = $pass;
        if ($this->user == NULL)
            abort(404, "Usuário não encontrado!");
        $configEmail = Configuracoes::where('nome', Configuracoes::CONFIGURACAO_EMAIL_SUPORTE)->first();
        if ($configEmail != NULL)
            $this->email_suporte = $configEmail->valor;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $view = "email.emailLdap";
        return $this->from(env('MAIL_FROM_ADDRESS'))
            ->view($view)
            ->subject('Senha alterada para: '.$this->user['cn'][0])
            ->with([
                'user' => $this->user,
                'email' => $this->email_suporte,
                'pass' => $this->pass,
                'acao' => 'alterada'
            ]);
    }
}
