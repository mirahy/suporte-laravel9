import { Injectable } from '@angular/core';
import { Http } from '@angular/http';
import { Status } from './status';
import { Sala } from './salas/sala';
import { CursosService } from './cursos.service';
import { PlDisciplinasAcademicos } from './pl-disciplinas-academicos/pl-disciplinas-academicos';
import { PeriodoLetivo } from './periodo-letivos/periodo-letivo';
import { PeriodoLetivosService } from './periodo-letivos.service';
import { ServidoresMoodleService } from './servidores-moodle.service';

@Injectable(
  //{ providedIn: 'root'}
)
export class SalasService {

  salas: Array<Sala>;
  modalidades: Array<any>;
  objetivosSalas: Array<any>;


  constructor(private http: Http, private cursosService: CursosService, private periodoLetivoService: PeriodoLetivosService,
              private servidoresMoodleService: ServidoresMoodleService) { }

  atualizarSala(sala: Sala): Promise<boolean> {
    return this.http.post('salas/' + sala.id, sala)
      .toPromise()
      .then(response => {
        this.salas = this.salas.slice(0);
        for (var i in this.salas)
          if (this.salas[i].id == sala.id) {
            this.salas[i] = sala;
            if (typeof sala.curso == 'number' || typeof sala.curso == 'string') {
              this.salas[i].curso = this.cursosService.cursosIndex.get(sala.curso);
            }
            return null;
          }
        return response;
      })
      .catch(response => {
        return response;
      });

  }

  listar() {
    return this.http.get("/salas/listar")
      .toPromise()
      .then(response => {
        this.salas = Sala.generateListPlus(response.json().reverse(), this.cursosService.cursosIndex);
        /*this.salasIndex = {};
        var ss = response.json();
        for (var i = 0; i < ss.length; i++) {
            var sala = this.criaSala(ss[i]);
            this.salasIndex[sala.id] = sala;
            this.salas.push( sala );
        }
        this.salas.sort(this.sortSalas);*/
        return this.salas;
      });
  }

  getMensagemSala(sala: Sala) {
    return this.http.get("/salas/mensagem/" + sala.id)
      .toPromise()
      .then(response => {
        return response.text();
      });
  }

  getSalaMoodle(id, sala) {

    return this.http.post("/salas/sala-moodle/" + id, sala)
      .toPromise()
      .then(response => {
        return response;
      });

  }

  statusSala(sala: Sala, status: string, mensagem: string): Promise<any> {
    return this.http.patch('/salas/status/' + sala.id, { status: status, mensagem: mensagem })
      .toPromise()
      .then(response => {
        var s = response.json();
        sala.status = s.status;
        return null;
      })
      .catch(response => {
        return response;
      });
  }
  preparaCreate() {
    return this.http.get("/salas/preparacreate").toPromise()
      .then(response => {
        var r = response.json();
        var s = Sala.geraNovaSala();
        s.nome_professor = r.nome_professor;
        s.email = r.email;
        s.periodo_letivo_id = r.periodo_letivo_id;
        return s;
      });
  }

  aplicarPlDisciplina(sala: Sala, plda: PlDisciplinasAcademicos): Sala {
    sala.carga_horaria_total_disciplina = plda.carga_horaria_total_disciplina;
    sala.turma_nome = plda.turma_nome;
    sala.avaliacao = plda.avaliacao;
    sala.turma_id = plda.turma_id;
    if (typeof plda.periodo_letivo == 'object' && plda.periodo_letivo instanceof PeriodoLetivo)
      sala.periodo_letivo_key = plda.periodo_letivo.id_sigecad;
    else
      sala.periodo_letivo_key = plda.periodo_letivo.toString();
    //sala.curso_key = plda.curso;
    sala.disciplina_key = plda.disciplina_key;
    return sala;
  }

  create(sala) {
    return this.http.post("/salas/", sala).toPromise()
      .then(response => {
        return response.json();
      });
  }
  executarRestauracaoAutomatica(sala, courseImportId?) {
    return this.http.post('/salas/autorestore/' + sala.id, { sala_moodle_id: sala.sala_moodle_id, macro_id: sala.macro_id, courseImportId: courseImportId }).toPromise()
      .then(response => {
        return response.text();
      })
  }
  exportarEstudantesMoodle(sala) {
    return this.http.post('/salas/autorestore-estudantes/' + sala.id, { sala_moodle_id: sala.sala_moodle_id, macro_id: sala.macro_id }).toPromise()
      .then(response => {
        return response.text();
      })
  }
  getModalidades() {
    return this.http.get('/salas/modalidades').toPromise()
      .then(response => {
        var m = response.json();
        this.modalidades = m;
        return m
      })
  }
  getObjetivosSalas() {
    return this.http.get('/salas/objetivos').toPromise()
      .then(response => {
        var o = response.json();
        this.objetivosSalas = o;
        return o;
      })
  }

  convertCreatedSala(s, sala?: Sala, status?: Status) {
    if (!sala)
      sala = Sala.geraNovaSala();
    else
      sala.id = s.id;
    sala.email = s.email;
    sala.curso = this.cursosService.cursosIndex.get(s.curso_id);
    //sala.mensagem = s.mensagem;
    sala.nome_professor = s.nome_professor;
    sala.nome_sala = s.nome_sala;
    sala.modalidade = s.modalidade;
    sala.objetivo_sala = s.objetivo_sala;
    sala.observacao = s.observacao;
    sala.link_backup_moodle = s.link_backup_moodle;
    sala.senha_aluno = s.senha_aluno;
    //sala.estudantes = s.estudantes;
    sala.periodo_letivo_id = s.periodo_letivo_id;
    sala.carga_horaria_total_disciplina = s.carga_horaria_total_disciplina;
    sala.avaliacao = s.avaliacao;
    sala.turma_nome = s.turma_nome;
    sala.turma_id = s.turma_id;
    sala.periodo_letivo_key = s.periodo_letivo_key;
    sala.curso_key = sala.curso.curso_key;
    sala.disciplina_key = s.disciplina_key;
    sala.macro_id = s.macro_id;
    sala.status = s.status ? s.status : status;
    //sala.solicitante_id = s.solicitante_id;
    return sala;
  }

