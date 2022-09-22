import { Injectable } from '@angular/core';
import { Http } from '@angular/http';
import { ArrayIndexador } from './array-indexador';
import { Curso } from './cursos/curso';
import { Faculdade } from './faculdades/faculdade';
import { LoteSalas } from './lote-salas/lote-salas';
import { PeriodoLetivo } from './periodo-letivos/periodo-letivo';
import { Sala } from './salas/sala';
import { Status } from './status';

@Injectable()
export class LoteSalasService {

    constructor(private http: Http) { }

    lotesSalasList: Array<LoteSalas> = [];

    listar(periodoLetivoIndex:ArrayIndexador<PeriodoLetivo>, faculdadeIndex:ArrayIndexador<Faculdade>, cursoIndex:ArrayIndexador<Curso>) {
        return this.http.get("/lote-salas/all")
            .toPromise()
            .then(response => {
                this.lotesSalasList = LoteSalas.generateListPlus(response.json(), periodoLetivoIndex, faculdadeIndex, cursoIndex);
                return this.lotesSalasList;
            });
    }

    criaLoteSalas(loteSalas:LoteSalas) {
        var salasPost = [];
        for(var i = 0;i < loteSalas.salas.length; i++) {
            salasPost.push(this.getPostSala(loteSalas.salas[i]));
        }
        var postLoteSalas = {
            curso_id: loteSalas.curso,
            descricao: loteSalas.descricao,
            faculdade_id: loteSalas.faculdade,
            periodo_letivo_id: loteSalas.periodo_letivo,
            salas : salasPost
        }
        console.log(postLoteSalas)
        return this.http.post("/lote-salas", postLoteSalas)
            .toPromise()
            .then(response => {
                //this.lotesSalasList = LoteSalas.generateListPlus(response.json(), periodoLetivoIndex, faculdadeIndex, cursoIndex);
                return response.json();
            });
    }

    getSalasDoLote(loteSalas:LoteSalas, periodoLetivoIndex:ArrayIndexador<PeriodoLetivo>, faculdadeIndex:ArrayIndexador<Faculdade>, cursoIndex:ArrayIndexador<Curso>) {
        return this.http.get("/lote-salas/"+ loteSalas.id)
            .toPromise()
                .then(response => {
                    return response.json();
                });
    }

    executaExportacoes(loteSalas:LoteSalas) {
        return this.http.get("/lote-salas/exportacao/"+ loteSalas.id)
            .toPromise()
                .then(response => {
                    return response.text();
                });
    }

    insereEstudantes(loteSalas:LoteSalas) {
        return this.http.get("/lote-salas/estudantes/"+ loteSalas.id)
            .toPromise()
                .then(response => {
                    return response.text();
                });
    }

    updateMacro (sala: Sala, macroId) {
        return this.http.put("/lote-salas/macro", {macro_id: macroId, sala_id: sala.id})
            .toPromise()
                .then(response => {
                    return response.json();
                });
    }
    
    removeLoteSalas(loteSalas:LoteSalas) {
        return this.http.delete("/lote-salas/"+ loteSalas.id)
            .toPromise()
                .then(response => {
                    return response.json();
                });
    }

    private getPostSala (s:Sala) {
        var sala = Sala.geraNovaSala();
        //sala.id = s.id;
        //sala.email = s.email;
        sala.curso =  s.curso.id;
        //sala.mensagem = s.mensagem;
        sala.nome_professor = s.nome_professor;
        sala.cpf_professor = s.cpf_professor;
        sala.nome_sala = s.nome_sala;
        sala.modalidade = s.modalidade;
        sala.objetivo_sala = s.objetivo_sala;
        sala.observacao = s.observacao;
        sala.senha_aluno = s.senha_aluno;
        sala.estudantes = s.estudantes;
        sala.periodo_letivo_id =  s.periodo_letivo_id;
        sala.carga_horaria_total_disciplina = s.carga_horaria_total_disciplina;
        sala.avaliacao = s.avaliacao;
        sala.turma_nome = s.turma_nome;
        sala.turma_id = s.turma_id;
        sala.periodo_letivo_key = s.periodo_letivo_key;
        sala.curso_key = s.curso_key;
        sala.disciplina_key = s.disciplina_key;
        //sala.status = status;
        //sala.solicitante_id = s.solicitante_id;   
        return sala;
    }
}
