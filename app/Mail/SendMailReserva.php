<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use App\Sala;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Configuracoes;

class SendMailReserva extends Mailable
{
    use Queueable, SerializesModels;

    private $reserva;
    private $email_suporte;
    private $timezone;
    private $isUpdate;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($reserva, $isUpdate)
    {
        $this->isUpdate = $isUpdate;
        $this->reserva = $reserva;
        if ($this->reserva == NULL)
            abort(404, "Reserva não encontrada!");
        $configEmail = Configuracoes::where('nome', Configuracoes::CONFIGURACAO_EMAIL_SUPORTE)->first();
        if ($configEmail != NULL)
            $this->email_suporte = $configEmail->valor;
        else 
            $this->email_suporte = "";
        $timezone = Configuracoes::where('nome', Configuracoes::CONFIGURACAO_TIMEZONE)->first();
        if ($timezone != NULL)
            $this->timezone = $timezone->valor;
        else 
            $this->timezone = "";
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from(env('MAIL_FROM_ADDRESS'))
            ->view('email.email-reserva')
            ->subject('#'.$this->reserva->id.($this->isUpdate ? " [Alteração]" : "").' Solicitação de Reserva')
            ->with([
                'reserva' => $this->reserva, 'email' => $this->email_suporte, 'timezone' => $this->timezone, 'gestores' => [], 'isUpdate' => $this->isUpdate
            ]);
    }
}
