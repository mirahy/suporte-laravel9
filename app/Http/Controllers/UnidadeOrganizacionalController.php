<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\UnidadeOrganizacional;
use App\User;
use Exception;
use Illuminate\Support\Facades\Validator;
use LdapRecord\Models\ActiveDirectory\User as UserLdap;
use LdapRecord\Models\ActiveDirectory\OrganizationalUnit;
use LdapRecord\Models\Attributes\AccountControl;
use LdapRecord\Models\ActiveDirectory\Group;
use App\Configuracoes;
use App\Mail\SendMailUserLdap;
use Illuminate\Http\Client\Request as ClientRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use LdapRecord\Connection;
use LdapRecord\Container;

class UnidadeOrganizacionalController extends Controller
{
    private $connection;
    private $crypt;
    private $trying = 0;

    public function __construct(Request $request, Crypt $crypt)
    {
        $this->middleware('auth');
        $this->middleware('authhost');
        $this->middleware('permissao:' . User::PERMISSAO_ADMINISTRADOR);
        $this->crypt = $crypt;
        
    }

    public function all()
    {
        return UnidadeOrganizacional::all();
    }

    private function adConnection(Request $request){

        $usuario = $usuario = Auth::user();
        $name = $usuario->name;
        
        $this->connection = new Connection([
                // Mandatory Configuration Options
                'hosts'            => [env('LDAP_USERKEEP_HOSTS')],
                'base_dn'          => env('LDAP_USERKEEP_BASE_DN'),
                'username'         => 'cn='.$name.','.env('OU_USER'),
                'password'         => $this->crypt->decrypt($request->session()->get('pass')),
            
                // Optional Configuration Options
                'port'             => 636,
                'use_ssl'          => true,
                'use_tls'          => false,
                'use_sasl'         => false,
                'timeout'          => 5,
                'follow_referrals' => false,
            
                // Custom LDAP Options
                'options' => [
                    // See: http://php.net/ldap_set_option
                    LDAP_OPT_X_TLS_REQUIRE_CERT => LDAP_OPT_X_TLS_NEVER
                ],

                // See: https://www.php.net/manual/en/function.ldap-sasl-bind.php
                'sasl_options' => [
                    'mech' => null,
                    'realm' => null,
                    'authc_id' => null,
                    'authz_id' => null,
                    'props' => null,
                ],
        ]);
    }

    public function getOuDirRoot(Request $request)
    {
        $ouRootBase = Configuracoes::where('nome', Configuracoes::CONFIGURACAO_OU_ROOT_DIR)->first();
        return $ouRootBase->valor;
    }

