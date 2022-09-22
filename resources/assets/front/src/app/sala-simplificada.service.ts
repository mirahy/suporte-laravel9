import { Injectable } from '@angular/core';
import { Http } from '@angular/http';
import { ArrayIndexador } from './array-indexador';
import { CursosService } from './cursos.service';
import { LoteSalasSimplificadoService } from './lote-salas-simplificado.service';
import { LoteSalasSimplificado } from './lote-salas-simplificado/lote-salas-simplificado';
import { PeriodoLetivosService } from './periodo-letivos.service';
import { PeriodoLetivo } from './periodo-letivos/periodo-letivo';
import { PlDisciplinasAcademicos } from './pl-disciplinas-academicos/pl-disciplinas-academicos';
import { SalaSimplificada } from './sala-simplificada/sala-simplificada';

@Injectable()
export class SalaSimplificadaService {

	salasSimplificadas: Array<SalaSimplificada> = [];
	salasSimplificadasIndex: ArrayIndexador<SalaSimplificada> = null;
	salaSimplificadaSelecionada: SalaSimplificada = SalaSimplificada.generate();

	constructor(private http: Http, private loteSalasSimplificadoService: LoteSalasSimplificadoService, 
        private cursosService: CursosService, private periodoLetivosService: PeriodoLetivosService) { }

    get loteSalasSimplificadoSelecionado () : LoteSalasSimplificado {
        return this.loteSalasSimplificadoService.loteSalasSimplificadoSelecionada;
    }

    reset () {
        this.salasSimplificadas = [];
        this.salasSimplificadasIndex = null;
        this.salaSimplificadaSelecionada = SalaSimplificada.generate();
    }

    list (postSelectId?) {
        return this.http.get("/salas-simplificadas/list-lote/"+ this.loteSalasSimplificadoSelecionado.id)
            .toPromise()
            .then(response => {
                this.salasSimplificadas = SalaSimplificada.generateListPlus(response.json(), this.cursosService.cursosIndex, 
                    this.periodoLetivosService.periodoLetivosIdIndex, this.loteSalasSimplificadoSelecionado);
                this.salasSimplificadasIndex = new ArrayIndexador<SalaSimplificada>(this.salasSimplificadas);
                if (postSelectId)
                    this.salaSimplificadaSelecionada = this.salasSimplificadasIndex.get(postSelectId).clone();
                return this.salasSimplificadas;
            });
    } 

	create(salaSimplificadaPost) {
        return this.http.post("/salas-simplificadas",salaSimplificadaPost)
            .toPromise()
            .then(response => {
                return this.list(response.json());
            });
    }

	async createAll(plDisciplinasAcademicos:Array<PlDisciplinasAcademicos>) {
        var _this = this;
        var salasIds = [];
        var createUno = function (salaSimplificadaPost) {
            return _this.http.post("/salas-simplificadas",salaSimplificadaPost)
                .toPromise()
        }
        for (var i in plDisciplinasAcademicos) {
            var salaSimplificadaPost = SalaSimplificada.generatePostSalaSimplificada(0,plDisciplinasAcademicos[i],'',
                <PeriodoLetivo> plDisciplinasAcademicos[i].periodo_letivo,this.loteSalasSimplificadoService.loteSalasSimplificadoSelecionada.id);
            var salaId = await createUno(salaSimplificadaPost);
            salasIds.push(salaId.text())
        }
        return this.list();
    }

	update(salaSimplificadaPost) {
        return this.http.put("/salas-simplificadas/"+salaSimplificadaPost.id, salaSimplificadaPost)
            .toPromise()
            .then(response => {
                return this.list(response.json());
            });
    }

	refreshSala(salaId) {
        return this.http.get("/salas-simplificadas/refresh/"+salaId)
            .toPromise()
            .then(response => {
                return this.list(response.json());
            });
    }

	delete(id) {
        return this.http.delete("/salas-simplificadas/"+id)
            .toPromise()
            .then(response => {
                return this.list();
            });
    }

    exportarEstudantes(salaId) {
        return this.http.get("/salas-simplificadas/estudantes/"+salaId)
            .toPromise()
            .then(response => {
                return response.text();
            });
    }

    executarRestauracaoSala(salaId, macroId, courseImportId?) {
        return this.http.get ('/salas-simplificadas/autorestore/' + salaId + "/" + macroId + ( courseImportId ? "/" + courseImportId : '')).toPromise()
            .then (response => {
                return response.text();
            })
    }

    getMacro (salaId) {
        return this.http.get("/salas-simplificadas/macro/"+salaId)
            .toPromise()
            .then(response => {
                return response.json();
            });
    }
}
