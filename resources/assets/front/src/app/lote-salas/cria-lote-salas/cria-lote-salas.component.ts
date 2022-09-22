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
import { Sala } from 'src/app/salas/sala';
import { UsuarioService } from 'src/app/usuario.service';
import { Usuario } from 'src/app/usuarios/usuario';

@Component({
    selector: 'app-cria-lote-salas',
    templateUrl: './cria-lote-salas.component.html',
    styleUrls: ['./cria-lote-salas.component.less']
})
export class CriaLoteSalasComponent extends AbstractComponent implements OnInit {

    constructor(private salasService: SalasService, private dadosService:DadosService, private faculdadeService: FaculdadeService, private plDisciplinasAcademicosService:PlDisciplinasAcademicosService,
        private macroService:MacroService, private periodoLetivoService: PeriodoLetivosService, private cursosService:CursosService, private usuarioService:UsuarioService) {
        super();
    }

    sala: Sala = Sala.geraNovaSala();

    statusTemp:string = "";

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

    buscarSalas() {
        this.plDisciplinasAcademicosService.getDisciplinasCursoSigecad(this.periodoLetivoSelecionadoId, this.faculdadeTemp.sigla, this.cursoSelecionadoCodigo)
            .then(salasAny => {
                this.salas = [];
                for (var i in salasAny) {
                    var sala = this.salasService.convertChargedSala(salasAny[i]);
                    sala.nome_professor = salasAny[i].nome_professor;
                    sala.username_professor = salasAny[i].username_professor;
                    sala.cpf_professor = salasAny[i].cpf_professor;
                    this.salas.push(sala);
                } 
                this.status = this.COMPLETE;
                console.log(this.salas);
            })
            .catch(response => {
                this.status = this.ERROR;
                console.log(response)
                this.salas = [];
            })
    }

    getPeriodoLetivo(plid) {
        return this.periodoLetivoService.periodoLetivos[this.periodoLetivoService.periodoLetivosIndex[plid]];
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
                                                                this.salas = [];
                                                                /*this.salasService.listar()
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
