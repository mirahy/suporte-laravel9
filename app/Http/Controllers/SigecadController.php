<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;

class SigecadController extends Controller
{
    const DBCONN_DISCIPLINAS_VIEW = "secretaria.disciplinas_para_moodle";
    const DBCONN_ACADEMICOS_VIEW = "secretaria.disciplinas_academicos_para_moodle";
    const DBCONN_CARTOES_VIEW = "secretaria.cartoes_para_moodle";
    const DBCONN_PESSOAS_VIEW = "public.pessoas_view";

    const DBCONN_KEYFIELD = "username";

    const NOME_QUERY = "query_sigecad";

    const COLUNA_SIGLA_FACULDADE = "sigla_faculdade";
    const COLUNA_NOME_FACULDADE = "nome_faculdade";
    const COLUNA_NOME_CURSO = "nome_curso";
    const COLUNA_NOME_ALUNO = "nome_aluno";
    const COLUNA_NOME_DISCIPLINA = "nome_disciplina";
    const COLUNA_CODIGO_CURSO = "codigo_curso";
    const COLUNA_CODIGO_DISCIPLINA = "codigo_disciplina";
    const COLUNA_USERNAME_ALUNO = "username_aluno";
    const COLUNA_EMAIL_ALUNO = "email_do_aluno";
    const COLUNA_PERIODO_LETIVO_ID = "periodo_letivo_id";
    const COLUNA_PERIODO_LETIVO = "periodo_letivo";
    const COLUNA_CARGA_HORARIA_DISCIPMINA = "carga_horaria_total_disciplina";
    const COLUNA_NOME_PROFESSOR = "nome_professor";
    const COLUNA_USERNAME_PROFESSOR = "username_professor";
    const COLUNA_CPF_PROFESSOR = "cpf_professor";
    const COLUNA_AVALIACAO = "avaliacao";
    const COLUNA_TURMA_ID = "turma_id";
    const COLUNA_TURMA_NOME = "turma_nome";
    const COLUNA_GRUPO = "grupo";

    const COLUNA_BUSCA_CARTAO = "cpf";
    const COLUNA_NUMERO_CARTAO = "numero_cartao";
    const COLUNA_ESTADO_CARTAO = "estado_cartao";
    const COLUNA_ESTATUS_PESSOA_CARTAO = "tipo_estatus_nome";

    const COLUNA_NOME_PESSOA = "nome";
    const COLUNA_DOCUMENTO_PESSOA = "documento";
    const COLUNA_LOGIN_PESSOA = "login";
    const COLUNA_TIPO_ESTATUS_PESSOA = "tipo_estatus_nome";
    const COLUNA_TIPO_PESSOA = "tipo_pessoa";
    const COLUNA_ATIVO_PESSOA = "ativo";
    const COLUNA_EMAIL_PESSOA = "email";
    const COLUNA_EMAIL_ALTERNATIVO_PESSOA = "email_alternativo";
    const COLUNA_LOTACAO_PESSOA = "lotacao";
    const COLUNA_FACULDADE_PESSOA = "faculdade";

