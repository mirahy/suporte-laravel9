import { Component, OnInit } from '@angular/core';
import { AbstractComponent } from 'src/app/abstract-component';
import { SalasService } from 'src/app/salas.service';
import { Sala } from '../sala';
import { Status } from 'src/app/status';
import { FaculdadeService } from 'src/app/faculdade.service';
import { Faculdade } from 'src/app/faculdades/faculdade';
import { CursosService } from 'src/app/cursos.service';
import { PlDisciplinasAcademicos } from 'src/app/pl-disciplinas-academicos/pl-disciplinas-academicos.js';
import { PlDisciplinasAcademicosService } from 'src/app/pl-disciplinas-academicos.service';
import { UsuarioService } from 'src/app/usuario.service';
import { Usuario } from 'src/app/usuarios/usuario';
import { ThirdPartyDraggable } from '@fullcalendar/interaction';
import { PeriodoLetivo } from 'src/app/periodo-letivos/periodo-letivo';
import { PeriodoLetivosService } from 'src/app/periodo-letivos.service';
import { ActivatedRoute } from '@angular/router';
import { Curso } from 'src/app/cursos/curso';
import { forEach } from '@angular-devkit/schematics';
declare var jQuery: any;

@Component({
    selector: 'app-cria-salas',
    templateUrl: './cria-salas.component.html',
    styleUrls: ['./cria-salas.component.less']
})
export class CriaSalasComponent extends AbstractComponent implements OnInit {

    readonly STATUS_INICIAL_PADRAO = Status.CHAVES.PROCESSO;
    readonly STATUS_CONCLUIDO_PADRAO = Status.CHAVES.CONCLUIDO;
    readonly STATUS_REJEITADO_PADRAO = Status.CHAVES.REJEITADO;

    constructor(private salasService:SalasService, private faculdadeService: FaculdadeService, private cursosService:CursosService, private periodoLetivoService: PeriodoLetivosService,
                private usuarioService:UsuarioService,  private plDisciplinasAcademicosService:PlDisciplinasAcademicosService, private route: ActivatedRoute) {
        super();
    }

    sala:Sala = Sala.geraNovaSala();
    salaResp = Sala.geraNovaSala();
    emailResp = "";
    redirectLink = "";

    mensagemDialog = "Carregando dados...";

    faculdadeTemp = Faculdade.generateFaculdade();
    faculdadeSelecionadaId = "";

    //faculdadesOptions = [];

    plDisciplinasAcademicosTemp: PlDisciplinasAcademicos = PlDisciplinasAcademicos.generatePlDisciplinasAcademicos();
    filteredDisciplina = [];

    usuarios: Array<Usuario> = [];
    filteredUsuarios = [];
    nome_professor_temp = "";
    nome_sala_moodle = "";
    professor_sala_moodle = "";

    get faculdades() {
        return this.faculdadeService.faculdades;
    }
    get modalidades() {
        return this.salasService.modalidades;
    }
    get objetivosSalas() {
        return this.salasService.objetivosSalas;
    }
    get plDisciplinasAcademicosList () : Array<PlDisciplinasAcademicos> {
        return this.plDisciplinasAcademicosService.plDisciplinasAcademicos;
    }
    get periodoLetivos () : Array<PeriodoLetivo> {
        return this.periodoLetivoService.periodoLetivos;
    }

    criaSala (ev) {
        ev.preventDefault();
        var salaForm = jQuery('#salaForm')[0];
		if (salaForm.reportValidity() && this.validaLinkMoodle()) {
            this.editavel = false;
            if (this.plDisciplinasAcademicosService.plDisciplinasAcademicosNameIndex && this.plDisciplinasAcademicosService.plDisciplinasAcademicosNameIndex.get(this.sala.nome_sala))
                this.salasService.aplicarPlDisciplina(this.sala, this.plDisciplinasAcademicosService.plDisciplinasAcademicosNameIndex.get(this.sala.nome_sala))
            this.salasService.create(this.sala)
                .then(r => {
                    this.salaResp = r.sala;
                    this.salaResp.curso = this.cursosService.cursosIndex.get(r.sala.curso_id);
                    this.emailResp = r.email;
                    this.redirectLink = r.redirect;
                    if (this.redirectLink) {
                        window.location.href = this.redirectLink;
                    }
                    else {
                        jQuery('#dialogCreate').modal('show');
                        this.sala = Sala.geraNovaSala();
                        this.sala.nome_professor = this.salaResp.nome_professor;
                        this.sala.email = this.salaResp.email;
                        this.faculdadeSelecionadaId = "";
                    }
                    this.editavel = true;
                }).catch(response => {
                    this.erroAviso = true;
                    this.aviso = this.erroHttp(response);
                    this.editavel = true;
                });
		}
    }

    geraStatus () {

    }

