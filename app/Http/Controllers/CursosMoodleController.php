<?php

namespace App\Http\Controllers;

use App\ServidorMoodle;
use App\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Crypt;
use PDO;
use PDOException;

class CursosMoodleController extends Controller
{

    private $crypt;
    public function __construct( Crypt $crypt)
    {
        $this->crypt = $crypt;
        $this->middleware('auth');
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function meusCursos()
    {
        return view("layouts.app-angular");
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function todosCursos()
    {
        return view("layouts.app-angular");
    }


    /**
     * Retorna os moodles do banco de dados do site.
     *
     * @param  array  $moodles_id       ID's de alguns moodles específicos, se estiver vazio retorna todos os moodles.
     * @return array                    Array com os registros dos moodles.
     */
    public function getMoodles(Request $request)
    {
        $request->has('idMoodles') ? $moodles = ServidorMoodle::whereIn('id', $request->get('idMoodles'))->get() : $moodles = ServidorMoodle::all();
        // $moodles = ServidorMoodle::where('id', 3)->get();
        return $moodles->toArray();
    }


    /**
     * Obtém os moodles com uma chave contendo os cursos do usuário
     *
     * @param  Usuario $usuario     Usuário para buscar os cursos.
     * @param  int    $ultimosMeses Parâmetro para especificar o intervalo de meses anterior ao momento atual para buscar os cursos.
     * @param  array  $idMoodles    Array para buscar por moodles específicos
     * @return array                Dados dos moodles com os cursos do usuário.
     */
    public function getMoodlesComCursos(Request $request, $ultimosMeses = false, $idMoodles = [])
    {

        $usuario = Auth::user();
        $request->has('ultimosMeses') ? $ultimosMeses = $request->get('ultimosMeses') : '';
        $request->has('idMoodles') ? $idMoodles = $request->get('idMoodles') : '';
        $moodles = $this->getMoodles($request);

        // obtém os cursos para cada moodle
        foreach ($moodles as $chave => $moodle) {
            $request->merge(['idMoodles' => [$moodles[$chave]['id']], 'idCurso' => 0 ]);
            $moodles[$chave]['href'] = $this->goMoodle($request);
            $moodles[$chave]['pos'] = $chave + 1;
            // adiciona os dados dos cursos junto com os dados do moodle
            $moodles[$chave]['cursos'] = $this->getCursosParaOMoodle($moodle, $usuario, $ultimosMeses);
            if(!empty($moodles[$chave]['cursos'])){
                $cursos = $moodles[$chave]['cursos'];
                foreach($cursos as $chaveCurso => $curso){
                    $request->merge(['idMoodles' => [$moodles[$chave]['id']], 'idCurso' => $moodles[$chave]['cursos'][$chaveCurso]['id']]);
                    $moodles[$chave]['cursos'][$chaveCurso]['href'] = $this->goMoodle($request);
                }
            }
            
        }

        return $moodles;
    }


    /**
     * Obtém os cursos do usuário do banco de dados do moodle.
     *
     * @param  array   $moodle          Dados do moodle.
     * @param  Usuario $usuario         Usuário para fazer a busca.
     * @param  int     $ultimosMeses    Parâmetro para especificar o intervalo de meses anterior ao momento atual para buscar os cursos.
     * @return array                    Cursos do usuário.
     */
    public function getCursosParaOMoodle(array $moodle, $usuario, $ultimosMeses = false)
    {

        // prefixo das tabelas dos moodles
        $prefixo = $moodle['prefixo'];

        $nomeDeUsuario = strtolower($usuario->email);


        // define um intervalo de meses para obter os cursos se o argumento $ultimosMeses for passado
        $sqlIntervalo = ''; // sql default
        if ($ultimosMeses) {
            $intervalo = $ultimosMeses . ' month';

            // concatena na query
            $sqlIntervalo .= " AND C.startdate >= (now() - interval '{$intervalo}')::abstime::int::bigint";
        }

        // SQL para buscar os cursos e disciplinas
        // sintaxe de string de múltiplas linhas
        // Mais informações aqui: http://php.net/manual/pt_BR/language.types.string.php#language.types.string.syntax.heredoc
        $sql = <<<EOT
                SELECT C.id, C.shortname, C.fullname, C.startdate, U.suspended
                FROM {$prefixo}_user U
                INNER JOIN {$prefixo}_role_assignments RS ON RS.userid=U.id
                INNER JOIN {$prefixo}_context E ON RS.contextid=E.id
                INNER JOIN {$prefixo}_course C ON C.id=E.instanceid
                WHERE E.contextlevel=50 AND U.username='{$nomeDeUsuario}' AND (C.visible = 1 OR ( C.visible = 0 AND RS.roleid != 5)) {$sqlIntervalo}
                group by C.id, C.shortname, C.fullname, U.suspended
                ORDER BY C.startdate DESC
                EOT;



        $sqlEAD = <<<EOT
                SELECT C.id, C.shortname, C.fullname, C.startdate, U.suspended
                FROM {$prefixo}_user U
                INNER JOIN {$prefixo}_role_assignments RS ON RS.userid=U.id
                INNER JOIN {$prefixo}_context E ON RS.contextid=E.id
                INNER JOIN {$prefixo}_course C ON C.id=E.instanceid
                WHERE E.contextlevel=50 AND U.username='{$nomeDeUsuario}' AND (C.visible = 1 OR ( C.visible = 0 AND RS.roleid != 5)) AND  C.enddate >= (now())::abstime::int::bigint
                group by C.id, C.shortname, C.fullname, U.suspended
                ORDER BY C.startdate DESC
                EOT;

        $sqlEAD = "SELECT C.id, C.shortname, C.fullname, C.startdate, U.suspended
                FROM " . $prefixo . "_user U
                INNER JOIN " . $prefixo . "_role_assignments RS ON RS.userid=U.id
                INNER JOIN " . $prefixo . "_context E ON RS.contextid=E.id
                INNER JOIN " . $prefixo . "_course C ON C.id=E.instanceid
                WHERE E.contextlevel=50 AND U.username='" . $nomeDeUsuario . "' AND (C.visible = 1 OR ( C.visible = 0 AND RS.roleid != 5)) AND  C.enddate >= (now())::abstime::int::bigint
                group by C.id, C.shortname, C.fullname, U.suspended
                ORDER BY C.startdate DESC";

        $sqlLocal = "SELECT C.id, C.shortname, C.fullname, C.startdate, U.suspended
                FROM {$prefixo}_user U
                INNER JOIN {$prefixo}_role_assignments RS ON RS.userid=U.id
                INNER JOIN {$prefixo}_context E ON RS.contextid=E.id
                INNER JOIN {$prefixo}_course C ON C.id=E.instanceid
                WHERE E.contextlevel=50 AND U.username='{$nomeDeUsuario}' AND (C.visible = 1 OR ( C.visible = 0 AND RS.roleid != 5))
                {$sqlIntervalo}
                group by C.id, C.shortname, C.fullname, U.suspended
                ORDER BY C.startdate DESC";

        // Executa a consulta e obtém o resultado com PDO
        // Mais informações sobre PDO aqui: http://php.net/manual/pt_BR/class.pdo.php
        // E aqui: http://www.diogomatheus.com.br/blog/php/trabalhando-com-pdo-no-php/
        $pdo = null;
        $cursos = array();
        try {
            $pdo = new PDO('pgsql:host=' . $moodle['ip_banco'] . ';dbname=' . $moodle['nome_banco'], 'portal_consulta', '121eadufgd387');
            
            if ($moodle['nome_banco'] === 'MoodleUabLibras2022') {
                $stmt = $pdo->prepare($sqlEAD);
            } elseif($moodle['nome_banco'] === 'moodle406' || $moodle['nome_banco'] === 'moodle311'){
                $stmt = $pdo->prepare($sqlLocal);
            } else $stmt = $pdo->prepare($sql);

            //  $stmt = $pdo->prepare($sql);
            $resultado = $stmt->execute();
            // se a consulta falhou
            if (!$resultado) {
                //  echo 'false';
                return false;
            }

            $cursos = $stmt->fetchAll();
            // echo '<pre>';
            //   var_dump ($cursos);

        } catch (PDOException $e) {
            //echo 'Ocorreu um erro ao obter seus cursos! Entre em contato com a equipe de TI através do e-mail: <b>ti.ead@ufgd.edu.br</b>';
            //echo $e->getMessage();
            //mostrarMensagemDeErro('Ocorreu um erro ao obter seus cursos! Entre em contato com a equipe de TI através do e-mail: <b>ti.ead@ufgd.edu.br</b>');
        }

        return $cursos;
    }


    public function goMoodle(Request $request)
    {

        $idCurso = 1;
        $idMoodle = $request->has('idMoodles') ? $request->get('idMoodles')  : false;
        $idCurso =  $request->has('idCurso') ? $request->get('idCurso')   : false;

        if ($idCurso == false) {
            $idCurso = 1;
        }

        $usuario = Auth::user();

        $username = (string) strtolower($usuario->email);
        $password = $request->session()->get('pass');
        // $password = (string) $usuario->password;
        // obtém os dados do moodle no banco do site
        $moodle = $this->getMoodles($request);
        $moodle = $moodle[0];

        if (!$moodle) { // moodle inválido
            abort(500,'Ocorreu um erro!');
        }

        //$username = 'CeHEt%2FR9i3SVdvwHRsS70qYWIcvR8bPuq6WQ8TMNpTg%3D';
        //$password='2YqjqUwrIwtgDlDSxV3%2BigyhYw%2BOCfcWUFOt%2BTwu4DE%3D';
        $today = getdate();
        $d = $today['mday'];
        $m = $today['mon'];
        $y = $today['year'];
        $lt = $today['0'];
        $v = $d + $m + $y;
        $v2 = $username . '&p=' . $password . '&p=' . $idCurso . '&p=0&p=' . $lt;
        
        $validacao = $this->crypt->encrypt($v2);
        
        //   $validacao= encrypte('teste');
        if ($moodle['ip_server'] == '200.129.209.222' || $moodle['ip_server'] == '200.129.209.243' || 
            $moodle['ip_server'] == '200.129.209.203' || $moodle['ip_server'] == '200.129.209.202' || 
            $moodle['ip_server'] == '200.129.209.241' || $moodle['ip_server'] == 'localhost:8082' ||
            $moodle['ip_server'] == 'localhost:8084') {
            $url = 'http://' . $moodle['ip_server'] . '/';
        } else {
            $url = 'https://' . $moodle['ip_server'] . '/';
        }
        $url .= 'login/loginportal.php?s=' . $validacao;
        //  $url .= 'login/loginportal.php?username='.$username.'&password=' .$password.'&curseid=' .$idCurso.'&s='.$validacao;
        // $url .= 'login/loginportal.php?username='.$username.'&password=' .$password.'&curseid=' .$idCurso;

        return($url);
    }
}