    private function executarGeneric($view, $parametros, $colunas = [], $nativeQuery = null) {
        $dbconn = pg_connect("host=".env('DB_ACAD_HOSTNAME').
        " port=".env('DB_ACAD_PORT').
        " dbname=".env('DB_ACAD_DBNAME').
        " user=".env('DB_ACAD_USERNAME').
        " password=".env('DB_ACAD_PASSWORD'));

        if ($nativeQuery) {
            $result = pg_prepare($dbconn, self::NOME_QUERY, $nativeQuery);
        }
        else {
            $cols = "";
            $first = true;
            foreach ($colunas as $nomeCol => $rename) {
                $cols .= ($first ? ' ' : ', ') . $nomeCol .' '. $rename;
                $first = false;
            }
            if(!$cols)
                $cols = " *";
            if (is_array($parametros)) {
                if (count($parametros) > 0 && array_keys($parametros)[0] == '0' ){
                    $vars = "($1";
                    for ($i = 2; $i <= count( $parametros ); $i++) {
                        $vars .= ",$".$i;
                    }
                    $vars .= ")";
                    $result = pg_prepare($dbconn, self::NOME_QUERY, 'SELECT'.$cols.' FROM '.$view.' WHERE '.self::DBCONN_KEYFIELD.' IN '.$vars);
                }
                else {
                    $query = 'SELECT'.$cols.' FROM '.$view;
                    $first = true;
                    $index = 1;
                    $parametrosReindex = [];
                    foreach ($parametros as $field => $value) {
                        if ($field == self::COLUNA_USERNAME_PROFESSOR || $field == self::COLUNA_NOME_PROFESSOR)
                            $query .= ($first ? ' WHERE ': ' AND '). 'unaccent('.$field .') ilike unaccent($'.$index.')';
                        elseif ($field == self::COLUNA_LOTACAO_PESSOA || $field == self::COLUNA_FACULDADE_PESSOA  || $field == self::COLUNA_TIPO_ESTATUS_PESSOA){
                            if ($field == self::COLUNA_LOTACAO_PESSOA && $parametros[self::COLUNA_TIPO_PESSOA] == 'funcionario') {
                                $val1 = "%/".$value." - %";
                                $val2 = $value." - %";
                                $query .= ($first ? ' WHERE ': ' AND '). '(TRIM('.$field .') ilike $'.$index.' OR TRIM('.$field .') ilike $'.($index+1).')';
                                $parametrosReindex[] = $val1;
                                $index++;
                                $value = $val2;
                            }
                            else
                                $query .= ($first ? ' WHERE ': ' AND '). 'TRIM('.$field .') = TRIM($'.$index.')';
                        }
                        else 
                            $query .= ($first ? ' WHERE ': ' AND '). $field .' =  $'.$index;
                        $parametrosReindex[] = $value;
                        $index++;
                        $first = false;
                    }
                    if ($view == self::DBCONN_ACADEMICOS_VIEW)
                        $query .= ' ORDER BY '.self::COLUNA_NOME_ALUNO;
                    elseif ($view == self::DBCONN_CARTOES_VIEW)
                        $query .= ' ORDER BY '.self::COLUNA_ESTADO_CARTAO.','.self::COLUNA_ESTATUS_PESSOA_CARTAO;
                    elseif ($view == self::DBCONN_PESSOAS_VIEW)
                        $query .= ' ORDER BY '.self::COLUNA_NOME_PESSOA.','.self::COLUNA_TIPO_ESTATUS_PESSOA;
                    //return $query;
                    $result = pg_prepare($dbconn, self::NOME_QUERY, $query);
                    $parametros = $parametrosReindex;
                }
            }
            else {
                $result = pg_prepare($dbconn, self::NOME_QUERY, 'SELECT'.$cols.' FROM '.$view.' WHERE '.self::DBCONN_KEYFIELD.' = $1');
            }
        }
        
        $result = pg_execute($dbconn, self::NOME_QUERY, $parametros);

        $arr = pg_fetch_all($result);

        pg_close($dbconn);

        return $arr ? $arr : [];
    }

    private function executarDisciplinas($parametros, $colunas = [], $nativeQuery = null) {
        return $this->executarGeneric(self::DBCONN_DISCIPLINAS_VIEW, $parametros, $colunas, $nativeQuery);
    }

    private function executarEstudantes($parametros, $colunas = [], $nativeQuery = null) {
        return $this->executarGeneric(self::DBCONN_ACADEMICOS_VIEW, $parametros, $colunas, $nativeQuery);
    }

    private function executarPessoas($parametros, $colunas = [], $nativeQuery = null) {
        return $this->executarGeneric(self::DBCONN_PESSOAS_VIEW, $parametros, $colunas, $nativeQuery);
    }

    public function consulta(Request $request){

        return $this->executarDisciplinas([]);
        //ob_start();
        //var_dump($arr);
        //return ob_get_clean();

        return [];
    }