  //converte dados brutos da sala do sigecad para Sala da view
  convertChargedSala(s, sala?: Sala, status?: Status) {
    if (!sala)
      sala = Sala.geraNovaSala();
    //sala.id = s.id;
    //sala.email = s.email;
    if (s.hasOwnProperty('codigo_curso'))
      sala.curso = this.cursosService.cursosKeyIndex.get(s.codigo_curso);
    else {
      sala.curso = this.cursosService.cursosIndex.get(s.curso_id);
      s.codigo_curso = sala.curso.curso_key;
    }
    //sala.mensagem = s.mensagem;
    //sala.nome_professor = s.nome_professor;
    sala.nome_sala = s.nome_disciplina;
    //sala.modalidade = s.modalidade;
    //sala.objetivo_sala = s.objetivo_sala;
    //sala.observacao = s.observacao;
    //sala.link_backup_moodle = s.link_backup_moodle;
    //sala.senha_aluno = s.senha_aluno;
    //sala.estudantes = s.estudantes;
    //sala.periodo_letivo_id =  this.periodoLetivoService.periodoLetivosKeyIndex.get( s.periodo_letivo_id ).id.toString();
    sala.periodo_letivo_id = this.periodoLetivoService.periodoLetivosNameIndex.get(s.periodo_letivo).id.toString();
    sala.carga_horaria_total_disciplina = s.carga_horaria_total_disciplina;
    sala.avaliacao = s.avaliacao;
    sala.turma_nome = s.turma_nome;
    sala.turma_id = s.turma_id;
    sala.periodo_letivo_key = s.periodo_letivo_id;
    sala.curso_key = s.codigo_curso;
    sala.disciplina_key = s.codigo_disciplina;
    if (status)
      sala.status = status;
    //sala.solicitante_id = s.solicitante_id;
    return sala;
  }
  chargeSala(sala: Sala, plKey, codigoCurso, codigoDiscoplina, salaTurma) {
    return this.http.get('/salas/charge/' + plKey + "/" + codigoCurso + "/" + codigoDiscoplina + "/" + salaTurma).toPromise()
      .then(response => {
        if (response.text()) {
          var s = response.json();
          this.convertChargedSala(s, sala);
        }
        return sala;
      })
  }

  getSufixoNomeSala() {
    return this.http.get('/sufixonome').toPromise()
      .then(response => {
        return response.text();
      })
  }

  validaLinkMoodle(link: string) {
    return this.servidoresMoodleService.getLinksServidoresMoodle()
    .then(results =>{
      link = link ? link.replace(/\s+/g, ''): "";
      let retorno = {};
      let links;
      let linksValid =  false;
      let msgLink = "";
      links = results;
      links.forEach(element => {
        if (link.includes(element + '/course/view.php?id=')){
          linksValid = true;
        }
        if (link.includes(element) && !linksValid){
          msgLink = element + '/course/view.php?id=***';
        }
      });
      if (link !== "" && link.indexOf("http") == 0 && linksValid) {
        //retorna true se o campo não for vazio e igual a um dos links acima
        retorno = { status: { value: true } };
        return retorno;
      }
      else if (link !== "") {
        //retorna false se não o campo não for vazio e o link e diferente dos listados
        msgLink = msgLink ? msgLink : 'https://moodle.ead.ufgd.edu.br/course/view.php?id=***'
        retorno = {
          status: { value: false },
          msg: { value: "Link incorreto, favor inserir um link válido, ex.: " + msgLink }
        };
        return retorno;
      } else if (link == "") {
        //retorna true se o campo for vazio
        retorno = { status: { value: true } };
        return retorno;
      }
    })

  }

  getIdLinkMoodle(link: string) {
    // removendo todos os espaços em branco
    link = link ? link.replace(/\s+/g, ''): "";
    let retorno = {};
    let id = "";
    if (link !== "") {
      if (link.indexOf("&") > 0) {
        // url com mais parametros alem do id
        id = link.substring(link.indexOf("view.php?id=") + 12, link.indexOf("&"));
      } else {
        // url somemnte com parametro id
        id = link.substring(link.indexOf("=") + 1);
      }
      if ((link.match(/http/g) || []).length > 1) {
        //campo com mais de 1 url
        retorno = {
          status: { value: false },
          msg: { value: "Link incorreto, favor inserir um link válido, ex.: 'https://moodle.ead.ufgd.edu.br/course/view.php?id=***'" },
          id: { value: "" }
        };
        return retorno;
      }
      if (id == "") {
        retorno = {
          status: { value: false },
          msg: { value: link + "***{O ID NÃO PODE ESTAR VAZIO!!!}***" },
          id: { value: "" }
        };
        return retorno;
      }

    }
    retorno = {
      status: { value: true },
      msg: { value: ""},
      id: { value: id }
    };
    return retorno;
  }

  sendEmail(id) {
    return this.http.post('/salas/sendemail/' + id, id).toPromise()
      .then(response => {
        return response;
      })
      .catch(response => {
        return response;
      });
  }

}
