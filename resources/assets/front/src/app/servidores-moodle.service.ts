import { Injectable } from '@angular/core';
import { ServidorMoodle } from './servidores-moodle/servidor-moodle';
import { Http } from '@angular/http';
import { ArrayIndexador } from './array-indexador';
import { escapeIdentifier } from '@angular/compiler/src/output/abstract_emitter';
import { Estudante } from './pl-disciplinas-academicos/estudante';

@Injectable()
export class ServidoresMoodleService {

    constructor(private http: Http) { }

    servidoresMoodle: Array<ServidorMoodle> = [];
    servidoresMoodleIndex: ArrayIndexador<ServidorMoodle> = new ArrayIndexador([]);

    getServidoresMoodle() {
        return this.http.get("/servidores-moodle/all").toPromise()
            .then(response => {
                var sms = response.json();
                this.servidoresMoodle = [];
                this.servidoresMoodleIndex = new ArrayIndexador([]);
                for (var i in sms) {
                    var sm = new ServidorMoodle(sms[i]);
                    this.servidoresMoodle.push(sm);
                    this.servidoresMoodleIndex.add(sm);
                }
                return this.servidoresMoodle;
            });
    }

    getLinksServidoresMoodle () {
        return this.http.get("/servidores-moodle/links").toPromise()
            .then(response => {
                return response.json();
            });
    }

    createUpdate(sm: ServidorMoodle) {
        if (sm.id) {
            return this.http.put("/servidores-moodle/" + sm.id, sm).toPromise()
                .then(response => {
                    this.getServidoresMoodle();
                    return response.json();
                });
        }
        else {
            return this.http.post("/servidores-moodle/", sm).toPromise()
                .then(response => {
                    this.getServidoresMoodle();
                    return response.json();
                });
        }
    }

    delete(sm: ServidorMoodle) {
        return this.http.delete("/servidores-moodle/" + sm.id).toPromise()
            .then(response => {
                this.getServidoresMoodle();
                return response.json();
            });
    }

    exportarEstudantes(estudantes, servidorMoodle, courseId, senhaPadrao) {
        return this.http.post("/formulario-insere-usuarios", {estudantes: estudantes, servidorMoodle: servidorMoodle, courseId:courseId, senhaPadrao:senhaPadrao} ).toPromise()
            .then(response => {
                return response.text();
            });
    }

    getAcademicosDisciplinasSigecad(codDisciplina, periodoLetivoIdSigecad, turmaId, turmaNome){
        return this.http.get("/pl-disciplinas-academicos/academicos-disciplinas-sigecad/" + codDisciplina + "/" + periodoLetivoIdSigecad + "/" + turmaId + "/" + turmaNome).toPromise()
            .then(response => {
                //return response.json();
                var estudantes = Estudante.converteJSONParaEstudantesComSenha( Estudante.converteEstudantesParaJSON( response.json() ) );
                return estudantes;
            });
    }
}