    // Obtêm todos os período letivos correntes
    public function getPeriodoLetivoList(Request $request){
        return $this->executarDisciplinas([], [], "SELECT ".self::COLUNA_PERIODO_LETIVO_ID." id_sigecad, ".self::COLUNA_PERIODO_LETIVO." nome FROM ".self::DBCONN_DISCIPLINAS_VIEW." GROUP BY ".self::COLUNA_PERIODO_LETIVO_ID.", ".self::COLUNA_PERIODO_LETIVO);
        return '[{"id_sigecad":1,"nome":"2020-1"},{"id_sigecad":2,"nome":"2020-2"},{"id_sigecad":3,"nome":"2020-3"}]';
    }

    // Obtêm todas as faculdades e os cursos de um período letivo 
    public function getCursosList(Request $request, $periodoLetivoKey){
        if (is_string($periodoLetivoKey)) 
            return $this->executarDisciplinas([$periodoLetivoKey], [], "SELECT ".self::COLUNA_SIGLA_FACULDADE.", ".self::COLUNA_NOME_FACULDADE.", ".self::COLUNA_NOME_CURSO.", ".self::COLUNA_CODIGO_CURSO." FROM ".self::DBCONN_DISCIPLINAS_VIEW." WHERE ".self::COLUNA_PERIODO_LETIVO." = $1 GROUP BY  ".self::COLUNA_CODIGO_CURSO.", ".self::COLUNA_NOME_CURSO.", ".self::COLUNA_SIGLA_FACULDADE.", ".self::COLUNA_NOME_FACULDADE);
        else 
            return $this->executarDisciplinas([$periodoLetivoKey], [], "SELECT ".self::COLUNA_SIGLA_FACULDADE.", ".self::COLUNA_NOME_FACULDADE.", ".self::COLUNA_NOME_CURSO.", ".self::COLUNA_CODIGO_CURSO." FROM ".self::DBCONN_DISCIPLINAS_VIEW." WHERE ".self::COLUNA_PERIODO_LETIVO_ID." = $1 GROUP BY  ".self::COLUNA_CODIGO_CURSO.", ".self::COLUNA_NOME_CURSO.", ".self::COLUNA_SIGLA_FACULDADE.", ".self::COLUNA_NOME_FACULDADE);  
    }

    public function getDisciplinasList(Request $request, $siglaFaculdade, $periodoLetivoKey){
        if (is_string($periodoLetivoKey)) 
            return $this->executarDisciplinas([self::COLUNA_SIGLA_FACULDADE => $siglaFaculdade, self::COLUNA_PERIODO_LETIVO => $periodoLetivoKey]);
        else 
            return $this->executarDisciplinas([self::COLUNA_SIGLA_FACULDADE => $siglaFaculdade, self::COLUNA_PERIODO_LETIVO_ID => $periodoLetivoKey]);
    }

