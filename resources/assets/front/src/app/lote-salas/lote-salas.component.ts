import { Component, OnInit } from '@angular/core';
import { AbstractComponent } from 'src/app/abstract-component';
import { CursosService } from 'src/app/cursos.service';
import { DadosService } from 'src/app/dados.service';
import { FaculdadeService } from 'src/app/faculdade.service';
import { Faculdade } from 'src/app/faculdades/faculdade';
import { MacroService } from 'src/app/macro.service';
import { PeriodoLetivosService } from 'src/app/periodo-letivos.service';
import { PeriodoLetivo } from 'src/app/periodo-letivos/periodo-letivo';
import { PlDisciplinasAcademicosService } from 'src/app/pl-disciplinas-academicos.service';
import { Estudante } from 'src/app/pl-disciplinas-academicos/estudante';
import { PlDisciplinasAcademicos } from 'src/app/pl-disciplinas-academicos/pl-disciplinas-academicos';
import { SalasService } from 'src/app/salas.service';
import { UsuarioService } from 'src/app/usuario.service';
import { Usuario } from 'src/app/usuarios/usuario';
import { Curso } from '../cursos/curso';
import { LoteSalasService } from '../lote-salas.service';
import { Sala } from '../salas/sala';
import { Status } from '../status';
import { LoteSalas } from './lote-salas';
declare var jQuery: any;

@Component({
    selector: 'app-lote-salas',
    templateUrl: './lote-salas.component.html',
    styleUrls: ['./lote-salas.component.less']
})
export class LoteSalasComponent extends AbstractComponent implements OnInit {

    constructor(private salasService: SalasService, private dadosService:DadosService, private faculdadeService: FaculdadeService, 
        private plDisciplinasAcademicosService:PlDisciplinasAcademicosService, private macroService:MacroService, private periodoLetivoService: PeriodoLetivosService, 
        private cursosService:CursosService, private usuarioService:UsuarioService, private loteSalasService: LoteSalasService) {
        super();
    }

    readonly STATUS_INICIAL_PADRAO = Status.CHAVES.ANALISE;
    readonly STATUS_INICIAL_POS_LOTE_CRIADO = Status.CHAVES.PROCESSO;
    readonly STATUS_CONCLUIDO_PADRAO = Status.CHAVES.CONCLUIDO;
    readonly STATUS_REJEITADO_PADRAO = Status.CHAVES.REJEITADO;

    loteSalas: LoteSalas = null;

    sala: Sala = Sala.geraNovaSala();
    salaPadraoDados: Sala = Sala.geraNovaSala();
    isSalaPadraoVisualizada = false;

    statusTemp:string = this.STATUS_INICIAL_PADRAO;

    estudantes:Array<Estudante> = [];
    estudanteTemp: Estudante = new Estudante("","","",false);
    
    periodoLetivoSelecionadoId = "";
    faculdadeTemp = Faculdade.generateFaculdade();
    faculdadeSelecionadaId = "";
    cursoSelecionadoCodigo = "";
    disciplinaSelecionadaId = "";


    usuarios: Array<Usuario> = [];
    filteredUsuarios = [];
    nome_professor_temp = "";
    macrosBacks = {};

    mostraMais = false;

    COLUNAS = {
        id: false,
        periodo_letivo_id: false,
        periodo_letivo_key: false,
        nome_professor: true,
        username_professor: false,
        cpf_professor: false,
        sigla_faculdade: false,
        nome_faculdade: false,
        curso_key: false,
        curso: true,
        nome_sala: true,
        disciplina_key: false,
        carga_horaria_total_disciplina: false,
        avaliacao: false,
        turma_nome: false,
        turma_id: false,
        modalidade: false,
        objetivo_sala: false,
        sala_moodle_id: false,
    }

    get salas(): Array<Sala> {
        return this.salasService.salas;
    }

    set salas(salas: Array<Sala>) {
        this.salasService.salas = salas;
    }

    get faculdades() {
        return this.faculdadeService.faculdades;
    }
    get macros() {
        return this.macroService.macros;
    }
    get periodoLetivos () : Array<PeriodoLetivo> {
        return this.periodoLetivoService.periodoLetivos;
    }
    get modalidades() {
        return this.salasService.modalidades;
    }
    get objetivosSalas() {
        return this.salasService.objetivosSalas;
    }
    get lotesSalasList () :Array <LoteSalas> {
        return this.loteSalasService.lotesSalasList;
    }
    

    selecionaFaculdade() {
        if (this.faculdadeSelecionadaId) {
            this.faculdadeTemp = this.faculdadeService.faculdadesIndex.get(this.faculdadeSelecionadaId);
            this.cursoSelecionadoCodigo = "";
            this.disciplinaSelecionadaId = "";
            //this.sala.nome_sala = "";
        }
    }

