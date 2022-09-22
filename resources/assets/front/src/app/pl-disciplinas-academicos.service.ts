import { Injectable } from '@angular/core';
import { Http } from '@angular/http';
import { PlDisciplinasAcademicos } from './pl-disciplinas-academicos/pl-disciplinas-academicos';
import { ArrayIndexador } from './array-indexador';
import { Estudante } from './pl-disciplinas-academicos/estudante';

@Injectable()
export class PlDisciplinasAcademicosService {

    plDisciplinasAcademicos:Array<PlDisciplinasAcademicos> = [];
    plDisciplinasAcademicosIndex:ArrayIndexador<PlDisciplinasAcademicos> = null;
    plDisciplinasAcademicosNameIndex:ArrayIndexador<PlDisciplinasAcademicos> = null;
    //plDisciplinasAcademicosCodigoIndex:ArrayIndexador<PlDisciplinasAcademicos> = null;

    constructor(private http: Http) { }

    getPlDisciplinasAcademicos(periodoLetivoId, cursoId, cursosKeyIndex?, periodoLetivosNomeIndex?) {
        return this.http.get("/pl-disciplinas-academicos/find/" + periodoLetivoId + "/" + cursoId).toPromise()
            .then(response => {
                if (cursosKeyIndex)
                    this.plDisciplinasAcademicos = PlDisciplinasAcademicos.generateListPlus(response.json(), cursosKeyIndex, periodoLetivosNomeIndex);
                else 
                    this.plDisciplinasAcademicos = PlDisciplinasAcademicos.generateList(response.json());
                this.plDisciplinasAcademicos = this.difereTurmas(this.plDisciplinasAcademicos);
                this.plDisciplinasAcademicosNameIndex = new ArrayIndexador<PlDisciplinasAcademicos>(this.plDisciplinasAcademicos,"disciplina");
                //this.plDisciplinasAcademicosNameIndex = new ArrayIndexador<PlDisciplinasAcademicos>(this.plDisciplinasAcademicos,"disciplina_key");
                this.plDisciplinasAcademicosIndex = new ArrayIndexador<PlDisciplinasAcademicos>(this.plDisciplinasAcademicos);
                return this.plDisciplinasAcademicosIndex;
            });
    }

    private difereTurmas(plDAList:Array<PlDisciplinasAcademicos>) {
        var indices = {};//new ArrayIndexador<PlDisciplinasAcademicos>(plDAList, "disciplina");
        var pares = {};
        for (var i = 0; i < plDAList.length; i++) {
            if (indices.hasOwnProperty(plDAList[i]['disciplina'])) {
                if (pares.hasOwnProperty (plDAList[i]['disciplina'])) 
                    pares[plDAList[i]['disciplina']].push(i);
                else 
                    pares[plDAList[i]['disciplina']] = [indices[plDAList[i]['disciplina']], i];
            }
            else
                indices[plDAList[i]['disciplina']] = i;
        }

        for (var j in pares) {
            for (var k in pares[j]) {
                plDAList[pares[j][k]].disciplina = j + " - " + plDAList[pares[j][k]].turma_nome;
            }
        }
        return plDAList;
    }

    criarAlterarDisciplina(plDisciplinasAcademicos:PlDisciplinasAcademicos) {
        var res = function (response) {
            if (plDisciplinasAcademicos.id)
            this.plDisciplinasAcademicos = PlDisciplinasAcademicos.generateList(response.json());
            this.plDisciplinasAcademicosIndex = new ArrayIndexador<PlDisciplinasAcademicos>(this.plDisciplinasAcademicos);
            return this.plDisciplinasAcademicosIndex;
        }
        if (plDisciplinasAcademicos.id)
            return this.http.put("/pl-disciplinas-academicos/"+plDisciplinasAcademicos.id, plDisciplinasAcademicos.toForm()).toPromise()
                .then(r => {
                    var plc = r.json()
                    this.plDisciplinasAcademicosIndex.get(plc.id).disciplina = plc.disciplina;
                    return plc.id;
                });
        else
            return this.http.post("/pl-disciplinas-academicos", plDisciplinasAcademicos.toForm()).toPromise()
                .then(r => {
                    var plc = r.json();
                    this.plDisciplinasAcademicosIndex.add(new PlDisciplinasAcademicos(plc.id, plc.curso_id, plc.periodo_letivo_id, plc.disciplina, []));
                    console.log(this.plDisciplinasAcademicos)
                    return plc.id;
                });
    }

    removeDisciplina(plcId) {
        return this.http.delete("/pl-disciplinas-academicos/"+ plcId).toPromise()
            .then(response => {
                this.plDisciplinasAcademicosIndex.remove(plcId);
                return plcId;
            });
    }

    getEstudantes(plDisciplinasAcademicosId, isListaCompleta:boolean):Promise<Array<Estudante>> {
        return this.http.get("/pl-disciplinas-academicos/estudantes/" + plDisciplinasAcademicosId + (isListaCompleta ?  "/" + isListaCompleta : "") ).toPromise()
            .then(response => {
                var estudantes = Estudante.converteJSONParaEstudantes(response.text());
                return estudantes;
            });
    }

    setEstudantes(plDisciplinasAcademicosId, estudantes:Array<Estudante>):Promise<Array<Estudante>> {
        return this.http.put("/pl-disciplinas-academicos/estudantes/" + plDisciplinasAcademicosId, {'estudantes' : Estudante.converteEstudantesParaJSON(estudantes)}).toPromise()
            .then(response => {
                var estudantes = Estudante.converteJSONParaEstudantes(response.text());
                return estudantes;
            });
    }

    uploadFileEstudantes(periodoLetivoId, file:File) {
        const formData: FormData = new FormData();
        if (periodoLetivoId && file) {
            formData.append('arquivo', file, file.name);
            return this.http.post("/pl-disciplinas-academicos/estudantes/" + periodoLetivoId, formData).toPromise()
                .then(r => {
                    return r.json();
                });
        }
    }

    
    getCursosSigecad(periodoLetivoId) {
        return this.http.get("/pl-disciplinas-academicos/carrega-cursos-sigecad/" + periodoLetivoId).toPromise()
            .then(response => {
                return response.json();
            });
    }

    getDisciplinasCursoSigecad(periodoLetivoIdSigecad, siglaFaculdade, codCurso){
        return this.http.get("/pl-disciplinas-academicos/disciplinas-curso-sigecad/" + periodoLetivoIdSigecad + "/" + siglaFaculdade + "/" + codCurso).toPromise()
            .then(response => {
                return response.json();
            });
    }

    getAcademicosDisciplinasSigecad(codDisciplina, periodoLetivoIdSigecad, turmaId, turmaNome, salaId){
        return this.http.get("/pl-disciplinas-academicos/academicos-disciplinas-sigecad/" + codDisciplina + "/" + periodoLetivoIdSigecad + "/" + turmaId + "/" + turmaNome + "/" + salaId).toPromise()
            .then(response => {
                //return response.json();
                var estudantes = Estudante.converteJSONParaEstudantes( Estudante.converteEstudantesParaJSON( response.json() ) );
                return estudantes;
            });
    }
}