    public function getDisciplinasCursoList(Request $request, $siglaFaculdade, $codigoCurso, $periodoLetivoKey = 0){
        if ($periodoLetivoKey) {
            if (is_string($periodoLetivoKey)) 
                return $this->executarDisciplinas([self::COLUNA_SIGLA_FACULDADE => $siglaFaculdade, self::COLUNA_CODIGO_CURSO => $codigoCurso, self::COLUNA_PERIODO_LETIVO => $periodoLetivoKey]);
            else 
                return $this->executarDisciplinas([self::COLUNA_SIGLA_FACULDADE => $siglaFaculdade, self::COLUNA_CODIGO_CURSO => $codigoCurso, self::COLUNA_PERIODO_LETIVO_ID => $periodoLetivoKey]);
            
        }
            
        return $this->executarDisciplinas([self::COLUNA_SIGLA_FACULDADE => $siglaFaculdade, self::COLUNA_CODIGO_CURSO => $codigoCurso]);
    }
    public function getDisciplinasCursoNomeList(Request $request, $siglaFaculdade, $nomeCurso, $periodoLetivoKey = 0){
        if ($periodoLetivoKey)
            return $this->executarDisciplinas([self::COLUNA_SIGLA_FACULDADE => $siglaFaculdade, self::COLUNA_NOME_CURSO => $nomeCurso, self::COLUNA_PERIODO_LETIVO_ID => $periodoLetivoKey]);
        return $this->executarDisciplinas([self::COLUNA_SIGLA_FACULDADE => $siglaFaculdade, self::COLUNA_NOME_CURSO => $nomeCurso]);
    }
    public function getAcademicosDisciplina(Request $request, $codigoDisciplina, $periodoLetivoKey, $turmaId, $turmaNome, $professor = null){
        $wheres = [
            self::COLUNA_CODIGO_DISCIPLINA => $codigoDisciplina, 
            (is_string($periodoLetivoKey) ? self::COLUNA_PERIODO_LETIVO : self::COLUNA_PERIODO_LETIVO_ID) => $periodoLetivoKey, 
            self::COLUNA_TURMA_ID => $turmaId, 
            self::COLUNA_TURMA_NOME => $turmaNome
        ];
        $estudantes = [];
        if ($professor) {
            $wheres[self::COLUNA_USERNAME_PROFESSOR] = $professor->email;
            $estudantes = $this->executarEstudantes($wheres,[self::COLUNA_USERNAME_ALUNO => 'username', self::COLUNA_NOME_ALUNO => 'fullname', self::COLUNA_EMAIL_ALUNO => 'email']);
            if (empty($estudantes)) {
                unset( $wheres[self::COLUNA_USERNAME_PROFESSOR] );
                $wheres[self::COLUNA_NOME_PROFESSOR] = $professor->name;
                $estudantes = $this->executarEstudantes($wheres,[self::COLUNA_USERNAME_ALUNO => 'username', self::COLUNA_NOME_ALUNO => 'fullname', self::COLUNA_EMAIL_ALUNO => 'email']);
                if (empty($estudantes)) {
                    unset( $wheres[self::COLUNA_NOME_PROFESSOR] );
                    $estudantes = $this->executarEstudantes($wheres,[self::COLUNA_USERNAME_ALUNO => 'username', self::COLUNA_NOME_ALUNO => 'fullname', self::COLUNA_EMAIL_ALUNO => 'email']);
                }
            }
        }
        else {
            $estudantes = $this->executarEstudantes($wheres,[self::COLUNA_USERNAME_ALUNO => 'username', self::COLUNA_NOME_ALUNO => 'fullname', self::COLUNA_EMAIL_ALUNO => 'email']);
        }
        return $estudantes;
    }
    public function chargeDisciplina (Request $request, $periodoLetivoKey, $codigoCurso, $codigoDisciplina, $salaTurmaNome, $professorUsername) {
        //abort(412,$professorUsername);
        $wheres = [
            self::COLUNA_PERIODO_LETIVO_ID => $periodoLetivoKey,
            self::COLUNA_CODIGO_CURSO => $codigoCurso,
            self::COLUNA_CODIGO_DISCIPLINA => $codigoDisciplina,
            self::COLUNA_TURMA_NOME => $salaTurmaNome,
        ];
        if ($professorUsername !== false) {
            $wheres[self::COLUNA_USERNAME_PROFESSOR] = $professorUsername;
        }
        $ret = $this->executarDisciplinas($wheres);
        if ($ret)
            return $ret[0];
        return "";
    }
    public function formataPadraoSaidaEstudantes ($estudantes) {
        $estFormat = [];
        foreach ($estudantes as $e) {
            $estudante = [];
            $estudante[] = $e['username'];
            $estudante[] = $e['email'];
            $estudante[] = $e['fullname'];
            $estFormat[] = $estudante;
        }
        return $estFormat;
    }

    public function consultaDadosCartao($documento) {
        $result = $this->executarGeneric(self::DBCONN_CARTOES_VIEW, [self::COLUNA_BUSCA_CARTAO => $documento]);
        if ($result) {
            return $result[0][self::COLUNA_NUMERO_CARTAO];
        }
        return '';
    }

