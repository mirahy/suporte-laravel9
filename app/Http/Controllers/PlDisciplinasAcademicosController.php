<?php

namespace App\Http\Controllers;

use App\Configuracoes;
use App\Curso;
use App\Faculdade;
use App\Macro;
use App\PeriodoLetivo;
use App\PlDisciplinaAcademico;
use App\Sala;
use App\SuperMacro;
use App\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Validator;

class PlDisciplinasAcademicosController extends Controller
{
    const NUM_ELEMENTOS_CSV = 9;
    const INDEX_SIGLA_FACULDADE = 0;
    const INDEX_NOME_FACULDADE = 1;
    const INDEX_NOME_CURSO = 2;
    const INDEX_NOME_ESTUDANTE = 3;
    const INDEX_NOME_DISCIPLINA = 4;
    const INDEX_CODIGO_CURSO = 5;
    const INDEX_CODIGO_DISCIPLINA = 6;
    const INDEX_USERNAME = 7;
    const INDEX_EMAIL = 8;

    
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permissao:'.User::PERMISSAO_ADMINISTRADOR.','.User::PERMISSAO_USUARIO);
        $this->middleware('permissao:'.User::PERMISSAO_ADMINISTRADOR)->except(['find']);
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

    public function toView($pldaList) {
        
    /*avaliacao: "nota"
    carga_horaria_total_disciplina: "72.00"
    codigo_curso: "0635V"
    codigo_disciplina: "06100004473"
    nome_curso: "QUÍMICA - LICENCIATURA"
    nome_disciplina: "MINERALOGIA"
    nome_faculdade: "FACULDADE DE CIÊNCIAS EXATAS E TECNOLOGIA"
    nome_professor: "JOSE DANIEL DE FREITAS FILHO"
    periodo_letivo: "2020 - 4"
    periodo_letivo_id: "132"
    sigla_faculdade: "FACET"
    username_professor: "JoseFilho"
    
    id: number;
    curso:Curso|number;
    periodo_letivo:PeriodoLetivo|number;
    disciplina:string;
    estudantes:string|Array<Estudante> = [];
    disciplina_key:number;*/
        $cursos = [];
        $periodoLetivos = [];

        $listView = [];
        if(!is_array($pldaList))
            return [];
        foreach ($pldaList as $plda) {
            $curso = null;
            if (isset($cursos[$plda['codigo_curso']]))
                $curso = $cursos[$plda['codigo_curso']];
            else {
                $curso = Curso::where(['curso_key' => $plda['codigo_curso']])->first();
                $cursos[$plda['codigo_curso']] = $curso;
            }

            $pl = null;
            if (isset($periodoLetivos[$plda['periodo_letivo_id']]))
                $pl = $periodoLetivos[$plda['periodo_letivo_id']];
            else {
                $pl = PeriodoLetivo::where(['id_sigecad' => $plda['periodo_letivo_id']])->first();
                $periodoLetivos[$plda['periodo_letivo_id']] = $pl;
            }

            $listView[] = (object) [
                'curso' => $curso->id,
                'periodo_letivo' => $pl->id,
                'estudantes' => '',
                'disciplina' => $plda['nome_disciplina'],
                'disciplina_key' => $plda['codigo_disciplina'],
                'nome_professor' => $plda['nome_professor'],
                'username_professor' => $plda['username_professor'],
                'carga_horaria_total_disciplina' => $plda['carga_horaria_total_disciplina'],
                'avaliacao' => $plda['avaliacao'],
            ];
        }
        return $listView;
    }