    public function setOuDirRoot(Request $request)
    {
        $ouRoot = $request->has('ou-dir-root') ? $request->input('ou-dir-root') : null;
        if (!$ouRoot)
            abort(403, "Erro de validação! Parâmetros inválidos!");
        $ouRootBase = Configuracoes::where('nome', Configuracoes::CONFIGURACAO_OU_ROOT_DIR)->first();
        $ouRootBase->valor = $ouRoot;
        $ouRootBase->save();
        return $ouRootBase->valor;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view("layouts.app-angular");
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    private function getValidationRules()
    {
        $rules = array(
            'nome'            => 'required',
            'valor'           => 'required'
        );
        return $rules;
    }

    public function getLdapUser(Request $request, $username)
    {
        $usuarioLdap = UserLdap::where('samaccountname', '=', $username)->get();
        if (!isset($usuarioLdap[0]))
            return false;
        return $usuarioLdap;
    }

    public function getLdapUserByEmail(Request $request, $email)
    {
        $usuarioLdap = UserLdap::where('mail', '=', $email)->get();
        if (!isset($usuarioLdap[0]))
            return false;
        return $usuarioLdap;
    }

    private function isCPF($value)
    {
        if (strlen($value) !== 11 || preg_match('/(\d)\1{10}/', $value)) {
            return false;
        }

        for ($t = 9; $t < 11; $t++) {
            for ($d = 0, $c = 0; $c < $t; $c++) {
                $d += $value[$c] * (($t + 1) - $c);
            }

            $d = ((10 * $d) % 11) % 10;

            if ($value[$c] != $d) {
                return false;
            }
        }

        return true;
    }

    private function tratarNome($nome)
    {
        $saida = "";

        $nome = mb_strtolower($nome, 'UTF-8'); // Converter o nome todo para minúsculo
        $nome = explode(" ", $nome); // Separa o nome por espaços

        for ($i = 0; $i < count($nome); $i++) {
            $array_de_tratamento = ["da", "e", "da", "dos", "do", "das"];
            if (in_array($nome[$i], $array_de_tratamento)) {
                $saida .= $nome[$i] . ' '; // Se a palavra estiver dentro das complementares mostrar toda em minúsculo
            } else {
                $saida .=  mb_convert_case($nome[$i], MB_CASE_TITLE, "UTF-8") . ' '; // Se for um nome, mostrar a primeira letra maiúscula
            }
        }
        return trim($saida);
    }

    private function quebraNome($nome)
    {
        for ($i = 0; $i < strlen($nome); $i++)
            if ($nome[$i] == ' ')
                return [substr($nome, 0, $i), substr($nome, $i + 1)];
        return [$nome, ""];
    }

    private function tirarAcentos($string)
    {
        return preg_replace(array("/(á|à|ã|â|ä)/", "/(Á|À|Ã|Â|Ä)/", "/(é|è|ê|ë)/", "/(É|È|Ê|Ë)/", "/(í|ì|î|ï)/", "/(Í|Ì|Î|Ï)/", "/(ó|ò|õ|ô|ö)/", "/(Ó|Ò|Õ|Ô|Ö)/", "/(ú|ù|û|ü)/", "/(Ú|Ù|Û|Ü)/", "/(ñ)/", "/(Ñ)/", "/(ç)/", "/(Ç)/"), explode(" ", "a A e E i I o O u U n N c C"), $string);
    }

    private function geraEmail($nome, $cpf, $sufixo)
    {

        $nome = str_replace("'", "", $nome);
        // verifica se existe espaço em branco e retira
        if(str_contains($nome, ""))
            $nome = trim($nome);    
        $fname = "";
        $lname = "";
        for ($i = 0; $i < strlen($nome); $i++)
            if ($nome[$i] == ' ') {
                $fname = mb_strtolower(substr($nome, 0, $i), 'UTF-8');
                break;
            }
        for ($i = strlen($nome) - 1; $i >= 0; $i--)
            if ($nome[$i] == ' ') {
                $lname = mb_strtolower(substr($nome, $i + 1, strlen($nome)), 'UTF-8');
                break;
            }
        return $this->tirarAcentos($fname) . '.' . $this->tirarAcentos($lname) . substr($cpf, 0, 3) . $sufixo;
    }

    /**
     * $user array [0]: username, [1]: email, [2]: nome, [3]?: senha
     * 
     */
    private function gerarAdUser($user, $company, $department, $userprincipalnameSufixo)
    {

        $nomeTratado = $user[2]; //$this->tratarNome($user[2]);
        $nomeQuebrado = $this->quebraNome($nomeTratado);
        $adUser = [
            'cn' => $nomeTratado,
            'instancetype' => 4,
            'samaccountname' => $user[0],
            'objectclass' => [
                'top', 'person', 'organizationalPerson', 'user'
            ],
            'displayname' => $nomeTratado,
            'name' => $nomeTratado,
            'givenname' => $nomeQuebrado[0],
            'sn' => $nomeQuebrado[1],
            'company' => $company,
            'department' => $department,
            'description' => substr($user[0], 0, 3) . "." . substr($user[0], 3, 3) . "." . substr($user[0], 6, 3) . "-" . substr($user[0], 9, 2),
            'mail' => $user[1],
            'userprincipalname' => $user[0] . "@" . $userprincipalnameSufixo
        ];
        return $adUser;
    }

    public function alterarSenha(Request $request)
    {
        $estudantes = $request->has('estudantes') ? json_decode($request->input('estudantes')) : null;
        $configEmail = Configuracoes::where('nome', Configuracoes::CONFIGURACAO_EMAIL_SUPORTE)->first();
        $configSeparadorEmail = Configuracoes::where('nome', Configuracoes::CONFIGURACAO_SEPARADOR_EMAIL)->first();

        if (!$estudantes)
            abort(403, "Erro de validação! Parâmetros inválidos!");

        $ret = "<h4><b>Alteração de Senha de Usuários no AD</b></h4><br>";

        $ret .= "<br><b>Iniciando processo...</b><br><br>";

        $ret .= '<table class="tabela-relatorio"><thead><tr>'
            . '<th>Username</th>'
            . '<th>Nome</th>'
            . '<th>Nova Senha</th>'
            . '<th>Processamento</th>'
            . '</tr></thead><tbody>';
        foreach ($estudantes as $e) {
            try {
                $pass = $this->generateStrongPassword();
                $user = UserLdap::where('samaccountname', '=', $e[0])->get();
                $user = $user[0];

                if (!$user) {
                    $ret .= "<tr><td>" . $e[0] . '</td><td></td><td>'  . $pass . '</td><td><span style="color: #e9d700;">Usuário não encontrado</span>';
                    continue;
                }

                $nomeUser = $user->getAttributes()['name'][0];
                $ret .= "<tr><td>" . $e[0] . '</td><td>' . $nomeUser . "</td><td>" . $pass . "</td><td>";

                $user->unicodePwd = $pass;

                $user->save();

                $ret .= "<span style=\"color: #1bb300;\">Senha alterada!</span>";

                if (config('app.debug')) {
                    return view('email.emailLdap', ['user' => $user->getAttributes(),
                                                    'pass' => $pass,
                                                    'email' => ($configEmail == NULL ? "" : $configEmail->valor ),
                                                    'ret' => $ret,
                                                    'acao' => 'alterada']);
                }
                else {
                    Mail::to(array_map('trim', explode($configSeparadorEmail, $e[3])))
                        ->cc($configEmail != null ? array_map('trim', explode($configSeparadorEmail, $configEmail->valor)) : "")
                        ->send(new SendMailUserLdap($user, $pass));
        
                    return $ret;
                }

            } catch (Exception $ex) {
                $ret .= '<span style="color: #ff0000;"> Erro: ' . $ex->getMessage() . ' </span>';
            }
            $ret .= "</td></tr>";
        }
        $ret .= "</tbody></table>";

        $ret .= "<br><br><b>Alteração de usuários no AD Finalizada!</b>";

        

        return $ret;
    }

    public function substituiEmailsPorPadrao(Request $request)
    {

        $sufixo = Configuracoes::where('nome', Configuracoes::CONFIGURACAO_AD_EMAIL_PADRAO_SUFIXO)->first()->valor;

        $estudantes = $request->has('estudantes') ? json_decode($request->input('estudantes')) : null;
        if (!$estudantes)
            abort(403, "Erro de validação! Parâmetros inválidos!");
        $estRet = [];
        foreach ($estudantes as $e) {
            $estRet[] = (object) [
                'username' => $e[0],
                'fullname' => $e[2],
                'email' => ($this->isCPF($e[0]) ? $this->geraEmail($e[2], $e[0], "@" . $sufixo) : $e[1]),
                'senha' => $e[3],
                'is_professor' => false
            ];
        }
        return $estRet;
    }

    public function getOusFilhas()
    {
        $ouRootBase = Configuracoes::where('nome', Configuracoes::CONFIGURACAO_OU_ROOT_DIR)->first();
        $us = OrganizationalUnit::in($ouRootBase->valor)->get();
        $ous = [];
        foreach ($us as $uu) {
            $ous[] = $uu->getAttributes()['distinguishedname'][0];
        }
        return $ous;
    }

    public function criarContasAD(Request $request)
    {
        $this->trying++;
        try {
            $this->adConnection($request);

            $this->connection->connect();

           if (!Container::hasConnection('default')) {
                Container::addConnection('default');
            }

            if(!Container::hasConnection('keepuser')) {
                Container::addConnection($this->connection, 'keepuser');
            }

            Container::setDefaultConnection('keepuser');


        
            $ouCadastro = $request->has('ouCadastro') ? $request->input('ouCadastro') : null;
            $ousIds = $request->has('ous') ? $request->input('ous') : null;
            $estudantes = $request->has('estudantes') ? json_decode($request->input('estudantes')) : null;
            // $senhaPadrao = $request->has('senhaPadrao') ? $request->input('senhaPadrao') : null;
    
            $company = Configuracoes::where('nome', Configuracoes::CONFIGURACAO_AD_COMPANY)->first()->valor;
            $department = Configuracoes::where('nome', Configuracoes::CONFIGURACAO_AD_DEPARTMENT)->first()->valor;
            $userprincipalnameSufixo = Configuracoes::where('nome', Configuracoes::CONFIGURACAO_AD_USER_PRINCIPAL_NAME_SUFIXO)->first()->valor;
            $configEmail = Configuracoes::where('nome', Configuracoes::CONFIGURACAO_EMAIL_SUPORTE)->first();
            $configSeparadorEmail = Configuracoes::where('nome', Configuracoes::CONFIGURACAO_SEPARADOR_EMAIL)->first();
    
            if (!$ouCadastro || !$ousIds || !$estudantes)
                abort(403, "Erro de validação! Parâmetros inválidos!");
    
            $ous = UnidadeOrganizacional::whereIn('id', $ousIds)->get();
    
            $ret = "<h4><b>Criação de Usuários no AD</b></h4><br>";
    
            $ret .= "<br><b>Diretório de Criação:</b><br>" . $ouCadastro . "<br>";
    
            $ret .= "<br><b>Grupos selecionados (Membro de):</b>";

            $ret .= "<br><b>Tentativas de conexão: ". $this->trying."</b>";
            $memberof = [];
            foreach ($ous as $ou) {
                $ret .= "<br>" . $ou->nome;
                $memberof[] = $ou->valor;
            }
    
            $ret .= "<br><b>Iniciando processo de criação de usuários no AD...</b><br><br>";
    
            $ret .= '<table class="tabela-relatorio"><thead><tr>'
                . '<th>Username</th>'
                . '<th>Nome</th>'
                . '<th>Processamento</th>'
                . '</tr></thead><tbody>';
            foreach ($estudantes as $e) {
                $pass = $this->generateStrongPassword();
                $e[2] = $this->tratarNome($e[2]);
                $ret .= "<tr><td>" . $e[0] . "</td><td>" . $e[2] . "</td><td>";
                if (!$this->isCPF($e[0])) {
                    $ret .= '<span style="color: #e9d700;">CPF Inválido para username </span>';
                    continue;
                }
               
                if ($ldapuser = $this->getLdapUser($request, $e[0])) {
                    $distinguishedname = $ldapuser[0]['distinguishedname'][0];
                    $ret .= '<span style="color: #e9d700;" title="' . $distinguishedname . '">
                    ' . (stripos($distinguishedname, $ouCadastro) !== FALSE ? 'Cadastrado anteriormente' : 'Usuário já existente (*)') . '
                    </span>';
                    continue;
                }
                if ($ldapuser = $this->getLdapUserByEmail($request, $e[1])) {
                    $distinguishedname = $ldapuser[0]['distinguishedname'][0];
                    $ret .= '<span style="color: #e9d700;" title="' . $distinguishedname . '">Email já utilizado (*)</span>';
                    continue;
                }
                try {
                    $adUserAbstr = $this->gerarAdUser($e, $company, $department, $userprincipalnameSufixo);
    
                    $user = (new UserLdap($adUserAbstr))->inside($ouCadastro)->setConnection('keepuser');
    
                    $user->unicodePwd = $pass;
                    
                    $user->save();
                    $user->refresh();
                    
                    foreach ($memberof as $grupo) {
                        if($group = Group::findOrFail($grupo))
                           $user->groups()->attach($group);
                    }
    
                    $user->userAccountControl = (AccountControl::NORMAL_ACCOUNT + AccountControl::DONT_EXPIRE_PASSWORD) ; // Normal, enabled account.
                    
                    $user->save();
    
                    $ret .= "<span style=\"color: #1bb300;\">Usuário criado!</span>";
    
                    if (config('app.debug')) {
                        return view('email.emailLdap', ['user' => $user->getAttributes(),
                                                        'pass' => $pass,
                                                        'email' => ($configEmail == NULL ? "" : $configEmail->valor ),
                                                        'ret' => $ret,
                                                        'acao' => 'criada']);
                    }
                    else {
                        Mail::to(array_map('trim', explode($configSeparadorEmail, $e[3])))
                            ->cc($configEmail != null ? array_map('trim', explode($configSeparadorEmail, $configEmail->valor)) : "")
                            ->send(new SendMailUserLdap($user, $pass));
            
                    }
    
                } catch (Exception $ex) {
                    $ret .= '<span style="color: #ff0000;"> Erro: ' . $ex->getMessage() . ' </span>';
                }
                $ret .= "</td></tr>";
            }
            $ret .= "</tbody></table>";
            $ret .= "<br>*: passe o mouse sobre o item para ver mais detalhes";
    
            $ret .= "<br><br><b>Criação de usuários no AD Finalizada!</b>";
            
            Container::setDefaultConnection('default');

            return $ret;

        } catch (\LdapRecord\Auth\BindException $e) {
            if($this->trying < 3){
                sleep(5);
                $this->criarContasAD($request);
            }else{
                $this->trying = 0;
                $error = $e->getDetailedError();
                $message = $error->getErrorCode().' || '. $error->getErrorMessage(). '|| '. $error->getDiagnosticMessage();
                abort(500, $message);
            }
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), $this->getValidationRules());
        if ($validator->fails())
            abort(403, 'Erro de Validação');

        $ou = new UnidadeOrganizacional();
        $ou->nome = $request->input('nome');
        $ou->valor = $request->input('valor');
        $ou->save();
        return $ou;
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return UnidadeOrganizacional::find($id);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $ou = UnidadeOrganizacional::find($id);
        if (!$ou)
            abort(404, 'Unidade Organizacional não encontrada');
        $validator = Validator::make($request->all(), $this->getValidationRules());
        if ($validator->fails())
            abort(403, 'Erro de Validação');
        $ou->nome = $request->input('nome');
        $ou->valor = $request->input('valor');
        $ou->save();
        return $ou;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $ou = UnidadeOrganizacional::find($id);
        if (!$ou)
            abort(404, 'Unidade Organizacional não encontrada');
        try {
            $ou->delete();
            return new UnidadeOrganizacional();
        } catch (Exception $e) {
            abort(400, $e->getMessage());
        }
    }

