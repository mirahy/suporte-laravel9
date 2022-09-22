<html>
    <body>
        <h2 style="font-weight: normal;">@if($isUpdate) [Alteração] @endif Reserva de Recurso: <b>{{ $reserva->recurso->nome }}</b></h2>
        <p></p>
        <p>Solicitante: <b>{{ $reserva->title }}</b></p>
        <p>Início: <b>{{ date('d/m/Y H:i', strtotime($reserva->start .' GMT'. $timezone)) }}</b></p>
        <p>Fim: <b>{{ date('d/m/Y H:i', strtotime($reserva->end .' GMT'. $timezone)) }}</b></p>
        @if($reserva->observacao != NULL && $reserva->observacao != '')
        <p>Observação: <b>{!! $reserva->observacao !!}</b></p>
        @endif
        <p>Status: <b><span style="background-color: {{ $reserva->status->cor }}; padding: 3px">{{ $reserva->status->descricao }}</span></b>
        @if($reserva->justificativa != NULL && $reserva->justificativa != '')
            <br/>
            @if($reserva->status->chave == 'DEFERIDO' || $reserva->status->chave == 'CANCELADO')
                Comentário: <b> {{ $reserva->justificativa }}</b>
            @endif
            @if($reserva->status->chave == 'REJEITADO')
                Justificativa: <b><span style="color: {{ $reserva->status->cor }}">{{  $reserva->justificativa }}</span></b>
            @endif
        @endif
        </p>
        <p></p>
        <br/>
        <br/>
        <p style="color: gray;"><i>Este é um email automático enviado pelo sistema, não responda este email!</i> </p>
        <p></p>
        © Equipe EAD <br/>
    Contato: <a href="mailto:{{$email}}">{{$email}}</a></p>
    @if(config('app.debug'))
    {{var_dump($gestores)}}
    <button class="btn btn-secondary botao-barra" type="button" onclick="window.location.href = '/reservas'">Voltar</button>
    @endif
    </body>
</html>