    getSalaMoodle(){
      this.erroAviso = false;
      this.aviso = "";
      this.nome_sala_moodle = "";
      this.professor_sala_moodle = "";
      // removendo todos os espaços em branco
      var link = this.sala.observacao.replace(/\s+/g, '');
      if(link !== "" && this.validaLinkMoodle()){
              jQuery('#dialogMensagem').modal('show');
              var id = "";
              if(link !== "" && link.indexOf("&") > 0){
                // url com mais parametros alem do id
                id = link.substring(link.indexOf("view.php?id=")+12, link.indexOf("&"));
              }else{
                // url somemnte com parametro id
                id = link.substring(link.indexOf("=") + 1);
              }
              if(id == ""){
                jQuery('#dialogMensagem').modal('hide');
                this.erroAviso = true;
                this.aviso = link + "***{O ID NÃO PODE ESTAR VAZIO!!!}***"
                return ;
              }
              this.salasService.getSalaMoodle(id, this.sala)
                .then(r => {
                  var result = r.json()
                  var salas = result.enrolledcourses;
                  salas.forEach(element => {
                    if(element.id == id){
                      this.nome_sala_moodle = element.fullname;
                    }
                  });
                  this.professor_sala_moodle = result.fullname
                }).catch(response => {
                    this.erroAviso = true;
                    this.aviso = this.erroHttp(response);
                    this.editavel = true;
                });
              jQuery('#dialogMensagem').modal('hide');
      }

    }

    validaLinkMoodle(){
      // removendo todos os espaços em branco
      var link = this.sala.observacao.replace(/\s+/g, '');
      if(link !== "" &&
          link.includes("https://presencial.ead.ufgd.edu.br/course/view.php?id=") ||
          link.includes("https://moodle.ead.ufgd.edu.br/course/view.php?id=") ||
          link.includes("http://host-apache-2/course/view.php?id=")){         //link de testes, comentar em servidor da produção
          this.erroAviso = false;
          this.aviso = "";
          return true;
      }
      else if(link !== ""){
        jQuery('#dialogMensagem').modal('hide');
        this.erroAviso = true;
        this.aviso = "Link incorreto, favor inserir um link válido, ex.: 'https://moodle.ead.ufgd.edu.br/course/view.php?id=***'"
        return false;
      }else if(link == ""){
        return true;
      }
    }
    selecionaPeriodoLetivo () {
        if (this.sala.periodo_letivo_id) {
            this.sala.curso = "";
            this.sala.nome_sala = "";
        }
    }

    selecionaFaculdade() {
        if (this.faculdadeSelecionadaId) {
            this.faculdadeTemp = this.faculdadeService.faculdadesIndex.get(this.faculdadeSelecionadaId);
            this.sala.curso = "";
            this.sala.nome_sala = "";
        }

    }
    selecionaCurso(disciplinaPrevia?:string) {
        if (this.sala.curso) {
            this.plDisciplinasAcademicosTemp = PlDisciplinasAcademicos.generatePlDisciplinasAcademicos();
            //this.sala.nome_sala = "";
            this.editavel = false;
            if (!disciplinaPrevia)
                jQuery('#dialogMensagem').modal('show');
            this.plDisciplinasAcademicosService.getPlDisciplinasAcademicos(this.sala.periodo_letivo_id, this.sala.curso)
                .then(r => {
                    this.editavel = true;
                    this.filteredDisciplina = this.filterDisciplina("", this.plDisciplinasAcademicosList);
                    if(disciplinaPrevia) {
                        var pl = this.plDisciplinasAcademicosService.plDisciplinasAcademicosNameIndex.get(disciplinaPrevia);
                        if (!pl)
                            pl = this.plDisciplinasAcademicosService.plDisciplinasAcademicosNameIndex.get(disciplinaPrevia + " - " + this.sala.turma_nome);
                        this.sala.nome_sala = pl ? pl.disciplina : "";
                    }
                    else{
                        this.sala.nome_sala = "";
                    }
                    jQuery('#dialogMensagem').modal('hide');
                }).catch(response => {
                    this.erroAviso = true;
                    this.aviso = this.erroHttp(response);
                    alert(this.aviso)
                    this.editavel = true;
                    jQuery('#dialogMensagem').modal('hide');
                });
        }
    }

    buscaDisciplina(event) {
        this.filteredDisciplina = this.filterDisciplina(event.query, this.plDisciplinasAcademicosList);
    }
    buscaUsuario(event) {
        if (event.query.length > 1)
            this.filteredUsuarios = this.filterUsuario(event.query, this.usuarios);
        else
        this.filteredUsuarios = [];
    }
    selecionaUsuario(event) {
        var id = event.substring(0,event.indexOf(' - ')) ;
        console.log(id);
        this.sala.solicitante_id = id;
        this.nome_professor_temp = event.substring(event.indexOf(' - ')+3) ;
    }
    limpaUsuario(event) {
        this.sala.solicitante_id = "";
    }