    public function find(Request $request, $periodoLetivoId, $cursoId)
    {
        if (!$cursoId)
            abort(400, "Parâmetros de busca inválidos");
        return $this->findSigecad ($request, $periodoLetivoId, $cursoId);
    }
    private function findOrigin (Request $request, $periodoLetivoId, $cursoId) {
        if (!$periodoLetivoId) {
            $idSuperMacroPadrao = Configuracoes::where('nome', Configuracoes::CONFIGURACAO_SUPER_MACRO_PADRAO)->first();
            if (!$idSuperMacroPadrao->valor)
                abort(400, 'Uma super macro padrão deve ser configurada');
            $periodoLetivoId = SuperMacro::find($idSuperMacroPadrao)->first()->macroPadrao->periodo_letivo_id;
        }
        $pldaList = PlDisciplinaAcademico::where(['periodo_letivo_id' => $periodoLetivoId, 'curso_id' => $cursoId])->get();
        return $pldaList;
    }
    private function findSigecad (Request $request, $periodoLetivoId, $cursoId) {
        $c = Curso::find($cursoId);
        if (!$c)
            abort(400, 'Curso inválido!');
        $pl = null;
        if ($periodoLetivoId)
            $pl = PeriodoLetivo::find($periodoLetivoId);
        $pldaList =  App::make('SigecadService')->getDisciplinasCursoList($request, $c->faculdade->sigla, $c->curso_key, $pl ? $pl->nome : 0);
        return $pldaList;
        return $this->toView($pldaList);
    }

    private function getValidationRules() {
        // read more on validation at http://laravel.com/docs/validation
        $rules = array(
            'curso_id'          => 'required|numeric',
            'periodo_letivo_id' => 'required|numeric',
            'disciplina'        => 'required'
        );
        return $rules;
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

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        /*$periodoLetivoCategoria = new PeriodoLetivoCategoria();
        $periodoLetivoCategoria->curso_id = $request->input('curso_id');
        $periodoLetivoCategoria->periodo_letivo_id = $request->input('periodo_letivo_id');
        $periodoLetivoCategoria->categoria_id = $request->input('categoria_id');
        $periodoLetivoCategoria->save();
        return $periodoLetivoCategoria;*/
        $validator = Validator::make($request->all(), $this->getValidationRules());
        if ($validator->fails()) 
            abort(403, 'Erro de Validação');

        $plda = new PlDisciplinaAcademico();
        $plda->curso_id = $request->input('curso_id');
        $plda->periodo_letivo_id = $request->input('periodo_letivo_id');
        $plda->disciplina = $request->input('disciplina');
        $plda->estudantes = $request->input('estudantes') ;
        $plda->disciplina_key = $request->input('disciplina_key') ;
        $plda->save();
        return $plda;
    }

    
    public function processaTabelaSigecad(Request $request, $periodoLetivoId)
    {
        $pl = PeriodoLetivo::find($periodoLetivoId);
        if (!$pl)
            abort(400, "Período letivo não encontrado");
        $dados = App::make('SigecadService')->getCursosList($request, $pl->nome);

        $all = [];
        foreach ($dados as $linha) {
            $this->processaTupla($linha,$all);
        }
        
        //$this->limpaDisciplinas($periodoLetivoId);
        $this->processaFaculdades($all, $periodoLetivoId);
        return $all;
    }