    selecionaCurso(resetSala = true) {
        /*if (this.sala.curso) {
            this.plDisciplinasAcademicosTemp = PlDisciplinasAcademicos.generatePlDisciplinasAcademicos();
            //this.sala.nome_sala = "";
            this.disciplinaSelecionadaId = "";
            this.editavel = false;
            this.plDisciplinasAcademicosService.getPlDisciplinasAcademicos(this.sala.periodo_letivo_id, this.sala.curso)
                .then(r => {
                    this.editavel = true;
                    this.filteredDisciplina = this.filterDisciplina("", this.plDisciplinasAcademicosList);
                    if (resetSala)
                        this.sala.nome_sala = "";
                    else {
                        var plda = this.plDisciplinasAcademicosService.plDisciplinasAcademicosNameIndex.get(this.sala.nome_sala)
                        if (plda)
                            this.disciplinaSelecionadaId = plda.disciplina;
                    }
                }).catch(response => {
                    this.erroAviso = true;
                    this.aviso = this.erroHttp(response);
                    alert(this.aviso)
                    this.editavel = true;
                });
        }*/
    }

    novoLoteSalas() {
        this.editavel = false;
        this.plDisciplinasAcademicosService.getDisciplinasCursoSigecad(this.periodoLetivoSelecionadoId, this.faculdadeTemp.sigla, this.cursoSelecionadoCodigo)
            .then(salasAny => {
                this.salas = [];
                for (var i in salasAny) {
                    var sala = this.salasService.convertChargedSala(salasAny[i],null,this.dadosService.statusesIndex.get(this.STATUS_INICIAL_PADRAO));
                    sala.nome_professor = salasAny[i].nome_professor;
                    sala.username_professor = salasAny[i].username_professor;
                    sala.cpf_professor = salasAny[i].cpf_professor;
                    this.salas.push(sala);
                } 
                this.status = this.COMPLETE;
                this.aviso = "";
                this.erroAviso = false;
                this.loteSalas = LoteSalas.generate();
                this.loteSalas.salas = this.salas;
                this.editavel = true;
                this.statusTemp = this.STATUS_INICIAL_PADRAO;
                this.salaPadraoDados.status = this.dadosService.statusesIndex.get( this.STATUS_INICIAL_PADRAO );
            })
            .catch(response => {
                this.status = this.ERROR;
                this.aviso = this.erroHttp(response);
                this.erroAviso = true;
                console.log(this.aviso)
                alert(this.aviso)
                this.salas = [];
                this.editavel = true;
            })
    }

    getLoteSalasList() {
        this.editavel = false;
        this.aviso = "";
        this.erroAviso = false;
        this.loteSalasService.listar(this.periodoLetivoService.periodoLetivosIdIndex, this.faculdadeService.faculdadesIndex, this.cursosService.cursosIndex)
            .then(response => {
                jQuery('#dialogListaLotes').modal('show');
                this.editavel = true;
            })
            .catch(response => {
                this.status = this.ERROR;
                alert(this.erroHttp(response))
                console.log(response)
            })
    }

    selecionaLoteSalas(loteSalas:LoteSalas) {
        this.loteSalas = loteSalas;
        this.periodoLetivoSelecionadoId = (<PeriodoLetivo>loteSalas.periodo_letivo).id.toString();
        this.faculdadeSelecionadaId = (<Faculdade>loteSalas.faculdade).id.toString();
        this.selecionaFaculdade();
        this.cursoSelecionadoCodigo = loteSalas.curso ? (<Curso>loteSalas.curso).curso_key : "";
        this.loteSalasService.getSalasDoLote(loteSalas,this.periodoLetivoService.periodoLetivosIdIndex, this.faculdadeService.faculdadesIndex, this.cursosService.cursosIndex)
            .then(salasAny => {
                this.salas = [];
                for (var i in salasAny) {
                    var sala = Sala.geraNovaSala();
                    sala = this.salasService.convertCreatedSala(salasAny[i],sala/*,this.dadosService.statusesIndex.get(this.STATUS_INICIAL_PADRAO)*/);
                    sala.nome_professor = salasAny[i].nome_professor;
                    sala.username_professor = salasAny[i].username_professor;
                    sala.cpf_professor = salasAny[i].cpf_professor;
                    this.salas.push(sala);
                } 
                this.status = this.COMPLETE;
                this.aviso = "";
                this.erroAviso = false;
                this.loteSalas.salas = this.salas;
                this.editavel = true;
                if (this.salas[0]){
                    this.statusTemp = this.salas[0].status.chave;
                }
                else 
                    this.statusTemp = this.STATUS_INICIAL_POS_LOTE_CRIADO;
                this.geraMacrosBacks();
                jQuery('#dialogListaLotes').modal('hide');
            })
            .catch(response => {
                this.erroAviso = true;
                this.aviso = this.erroHttp(response);
                alert(this.aviso);
                console.log(response);
                this.editavel = true;
            })
    } 

