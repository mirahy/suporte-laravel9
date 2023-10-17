<html>

<body>
    <h2 style="font-weight: normal;">Conta {{ $acao }}: <b>{{ $user['cn'][0] }}</b></h2>
    <p></p>
    <br />
    <p>Segue dados para acessar a sua conta!</p>
    <p>Login: <b>{{ $user['samaccountname'][0] }}</b></p>
    <p>Senha: <b>{{ $pass }}</b></p>
    <p></p>
    <p>Email institucional: <b>{{ $user['mail'][0] }}</b></p>
    <br />
    <br />
    Em caso de dúvidas, acesse nossa seção de tutoriais: <a
        href="https://portal.ead.ufgd.edu.br/tutoriais/">https://portal.ead.ufgd.edu.br/tutoriais/</a>.
    <p></p>
    <br />
    <br />
    <p style="color: gray;"><i>Este é um email automático enviado pelo sistema, não responda este email!</i> </p>
    <p></p>
    © Equipe EAD <br />
    Contato: <a href="mailto:{{ $email }}">{{ $email }}</a></p>
    <img src="img/assinaturaf.png">
    <br />
    <br />
    @if (config('app.debug'))
        {!! $ret !!}
    @endif
</body>

</html>