    // Generates a strong password of N length containing at least one lower case letter,
    // one uppercase letter, one digit, and one special character. The remaining characters
    // in the password are chosen at random from those four sets.
    //
    // The available characters in each set are user friendly - there are no ambiguous
    // characters such as i, l, 1, o, 0, etc. This, coupled with the $add_dashes option,
    // makes it much easier for users to manually type or speak their passwords.
    //
    // Note: the $add_dashes option will increase the length of the password by
    // floor(sqrt(N)) characters.

    function generateStrongPassword($length = 12, $add_dashes = false, $available_sets = 'luds')
    {
        $sets = array();
        if (strpos($available_sets, 'l') !== false)
            $sets[] = 'abcdefghjkmnpqrstuvwxyz';
        if (strpos($available_sets, 'u') !== false)
            $sets[] = 'ABCDEFGHJKMNPQRSTUVWXYZ';
        if (strpos($available_sets, 'd') !== false)
            $sets[] = '23456789';
        if (strpos($available_sets, 's') !== false)
            $sets[] = '!@#$%&*?';

        $all = '';
        $password = '';
        foreach ($sets as $set) {
            $password .= $set[array_rand(str_split($set))];
            $all .= $set;
        }

        $all = str_split($all);
        for ($i = 0; $i < $length - count($sets); $i++)
            $password .= $all[array_rand($all)];

        $password = str_shuffle($password);

        if (!$add_dashes)
            return $password;

        $dash_len = floor(sqrt($length));
        $dash_str = '';
        while (strlen($password) > $dash_len) {
            $dash_str .= substr($password, 0, $dash_len) . '-';
            $password = substr($password, $dash_len);
        }
        $dash_str .= $password;
        return $dash_str;
    }
}