    getPeriodoLetivo(plid) {
        return this.periodoLetivoService.periodoLetivos[this.periodoLetivoService.periodoLetivosIndex[plid]];
    }

    getLoteDesc () {
        return this.periodoLetivoSelecionadoId + " - " + this.faculdadeSelecionadaId + " - " + this.cursoSelecionadoCodigo;
    }

    aplicarSalaPadrao() {
        for (var i = 0; i < this.salas.length; i++){
            this.salas[i].modalidade = this.salaPadraoDados.modalidade;
            this.salas[i].objetivo_sala = this.salaPadraoDados.objetivo_sala;
            this.salas[i].observacao = this.salaPadraoDados.observacao;
            this.salas[i].senha_aluno = this.salaPadraoDados.senha_aluno;
        }
    }

    preparaCriaSolicitacoes() {
        var ret = "";
        if (!this.loteSalas.descricao)
            ret += "\nDescrição do Lote";
        if (!this.salaPadraoDados.modalidade)
            ret += "\nModalidade das Salas";
        if (!this.salaPadraoDados.objetivo_sala)
            ret += "\nObjetivos das Salas";
        return ret;
    }

    visualizarSala(sala:Sala) {
        this.isSalaPadraoVisualizada = this.salaPadraoDados == sala;
        this.sala = this.isSalaPadraoVisualizada ? sala : sala.clone();
        //this.estudantes = Estudante.converteJSONParaEstudantes(this.sala.estudantes);
        //this.faculdadeTemp = this.sala.curso ? this.sala.curso.faculdade : Faculdade.generateFaculdade();
        //this.faculdadeSelecionadaId = this.faculdadeTemp.id ? this.sala.curso.faculdade.id : "";
        //this.disciplinaSelecionadaId = "";
        this.nome_professor_temp = this.sala.nome_professor;
        this.sala.curso =  this.sala.curso ? this.sala.curso.id : "";
        if (sala.status.chave ==  this.STATUS_REJEITADO_PADRAO || sala.status.chave ==  this.STATUS_CONCLUIDO_PADRAO ) {
            this.aviso = this.sala.mensagem;
            this.erroAviso = sala.status.chave == this.STATUS_REJEITADO_PADRAO;
        }
        else{
            this.aviso = "";
            this.erroAviso = false;
        }
        this.editavel = this.isSalaPadraoVisualizada;
        this.mostraMais = false;
    }

    fechaDialogVizualizarSalas() {
        jQuery('#dialogSalas').modal('hide');
        this.editavel = true;
    }

    criaLoteSalas () {
        var checkValida = this.preparaCriaSolicitacoes();
        if (checkValida == "") {
            if(this.loteSalas) {
                this.loteSalas.periodo_letivo = parseInt(this.periodoLetivoSelecionadoId);
                this.loteSalas.faculdade = parseInt(this.faculdadeSelecionadaId);
                this.loteSalas.curso = this.cursoSelecionadoCodigo ? this.cursosService.cursosKeyIndex.get(this.cursoSelecionadoCodigo).id : 0;
                this.aplicarSalaPadrao();
                this.editavel = false;
                //console.log (this.loteSalas) ;
                this.loteSalasService.criaLoteSalas(this.loteSalas)
                    .then(response => {
                        //console.log (response)
                        this.loteSalas.id = response.id;
                        this.statusTemp = this.STATUS_INICIAL_POS_LOTE_CRIADO;

                        var salasAny = response.salas
                        this.salas = [];
                        for (var i in salasAny) {
                            var sala = Sala.geraNovaSala();
                            sala = this.salasService.convertCreatedSala(salasAny[i],sala,this.dadosService.statusesIndex.get(this.STATUS_INICIAL_POS_LOTE_CRIADO));
                            sala.lote_salas_id = response.id;
                            sala.username_professor = salasAny[i].username_professor;
                            sala.cpf_professor = salasAny[i].cpf_professor;
                            this.salas.push(sala);
                        }
                        this.geraMacrosBacks();
                        alert("Lote de Salas Criado!");
                        this.editavel = true;
                    })
                    .catch(response => {
                        this.editavel = true;
                        this.status = this.ERROR;
                        this.aviso = this.erroHttp(response);
                        alert(this.aviso);
                        console.log(this.aviso);
                    })
                
            }
        }
        else
            alert("Falta informar alguns dados:\n"+ checkValida);
    }

    alteraMacro(sala:Sala,macroId) {
        var macro_id_anterior = this.macrosBacks[sala.id];
        this.editavel = false;
        this.loteSalasService.updateMacro(sala, macroId)
            .then(response => {
                this.macrosBacks[sala.id] = response;
                this.sala.macro_id = response;
                this.editavel = true;
            })
            .catch(response => {
                this.editavel = true;
                console.log(response);
                alert(this.erroHttp(response))
                sala.macro_id = macro_id_anterior;
            })
        //this.macrosBacks[sala.id] = macroId;
    }