    public function processaTupla($linha,&$all)
    {
        $sigla_faculdade = $linha[SigecadController::COLUNA_SIGLA_FACULDADE];
        $faculdade = null;
        if (isset($all[$sigla_faculdade])) 
            $faculdade = &$all[$sigla_faculdade];
        else {
            $faculdade = [];
            $faculdade['nome'] = $linha[SigecadController::COLUNA_NOME_FACULDADE];
            $faculdade['sigla'] = $sigla_faculdade;
            //$faculdade->auto_increment_ref = 0;
            $faculdade['cursos'] = [];
            $all[$sigla_faculdade] = &$faculdade;
        }

        $codigo_curso = $linha[SigecadController::COLUNA_CODIGO_CURSO];
        $hash_curso = $codigo_curso;//substr(md5('abcdef'),0,5);
        $curso = null;
        if (isset($faculdade['cursos'][$hash_curso])) 
            $curso = &$faculdade['cursos'][$hash_curso];
        else {
            $curso = [];
            $curso['nome'] = $linha[SigecadController::COLUNA_NOME_CURSO];
            $curso['curso_key'] = $codigo_curso;
            //$curso->faculdade_id = 0;
            $curso['disciplinas'] = [];
            $faculdade['cursos'][$hash_curso] = &$curso;
        }

        /*
        $nome_disciplina = $linha[SigecadController::COLUNA_NOME_DISCIPLINA];
        $disciplina = null;
        if (isset($curso['disciplinas'][$nome_disciplina])) 
            $disciplina = &$curso['disciplinas'][$nome_disciplina];
        else {
            $disciplina = [];
            $disciplina['nome'] = $nome_disciplina;
            $disciplina['disciplina_key'] = $linha[SigecadController::COLUNA_CODIGO_DISCIPLINA];
            $disciplina['estudantes'] = [];
            $curso['disciplinas'][$nome_disciplina] = &$disciplina;
        }

        $cpf = $linha[SigecadController::COLUNA_USERNAME_ALUNO];
        $estudante = null;
        if (isset($disciplina['estudantes'][$cpf])) 
            $estudante = &$disciplina['estudantes'][$cpf];
        else {
            $estudante = [];
            $estudante[] = $cpf;
            $estudante[] = $linha[SigecadController::COLUNA_EMAIL_ALUNO];
            $estudante[] = $linha[SigecadController::COLUNA_NOME_ALUNO];
            $disciplina['estudantes'][$cpf] = &$estudante;
        }
        */
    }

    public function processarArquivoEstudantes(Request $request, $periodoLetivoId) {
        $nameFile = null;
    
        // Verifica se informou o arquivo e se é válido
        $arquivo = $request->file('arquivo');
        if ($arquivo && $arquivo->isValid()) {
            
            // Define um aleatório para o arquivo baseado no timestamps atual
            $name = uniqid(date('HisYmd'));
    
            // Recupera a extensão do arquivo
            $extension = $request->arquivo->getClientOriginalExtension();
            if ($extension != "csv")
                abort(400, "Arquivo não permitido");

            $handle = fopen($arquivo, "r");
            $start = true;
            $all = [];
            while (($data = fgetcsv($handle, 0, ";",'"','\\')) !== FALSE) {
                if ($start){
                    $num = count($data);
                    if ($num != self::NUM_ELEMENTOS_CSV){
                        fclose($handle);
                        abort(400, "Arquivo inválido");
                        return;
                    }
                    $start = false;
                    continue;
                }
                $this->processaLinha($data,$all);
            }
            fclose($handle);
            $this->limpaDisciplinas($periodoLetivoId);
            $this->processaFaculdades($all, $periodoLetivoId);
            return $all;////
        }
        abort(400, "Arquivo muito grande ou inválido!");
    }

