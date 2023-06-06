<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

use App\SalaOld;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Route;

Route::get('/', 'HomeController@index');

Route::get('/est/{periodoLetivoId}', 'PlDisciplinasAcademicosController@limpaDisciplinas');

Route::get('/statuslist', 'HomeController@statuslist');
Route::get('/sufixonome', 'HomeController@getSufixoNomeSala');

Route::get('/macro', 'MacroController@index');
Route::post('/macro/file', 'MacroController@store');
Route::post('/macro/name', 'MacroController@createUpdate');
Route::get('/macro/all', 'MacroController@all');
Route::delete('/macro/{id}', "MacroController@delete");
Route::get('/macro/busca', 'MacroController@buscadores');
Route::get('/macro/entradas', 'MacroController@getEntradasBuscadores');
Route::get('/macro/{macroId}/buscador', 'MacroController@getBuscadores');
Route::put('/macro/{macroId}/buscador', 'MacroController@addSetBuscador');
Route::delete('/macro/buscador/{buscadorId}', 'MacroController@delBuscador');
Route::post('/macro/mudararquivo', 'MacroController@mudarArquivo');
Route::get('/config', 'MacroController@config');
Route::post('/config', 'MacroController@updateConfig');
Route::get('/exec/{id}', 'MacroController@executar');
Route::get('/exec-old/{id}', 'MacroController@executarOld');
Route::get('/dd', 'MacroController@download');
Route::get('/files', 'MacroController@listFiles');

Route::get('/super-macro/all', 'SuperMacroController@all');
Route::get('/super-macro/msm/order/{msmId1}/{msmId2}', 'SuperMacroController@ordenaMSM');
Route::get('/super-macro/msm/campos', 'SuperMacroController@getMsmCampos');
Route::get('/super-macro/msm/operadores', 'SuperMacroController@getMsmOperadores');
Route::get('/super-macro/msm/{superMacroId}', 'SuperMacroController@allMSM');
Route::post('/super-macro/msm', 'SuperMacroController@newMSM');
Route::delete('/super-macro/msm/{msmId}', 'SuperMacroController@delMSM');
Route::put('/super-macro/msm/{msmId}', 'SuperMacroController@updateMSM');
Route::resource('super-macro', 'SuperMacroController');


Route::get('/salas/status/{salaId}/{status}/{mensagem?}', 'SalaController@statusSala')->where('mensagem', '(.*)');
//Route::get('/salas/status/{salaId}/{status}/{mensagem?}', 'TesteController@email')->where('mensagem', '(.*)');
Route::patch('/salas/status/{salaId}', 'SalaController@statusSala');
Route::get('/salas/mensagem/{salaId}', 'SalaController@mensagem');
Route::post('/salas/sala-moodle/{salaId}', 'SalaController@getSalaMoodle');
Route::post('/salas/autorestore/{salaId}', 'SalaController@executarRestauracaoAutomatica');
Route::post('/salas/autorestore-estudantes/{salaId}', 'SalaController@exportarEstudantesMoodle');
Route::get('/salas/success/', 'SalaController@success');
Route::post('/salas/{sala}/', 'SalaController@update');
Route::post('/salas/sendemail/{salaId}', 'SalaController@sendEmail');
Route::get('/salas/listar', 'SalaController@listar');
Route::get('/salas/preparacreate', 'SalaController@preparaCreate');
Route::get('/salas/modalidades', 'SalaController@getModalidades');
Route::get('/salas/objetivos', 'SalaController@getObjetivosSalas');
Route::get('/salas/usuarios', 'UsuarioController@list');
Route::get('/salas/charge/{periodoLetivoKey}/{codigoCurso}/{codigoDisciplina}/{salaTurma}', 'SalaController@chargeDisciplina');
Route::get('/salas/create/{periodoLetivoKey}/{codigoCurso}/{codigoDisciplina}/{salaTurma}', 'SalaController@create');
Route::resource('salas', 'SalaController');

Route::get('/salas-old/status/{salaId}/{status}/{mensagem?}', 'SalasOldController@statusSala')->where('mensagem', '(.*)');
Route::patch('/salas-old/status/{salaId}', 'SalasOldController@statusSala');
Route::get('/salas-old/mensagem/{salaId}', 'SalasOldController@mensagem');
Route::get('/salas-old/autorestore/{salaId}', 'SalasOldController@executarRestauracaoAutomatica');
Route::get('/salas-old/success/', 'SalasOldController@success');
Route::post('/salas-old/{sala}/', 'SalasOldController@update');
Route::get('/salas-old/listar', 'SalasOldController@listar');
Route::resource('salas-old', 'SalasOldController');