    criaSalas() {
        this.editavel = false;
        if(this.loteSalas && confirm ("Deseja executar as exportações automáticas das salas deste lote?") ) {
            this.loteSalasService.executaExportacoes(this.loteSalas)
                .then(response => {
                    jQuery('#dialogRestoreLote').modal('show');
                    jQuery('#saidaRestore').html(response);
                    this.loteSalas.is_salas_criadas = true;
                    this.editavel = true;
                    this.selecionaLoteSalas(this.loteSalas);
                })
                .catch(response => {
                    jQuery('#dialogRestoreLote').modal('show');
                    this.erroAviso = true;
                    this.aviso = this.erroHttp(response);
                    this.editavel = true;
                    jQuery('#saidaRestore').html('<span style="color: red;">'+this.aviso+"</span>");
                    this.selecionaLoteSalas(this.loteSalas);
                });
        }
    }

    inserirEstudantes() {
        this.editavel = false;
        if(this.loteSalas && confirm ("Deseja executar as a busca e inserção de estudantes nas salas deste lote?") ) {
            this.loteSalasService.insereEstudantes(this.loteSalas)
                .then(response => {
                    jQuery('#dialogRestoreLote').modal('show');
                    jQuery('#saidaRestore').html(response);
                    this.loteSalas.is_estudantes_inseridos = true;
                    this.editavel = true;
                    this.selecionaLoteSalas(this.loteSalas);
                })
                .catch(response => {
                    jQuery('#dialogRestoreLote').modal('show');
                    this.erroAviso = true;
                    this.aviso = this.erroHttp(response);
                    this.editavel = true;
                    jQuery('#saidaRestore').html('<span style="color: red;">'+this.aviso+"</span>");
                    this.selecionaLoteSalas(this.loteSalas);
                });
        }
    }

    cancelar() {
        this.loteSalas = null;
        this.sala = Sala.geraNovaSala();
        this.periodoLetivoSelecionadoId = "";
        this.faculdadeTemp = Faculdade.generateFaculdade();
        this.faculdadeSelecionadaId = "";
        this.cursoSelecionadoCodigo = "";
        this.disciplinaSelecionadaId = "";
        this.editavel = true;
        this.statusTemp = this.STATUS_INICIAL_PADRAO;
        this.salaPadraoDados.status = this.dadosService.statusesIndex.get( this.STATUS_INICIAL_PADRAO );
        this.salas = [];
    }

    excluiSala(sala) {
        if (!confirm("Deseja remover esta sala do lote?"))
            return;
        
        for (var i = 0; i < this.salas.length; i++) {
            if (this.salas[i] == sala) {
                this.salas.splice(i,1);
                return;
            }  
        }
    }

    geraMacrosBacks() {
        this.macrosBacks = {};
        for (var i = 0; i < this.salas.length; i++) {
            this.macrosBacks[this.salas[i].id] = this.salas[i].macro_id + "";
        }
    }

    ngOnInit() {
        this.dadosService.statusList()
            .then(response => {
                this.periodoLetivoService.getPeriodoLetivos()
                    .then(response => {
                        this.usuarioService.listaUsuariosCriaSala()
                            .then(response => {
                                this.usuarios = response;
                                this.faculdadeService.listar()
                                    .then(response => {
                                        this.macroService.getMacros()
                                            .then(response => {
                                                this.salasService.getObjetivosSalas()
                                                    .then(response => {
                                                        this.salasService.getModalidades()
                                                            .then(response => {
                                                                this.status = this.COMPLETE;
                                                                this.editavel = true;
                                                                this.salas = [];
                                                                /*this.loteSalasService.listar()
                                                                    .then(response => {

                                                                    })
                                                                    .catch(response => {
                                                                        this.status = this.ERROR;
                                                                        console.log(response)
                                                                    })*/
                                                            })
                                                            .catch(response => {
                                                                this.status = this.ERROR;
                                                                console.log(response)
                                                            }) 
                                                    })
                                                    .catch(response => {
                                                        this.status = this.ERROR;
                                                        console.log(response)
                                                    })
                                            })
                                            .catch(response => {
                                                this.status = this.ERROR;
                                                console.log(response)
                                            })                                        
                                    })
                                    .catch(response => {
                                        this.status = this.ERROR;
                                        console.log(response)
                                    })
                            })
                            .catch(response => {
                                this.status = this.ERROR;
                                console.log(response)
                            })
                    })
                    .catch(response => {
                        this.status = this.ERROR;
                        console.log(response)
                    })
            })
            .catch(response => {
                this.status = this.ERROR;
                console.log(response)
            });
    }

}