    private filterDisciplina(query, plcs: PlDisciplinasAcademicos[]):any[] {
        let filtered : string[] = [];
        for(let i = 0; i < plcs.length; i++) {
            let plc = plcs[i];
            if (plc.disciplina.toLowerCase().indexOf(query.toLowerCase()) == 0) {
                filtered.push(plc.disciplina);
            }
        }
        return filtered;
    }
    private filterUsuario(query, users: Usuario[]):any[] {
        let filtered : string[] = [];
        for(let i = 0; i < users.length; i++) {
            let u = users[i];
            if (u.name.toLowerCase().indexOf(query.toLowerCase()) == 0) {
                filtered.push(u.id + " - " + u.name);
            }
        }
        return filtered;
    }

    selecionaDisciplina(value) {
        //console.log(this.plDisciplinasAcademicosService.plDisciplinasAcademicosNameIndex.get(value))
    }

    /*private preparaOptions() {
        this.faculdadesOptions = [
            //{label:' -- Selecione -- ', value:null}
        ]
        for (var i = 0; i < this.faculdades.length; i++) {
            this.faculdadesOptions.push({label:this.faculdades[i].sigla, value: this.faculdades[i]});
        }
    }*/

    checaIfChargedSala() {
        //http://suporte-ead-ms/salas/create/133/0623/04000564/P2
        var pars = null;
        this.route.params.subscribe(p => {
            if (p.hasOwnProperty('periodoLetivoKey') &&
                p.hasOwnProperty('codigoCurso') &&
                p.hasOwnProperty('codigoDisciplina') &&
                p.hasOwnProperty('salaTurma')) {
                    pars = p;
                    return pars;
                }
        });
        return pars;
    }

    ngOnInit() {
        jQuery('#dialogMensagem').modal('show');
        this.periodoLetivoService.getPeriodoLetivos()
            .then (response => {
                this.usuarioService.listaUsuariosCriaSala()
                    .then(response => {
                        this.usuarios = response;
                        this.faculdadeService.listar()
                            .then(response => {
                                this.salasService.getObjetivosSalas()
                                    .then(response => {
                                        this.salasService.getModalidades()
                                            .then(response => {
                                                this.salasService.preparaCreate()
                                                    .then(r => {
                                                        this.sala = r;
                                                        this.nome_professor_temp = this.sala.nome_professor;
                                                        //this.preparaOptions();
                                                        var chargeParams = this.checaIfChargedSala();
                                                        if (chargeParams) {
                                                            this.salasService.chargeSala(this.sala, chargeParams.periodoLetivoKey,chargeParams.codigoCurso, chargeParams.codigoDisciplina, chargeParams.salaTurma)
                                                                .then(r => {
                                                                    //jQuery('#dialogMensagem').modal('hide');
                                                                    if (this.sala.curso) {
                                                                        this.faculdadeSelecionadaId = (<Faculdade>(<Curso>this.sala.curso).faculdade).id.toString();
                                                                        this.faculdadeTemp = this.faculdadeService.faculdadesIndex.get(this.faculdadeSelecionadaId);
                                                                        this.sala.curso = (<Curso>this.sala.curso).id;
                                                                        this.selecionaCurso(this.sala.nome_sala)
                                                                    }
                                                                    else
                                                                        jQuery('#dialogMensagem').modal('hide');
                                                                    this.editavel = true;
                                                                }).catch(response => {
                                                                    this.erroAviso = true;
                                                                    this.aviso = this.erroHttp(response);
                                                                })
                                                        }
                                                        else {
                                                            jQuery('#dialogMensagem').modal('hide');
                                                            this.editavel = true;
                                                        }
                                                    }).catch(response => {
                                                        this.status = this.ERROR;
                                                        this.erroAviso = true;
                                                        this.mensagemDialog = this.erroHttp(response);
                                                    })
                                            })
                                            .catch(response => {
                                                this.status = this.ERROR;
                                                this.erroAviso = true;
                                                this.mensagemDialog = this.erroHttp(response);
                                                console.log(response)
                                            })
                                    })
                                    .catch(response => {
                                        this.status = this.ERROR;
                                        this.erroAviso = true;
                                        this.mensagemDialog = this.erroHttp(response);
                                        console.log(response)
                                    })

                            })
                            .catch(response => {
                                this.status = this.ERROR;
                                this.erroAviso = true;
                                this.mensagemDialog = this.erroHttp(response);
                                console.log(response)
                            })
                    })
                    .catch(response => {
                        this.status = this.ERROR;
                        this.erroAviso = true;
                        this.mensagemDialog = this.erroHttp(response);
                        console.log(response)
                    })
            })
            .catch(response => {
                this.status = this.ERROR;
                this.erroAviso = true;
                this.mensagemDialog = this.erroHttp(response);
                console.log(response)
            })
    }

}