    public function processaLinha($linha,&$all)
    {
        $sigla_faculdade = $linha[self::INDEX_SIGLA_FACULDADE];
        $faculdade = null;
        if (isset($all[$sigla_faculdade])) 
            $faculdade = &$all[$sigla_faculdade];
        else {
            $faculdade = [];
            $faculdade['nome'] = $linha[self::INDEX_NOME_FACULDADE];
            $faculdade['sigla'] = $sigla_faculdade;
            //$faculdade->auto_increment_ref = 0;
            $faculdade['cursos'] = [];
            $all[$sigla_faculdade] = &$faculdade;
        }

        $codigo_curso = $linha[self::INDEX_CODIGO_CURSO];
        $hash_curso = $codigo_curso;//substr(md5('abcdef'),0,5);
        $curso = null;
        if (isset($faculdade['cursos'][$hash_curso])) 
            $curso = &$faculdade['cursos'][$hash_curso];
        else {
            $curso = [];
            $curso['nome'] = $linha[self::INDEX_NOME_CURSO];
            $curso['curso_key'] = $codigo_curso;
            //$curso->faculdade_id = 0;
            $curso['disciplinas'] = [];
            $faculdade['cursos'][$hash_curso] = &$curso;
        }

        $nome_disciplina = $linha[self::INDEX_NOME_DISCIPLINA];
        $disciplina = null;
        if (isset($curso['disciplinas'][$nome_disciplina])) 
            $disciplina = &$curso['disciplinas'][$nome_disciplina];
        else {
            $disciplina = [];
            $disciplina['nome'] = $nome_disciplina;
            $disciplina['disciplina_key'] = $linha[self::INDEX_CODIGO_DISCIPLINA];
            $disciplina['estudantes'] = [];
            $curso['disciplinas'][$nome_disciplina] = &$disciplina;
        }

        $cpf = $linha[self::INDEX_USERNAME];
        $estudante = null;
        if (isset($disciplina['estudantes'][$cpf])) 
            $estudante = &$disciplina['estudantes'][$cpf];
        else {
            $estudante = [];
            $estudante[] = $cpf;
            $estudante[] = $linha[self::INDEX_EMAIL];
            $estudante[] = $linha[self::INDEX_NOME_ESTUDANTE];
            $disciplina['estudantes'][$cpf] = &$estudante;
        }
        
    }

    public function processaFaculdades(&$all, $periodoLetivoId)
    {
        //itera entre as faculdades
        foreach ($all as $sigla => $fac) {
            //busca faculdade no banco
            $faculdade = Faculdade::where('sigla', $sigla)->first();
            if (!$faculdade){
                //  se não existe cria e obtem id
                $faculdade = new Faculdade();
                $faculdade->sigla = $sigla;
                $faculdade->nome = $fac['nome'];
                $faculdade->auto_increment_ref = null;
                $faculdade->ativo = true;
                $faculdade->save();
            }
            // processa curso
            $this->processaCursos($fac['cursos'], $faculdade->id, $periodoLetivoId);
        }
    }
    public function processaCursos(&$cursosArr, $faculdadeId, $periodoLetivoId)
    {
        //itera entre os cursos
        foreach ($cursosArr as $codigoCurso => $cur) {
            //busca curso no banco
            $curso = Curso::where(['curso_key' => $codigoCurso, 'faculdade_id' => $faculdadeId])->first();
            if (!$curso){
                //  se não existe cria e obtem id
                $curso = new Curso();
                $curso->nome = $cur['nome'];
                $curso->curso_key = $codigoCurso;
                $curso->auto_increment_ref = null;
                $curso->faculdade_id = $faculdadeId;
                $curso->ativo = true;
                $curso->save();
            }
            // processa disciplina
            $this->processaDisciplinas($cur['disciplinas'], $curso->id, $periodoLetivoId);
        }
    }
    public function processaDisciplinas(&$disciplinasArr, $cursoId, $periodoLetivoId)
    {
        //itera entre os cursos
        foreach ($disciplinasArr as $nomeDisciplina => $dis) {
            //busca disciplinas no banco
            $plc = PlDisciplinaAcademico::where(['disciplina'=>$nomeDisciplina, 'curso_id'=>$cursoId, 'periodo_letivo_id'=>$periodoLetivoId])->first();
            if (!$plc){
                //  se não existe cria e obtem id
                $plc = new PlDisciplinaAcademico();
                $plc->disciplina = $nomeDisciplina;
                $plc->curso_id = $cursoId;
                $plc->periodo_letivo_id = $periodoLetivoId;
                $plc->disciplina_key = $dis['disciplina_key'];
                $plc->estudantes = json_encode(array_values($dis['estudantes']));
                $plc->save();
            }
        }
    }

    private function limpaDisciplinas($periodoLetivoId) {
        PlDisciplinaAcademico::where(['periodo_letivo_id'=>$periodoLetivoId])->delete();
    }

