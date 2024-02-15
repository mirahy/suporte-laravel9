<html>

<body>
    <h2 style="font-weight: normal;">Conta {{ $acao }}: <b>{{ $user['cn'][0] }}</b></h2>
    {{-- <p></p>
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
    <br /> --}}

    <b id="title">Seu cadastro foi realizado com sucesso!</b>

    <div>
        <p>
            Seu acesso a sala de aula <a
                href="https://portal.ead.ufgd.edu.br/"target="_blank">https://portal.ead.ufgd.edu.br/</a>
            e o Sistema Acadêmico SIGECAD <a href="https://ufgdnet.ufgd.edu.br/"
                target="_blank">https://ufgdnet.ufgd.edu.br/</a>
        </p>
        <p>
            Seu login será o seu CPF (sem pontos e traços) e a senha: <b>{{ $pass }}</b>
        </p>
        <p>
            Assim que entrar no sistema acadêmico faça a alteração da senha para que tenha acesso ao email acadêmico que
            é:
            <a href="mailto:{{ $user['mail'][0] }}" targ="_blank"><b>{{ $user['mail'][0] }}</b></a>

        </p>
    </div>
    <div>
        <ul>
            <li>
                Visite o nosso portal para se familiarizar com a Universidade e seu curso: Portal EaD:
                <a href="https://portal.ead.ufgd.edu.br/" target="_blank">https://portal.ead.ufgd.edu.br/</a>
            </li>
            <li>
                Secretaria Acadêmica:
                <a href="https://portal.ead.ufgd.edu.br/secaf/" target="_blank">https://portal.ead.ufgd.edu.br/secaf/</a>
            </li>
            <li>
                Dúvidas frequentes:
                <a href="https://portal.ead.ufgd.edu.br/duvidas/"
                    target="_blank">https://portal.ead.ufgd.edu.br/duvidas/</a>
            </li>
            <li>
                Portal da UFGD:
                <a href="https://portal.ufgd.edu.br/" target="_blank">https://portal.ufgd.edu.br/</a>
            </li>
        </ul>
    </div>
    <div>
        <ul>
            <li>
                Telefone e email para contato: 67 3410-2664 e
                <a href="mailto:secaf.ead@ufgd.edu.br" target="_blank">secaf.ead@ufgd.edu.br</a>
            </li>
            <li>
                Outros contatos da Faculdade de Educação a Distância - EaD:
                <a href="https://portal.ead.ufgd.edu.br/atendimento/"
                    target="_blank">https://portal.ead.ufgd.edu.br/atendimento/</a>
            </li>
        </ul>
        <div>
            <p>
                Aqui foi criada uma sala para informações sobre alguns procedimentos:
                <a href="https://moodle.ead.ufgd.edu.br/course/view.php?id=905"
                    target="_blank">https://moodle.ead.ufgd.edu.br/course/view.php?id=905</a>
            </p>
        </div>

        <div>
            <b>
                <span style="color:rgb(255,0,0)">IMPORTANTE:</span>
                As aulas estão previstas para começar no dia 18 de março de 2024.
            </b>

        </div>
    </div>
    <div>
        <p style="color: gray;"><i>Este é um email automático enviado pelo sistema, não responda este email!</i> </p>
        <p></p>
        © Equipe EAD <br />
        Contato: <a href="mailto: secaf.ead@ufgd.edu.br"> secaf.ead@ufgd.edu.br</a>
        <p></p>
        <img src="{{ asset('/img/secretaria_acad.png') }}" width="510" height="160">
        <br />
        <br />
    </div>




    {{-- @if (config('app.debug'))
        {!! $ret !!}
        @endif --}}
</body>

</html>