    public function consultaPessoasVotacao(Request $request, $estatus, $tipo_pessoa, $lotacao, $faculdade = null) {
        $wheres = [
            self::COLUNA_ATIVO_PESSOA => true,
            self::COLUNA_TIPO_ESTATUS_PESSOA => $estatus,
            self::COLUNA_TIPO_PESSOA => $tipo_pessoa,
        ];
        if ($lotacao) {
            $wheres[self::COLUNA_LOTACAO_PESSOA] = $lotacao;
        }
        if ($faculdade) {
            $wheres[self::COLUNA_FACULDADE_PESSOA] = $faculdade;
        }
        $result = $this->executarPessoas($wheres, [
            self::COLUNA_NOME_PESSOA => 'nome', 
            self::COLUNA_DOCUMENTO_PESSOA => 'cpf',
            //self::COLUNA_TIPO_ESTATUS_PESSOA => 'estatus',
            //self::COLUNA_TIPO_PESSOA => 'tipo_pessoa',
            //self::COLUNA_ATIVO_PESSOA => 'ativo',
            self::COLUNA_LOGIN_PESSOA => 'login',
            self::COLUNA_EMAIL_PESSOA => 'email',
            self::COLUNA_EMAIL_ALTERNATIVO_PESSOA => 'email_alternativo',
            self::COLUNA_LOTACAO_PESSOA => 'lotacao',
        ]);
        if ($result) {
            return $result;
        }
        return '';
    }

    public function getEstatusTipoPessoaList(Request $request){
        return $this->executarPessoas([], [], "SELECT DISTINCT ".self::COLUNA_TIPO_ESTATUS_PESSOA." estatus, ".self::COLUNA_TIPO_PESSOA." tipo_pessoa FROM ".self::DBCONN_PESSOAS_VIEW." WHERE ".self::COLUNA_ATIVO_PESSOA." = true AND NOT (".self::COLUNA_TIPO_ESTATUS_PESSOA."='Tecnicos' AND ".self::COLUNA_TIPO_PESSOA."='academico_graduacao') ORDER BY ".self::COLUNA_TIPO_PESSOA.", ".self::COLUNA_TIPO_ESTATUS_PESSOA);
    }
    public function getLotacoesPessoaList(Request $request, $estatus){
        return $this->executarPessoas([$estatus], [], 
        "SELECT DISTINCT ".self::COLUNA_LOTACAO_PESSOA." FROM ".self::DBCONN_PESSOAS_VIEW." WHERE ".self::COLUNA_ATIVO_PESSOA." = true AND TRIM(".self::COLUNA_TIPO_ESTATUS_PESSOA.") = TRIM($1) ORDER BY ".self::COLUNA_LOTACAO_PESSOA);
    }
    public function getLotacoesPessoaFullList(Request $request){
        return $this->executarPessoas([], [], 
        "SELECT DISTINCT ".self::COLUNA_LOTACAO_PESSOA." FROM ".self::DBCONN_PESSOAS_VIEW." WHERE ".self::COLUNA_ATIVO_PESSOA." = true AND ".self::COLUNA_TIPO_PESSOA."='funcionario' ORDER BY ".self::COLUNA_LOTACAO_PESSOA);
    }
    public function getCursosFaculdadePessoaList(Request $request, $estatus, $faculdade){
        return $this->executarPessoas([$faculdade, $estatus], [], 
        "SELECT DISTINCT ".self::COLUNA_LOTACAO_PESSOA." FROM ".self::DBCONN_PESSOAS_VIEW." WHERE ".self::COLUNA_ATIVO_PESSOA." = true AND TRIM(".self::COLUNA_FACULDADE_PESSOA.") = TRIM($1) AND TRIM(".self::COLUNA_TIPO_ESTATUS_PESSOA.") = TRIM($2) ORDER BY ".self::COLUNA_LOTACAO_PESSOA);
    }
    public function getFaculdadePessoaList(Request $request, $estatus){
        return $this->executarPessoas([$estatus], [], 
        "SELECT DISTINCT ".self::COLUNA_FACULDADE_PESSOA." FROM ".self::DBCONN_PESSOAS_VIEW." WHERE ".self::COLUNA_ATIVO_PESSOA." = true AND TRIM(".self::COLUNA_TIPO_ESTATUS_PESSOA.") = TRIM($1) ORDER BY ".self::COLUNA_FACULDADE_PESSOA);
    }
}