Route::get('/lote-salas/all', 'LoteSalasController@all');
Route::get('/lote-salas/exportacao/{loteId}', 'LoteSalasController@executaExportacoes');
Route::get('/lote-salas/estudantes/{loteId}', 'LoteSalasController@insereEstudantes');
Route::put('/lote-salas/macro', 'LoteSalasController@updateMacro');
Route::resource('lote-salas', 'LoteSalasController');

Route::get('/grupo-lotes-simplificados/all', 'GrupoLotesSimplificadoController@all');
Route::get('/grupo-lotes-simplificados/estudantes/{grupoId}', 'GrupoLotesSimplificadoController@insereEstudantes');
Route::resource('grupo-lotes-simplificados', 'GrupoLotesSimplificadoController');

Route::get('lote-salas-simplificados/rota', 'LoteSalasSimplificadosController@rota');
Route::get('lote-salas-simplificados/all/{grupoId}', 'LoteSalasSimplificadosController@all');
Route::get('lote-salas-simplificados/exportacao/{loteId}', 'LoteSalasSimplificadosController@executaExportacoes');
Route::get('lote-salas-simplificados/estudantes/{loteId}', 'LoteSalasSimplificadosController@insereEstudantes');
Route::resource('lote-salas-simplificados', 'LoteSalasSimplificadosController');

Route::get('salas-simplificadas/list-lote/{loteId}', 'SalaSimplificadaController@listLote');
Route::get('salas-simplificadas/macro/{salaId}', 'SalaSimplificadaController@getMacro');
Route::get('salas-simplificadas/refresh/{salaId}', 'SalaSimplificadaController@refreshSala');
Route::get('/salas-simplificadas/autorestore/{salaId}/{macroId}/{courseImportId?}', 'SalaSimplificadaController@executarRestauracaoSala');
Route::get('salas-simplificadas/estudantes/{sala}', 'SalaSimplificadaController@insereEstudantes');
Route::resource('salas-simplificadas', 'SalaSimplificadaController');

Route::get('/periodo-letivos/all', 'PeriodoLetivosController@all');
Route::get('/periodo-letivos/id-padrao', 'PeriodoLetivosController@getPeriodoLetivoIdPadrao');
Route::get('/periodo-letivos/sigecad', 'PeriodoLetivosController@getListaSigecad');
Route::resource('periodo-letivos', 'PeriodoLetivosController');

Route::get('/faculdades/all', 'FaculdadesController@all');
Route::resource('faculdades', 'FaculdadesController');

Route::get('/cursos/all', 'CursosController@all');
Route::resource('cursos', 'CursosController');

Route::get('/periodo-letivos-categorias/all/{periodoLetivoId}', 'PeriodoLetivosCategoriasController@all');
Route::resource('periodo-letivos-categorias', 'PeriodoLetivosCategoriasController');

Route::get('/pl-disciplinas-academicos/find/{periodoLetivoId}/{cursoId}', 'PlDisciplinasAcademicosController@find');
Route::get('/pl-disciplinas-academicos/estudantes/{id}/{isDisciplinaCompleta?}', 'PlDisciplinasAcademicosController@getEstudantes');
Route::put('/pl-disciplinas-academicos/estudantes/{id}', 'PlDisciplinasAcademicosController@setEstudantes');
Route::post('/pl-disciplinas-academicos/estudantes/{periodoLetivoId}', 'PlDisciplinasAcademicosController@processarArquivoEstudantes');
Route::get('/pl-disciplinas-academicos/carrega-cursos-sigecad/{periodoLetivoId}', 'PlDisciplinasAcademicosController@processaTabelaSigecad');
Route::get('/pl-disciplinas-academicos/disciplinas-sigecad/{siglaFaculdade}/{periodoLetivoId}', 'PlDisciplinasAcademicosController@getListaDisciplinasSigecad');
Route::get('/pl-disciplinas-academicos/disciplinas-curso-sigecad/{periodoLetivoId}/{siglaFaculdade}/{codigoCurso?}', 'PlDisciplinasAcademicosController@getDisciplinasCursoList');
Route::get('/pl-disciplinas-academicos/academicos-disciplinas-sigecad/{codigoDisciplina}/{periodoLetivoId}/{turmaId}/{turmaNome}/{salaId?}', 'PlDisciplinasAcademicosController@getAcademicosDisciplina');
//Route::get('/pl-disciplinas-academicos/academicos-disciplinas-sigecad/{salaId}', 'PlDisciplinasAcademicosController@getEstudantesSigecad');
Route::resource('pl-disciplinas-academicos', 'PlDisciplinasAcademicosController');

Route::get('/servidores-moodle/all', 'ServidoresMoodleController@all');
Route::get('/servidores-moodle/links', 'ServidoresMoodleController@links');
Route::get('/servidores-moodle/download-script', 'ServidoresMoodleController@downloadScript');
Route::resource('servidores-moodle', 'ServidoresMoodleController');