    public function getEstudantes(Request $request, $id, $isDisciplinaCompleta = FALSE)
    {
        $plda = PlDisciplinaAcademico::find($id);
        if (!$plda) 
            abort(404, 'Dados não encontrados');
        if (!$isDisciplinaCompleta)
            return $plda->estudantes;
        $estAll = [];
        $plcs = PlDisciplinaAcademico::where(['periodo_letivo_id'=>$plda->periodo_letivo_id, 'disciplina_key'=>$plda->disciplina_key])->get();
        foreach ($plcs as $p) {
            $estAll = array_merge($estAll ,json_decode($p->estudantes));
        }
        return count($estAll) ? json_encode($estAll) : "";
    }
    public function getEstudantesSigecad(Request $request, $salaId)
    {
        $estAll = [];
        $sala = Sala::find($salaId);
        if (!$sala) 
            abort(404, 'Sala não encontrada');
        $plcs = App::make('SigecadService')->getAcademicosDisciplina($request, $sala->disciplina_key, $sala->periodoLetivo->nome, $sala->turma_id, $sala->turma_nome);
        return $plcs;
    }

    public function getListaDisciplinasSigecad(Request $request, $siglaFaculdade, $periodoLetivoId)
    {
        $pl = PeriodoLetivo::find($periodoLetivoId);
        if (!$pl)
            abort(400, "Período letivo não encontrado");
        return App::make('SigecadService')->getDisciplinasList($request, $siglaFaculdade, $pl->id_sigecad);
    }

    public function getDisciplinasCursoList(Request $request, $periodoLetivoId, $siglaFaculdade, $codigoCurso = 0)
    {
        $pl = PeriodoLetivo::find($periodoLetivoId);
        if (!$pl)
            abort(400, "Período letivo não encontrado");
        if ($codigoCurso)
            return App::make('SigecadService')->getDisciplinasCursoList($request, $siglaFaculdade, $codigoCurso, $pl->nome);
        return App::make('SigecadService')->getDisciplinasList($request, $siglaFaculdade, $pl->nome);
    }

    public function getAcademicosDisciplina(Request $request, $codigoDisciplina, $periodoLetivoId, $turmaId, $turmaNome, $salaId = 0)
    {
        $pl = PeriodoLetivo::find($periodoLetivoId);
        if (!$pl)
            abort(400, "Período letivo não encontrado");
        
        $prof = null;
        if ($salaId) {
            $sala = Sala::find($salaId);
            $prof = $sala->solicitante;
        }

        return App::make('SigecadService')->getAcademicosDisciplina($request, $codigoDisciplina, $pl->nome, $turmaId, $turmaNome, $prof);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
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
        $plda = PlDisciplinaAcademico::find($id);
        if (!$plda) 
            abort(404, 'Dados não encontrados');
        if (!$request->input('disciplina')) 
            abort(403, 'Erro de Validação');
        
        //$plda->curso_id = $request->input('curso_id');
        //$plda->periodo_letivo_id = $request->input('periodo_letivo_id');
        $plda->disciplina = $request->input('disciplina');
        //$plda->estudantes = $request->input('estudantes');
        $plda->disciplina_key = $request->input('disciplina_key') ;
        $plda->save();
        return $plda;
    }

    public function setEstudantes(Request $request, $id)
    {
        $plda = PlDisciplinaAcademico::find($id);
        if (!$plda) 
            abort(404, 'Dados não encontrados');
        if (!$request->input('estudantes')) 
            abort(403, 'Erro de Validação');
        $plda->estudantes = $request->input('estudantes');
        $plda->save();
        return $plda->estudantes;  
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $plda = PlDisciplinaAcademico::find($id);
        if (!$plda) 
            abort(404, 'Faculdade não encontrada');
        try{
            $plda->delete();
            return new PlDisciplinaAcademico();
        }
        catch (Exception $e) {
            abort(400, $e->getMessage());
        }
    }
}