Route::get('/formulario-insere-usuarios', 'ServidoresMoodleController@formulariosIndex');
Route::post('/formulario-insere-usuarios', 'ServidoresMoodleController@exportarEstudantes');
Route::get('/formulario-recuperacao-arquivos', 'ServidoresMoodleController@formulariosIndex');

//Route::get('/agenda', function () {return view("layouts.app-angular");});
Route::get('/calendario', function () {
    return view("layouts.app-angular-limpo");
});
Route::get('/agenda/listar', 'AgendaController@listar');
Route::resource('agenda', 'AgendaController');

Route::get('/recursos/{recursoId}/gestores', 'RecursoController@getGestoresRecurso');
Route::get('/recursos/attach/{recursoId}/{gestorId}', 'RecursoController@attachGestor');
Route::get('/recursos/detach/{recursoId}/{gestorId}', 'RecursoController@detachGestor');
Route::get('/recursos/listar', 'RecursoController@listar');
Route::resource('recursos', 'RecursoController');
Route::get('/reservas/listar', 'ReservasController@listar');
Route::get('/reservas/recurso', 'ReservasController@recurso');
Route::get('/reservas/usuario', 'ReservasController@usuarioLogado');
Route::put('/reservas/status', 'ReservasController@analiseGestor');
Route::put('/reservas/cancelar', 'ReservasController@cancelar');
Route::resource('reservas', 'ReservasController');

Route::get('/formulario-insere-ad', 'UnidadeOrganizacionalController@index');
Route::post('/formulario-insere-ad', 'UnidadeOrganizacionalController@criarContasAD');
Route::post('/formulario-insere-ad/substitui-emails', 'UnidadeOrganizacionalController@substituiEmailsPorPadrao');
Route::get('/formulario-altera-usuario', 'UnidadeOrganizacionalController@index');
Route::post('/formulario-altera-usuario/password', 'UnidadeOrganizacionalController@alterarSenha');
Route::get('/unidade-organizacional/ldapuser/{username}', 'UnidadeOrganizacionalController@getLdapUser');
Route::get('/unidade-organizacional/ous-filhas', 'UnidadeOrganizacionalController@getOusFilhas');
Route::get('/unidade-organizacional/ou-dir-root', 'UnidadeOrganizacionalController@getOuDirRoot');
Route::post('/unidade-organizacional/ou-dir-root', 'UnidadeOrganizacionalController@setOuDirRoot');
Route::get('/unidade-organizacional/all', 'UnidadeOrganizacionalController@all');
Route::resource('unidade-organizacional', 'UnidadeOrganizacionalController');

Route::get('/formulario-pessoas-estatus-lotacao', 'PessoasEstatusLotacaoController@index');
Route::get('/formulario-pessoas-estatus-lotacao/estatus', 'PessoasEstatusLotacaoController@estatusList');
Route::post('/formulario-pessoas-estatus-lotacao/lotacoes', 'PessoasEstatusLotacaoController@lotacoesList');
Route::get('/formulario-pessoas-estatus-lotacao/lotacoes-full', 'PessoasEstatusLotacaoController@lotacoesFullList');
Route::post('/formulario-pessoas-estatus-lotacao/faculdades', 'PessoasEstatusLotacaoController@faculdadesList');
Route::post('/formulario-pessoas-estatus-lotacao/cursos-faculdade', 'PessoasEstatusLotacaoController@cursosFaculdadeList');
Route::post('/formulario-pessoas-estatus-lotacao/academico', 'PessoasEstatusLotacaoController@getDadosAcademico');
Route::post('/formulario-pessoas-estatus-lotacao/funcionario', 'PessoasEstatusLotacaoController@getDadosFuncionarios');

Route::get('/usuarios/lista', 'UsuarioController@all');
Route::get('/logado', 'UsuarioController@usuarioLogado');
Route::resource('usuarios', 'UsuarioController');

Route::get('/logs/exportacao-estudantes/{arquivo?}', 'LogsController@exportacaoEstudantes');
Route::get('/logs', 'LogsController@index');

Route::post('/usuario-cartao', 'WebServicesController@consultaDadosCartao');
Route::get('/estudantes-grupo-lotes', 'WebServicesController@exportaEstudantesGrupoLotes');

//Route::get('/sigecad/{codigoDisciplina}/{periodoLetivoKey}/{turmaKey}', 'TesteController@sigecad');
Route::get('/teste', 'TesteController@down');

Route::get('/css/{param1}', function ($param1) {
    $str = preg_replace('/\.[a-z0-9]*\./', '.', $param1);
    return Response::download("js/angular/" . $str);
})->where('param1', '(primeicons.+|color.+|hue.+)');
Route::get('/{param2}', function ($param2) {
    return Response::download("js/angular/" . $param2);
})->where('param2', '(open-sans-v15-latin.+|primeicons.+)');

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');
