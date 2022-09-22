import { Injectable } from '@angular/core';
import { Http } from '@angular/http';
import { ArrayIndexador } from './array-indexador';
import { PeriodoLetivo } from './periodo-letivos/periodo-letivo';

@Injectable(
    //{providedIn: 'root'}
)
export class PeriodoLetivosService {

    periodoLetivos: Array<PeriodoLetivo> = [];
    periodoLetivosIndex = {};
    periodoLetivosIdIndex: ArrayIndexador<PeriodoLetivo>;
    periodoLetivosKeyIndex: ArrayIndexador<PeriodoLetivo>;
    periodoLetivosNameIndex: ArrayIndexador<PeriodoLetivo>;

    constructor(private http: Http) { }

    getPeriodoLetivos() {
        return this.http.get("/periodo-letivos/all").toPromise()
            .then(response => {
                var pls = response.json();
                this.periodoLetivos = [];
                this.periodoLetivosIndex = {};
                for (var i in pls) {
                    var pl = new PeriodoLetivo(pls[i]);
                    this.periodoLetivos.push(pl);
                    this.periodoLetivosIndex[pl.id] = i;
                }
                this.periodoLetivosIdIndex = new ArrayIndexador<PeriodoLetivo>(this.periodoLetivos);
                this.periodoLetivosKeyIndex = new ArrayIndexador<PeriodoLetivo>(this.periodoLetivos,'id_sigecad');
                this.periodoLetivosNameIndex = new ArrayIndexador<PeriodoLetivo>(this.periodoLetivos,'nome');
                return this.periodoLetivos;
            });
    }

    getPeriodoLetivosSigecad():Promise<Array<PeriodoLetivo>> {
        return this.http.get("/periodo-letivos/sigecad").toPromise()
            .then(response => {
                var pls = response.json();
                var periodoLetivosSigecad:Array<PeriodoLetivo> = [];
                for (var i in pls) {
                    var pl = new PeriodoLetivo(pls[i]);
                    periodoLetivosSigecad.push(pl);
                }
                return periodoLetivosSigecad;
            });
    }

    getPeriodoLetivoIdPadrao () {
        return this.http.get("/periodo-letivos/id-padrao").toPromise()
            .then(response => {
                return response.json();
            });
    }

    createUpdate(pl) {
        if (pl.id) {
            return this.http.put("/periodo-letivos/" + pl.id, pl).toPromise()
                .then(response => {
                    this.getPeriodoLetivos();
                    return response.json();
                });
        }
        else {
            return this.http.post("/periodo-letivos/", pl).toPromise()
                .then(response => {
                    this.getPeriodoLetivos();
                    return response.json();
                });
        }
    }

    delete(pl) {
        return this.http.delete("/periodo-letivos/" + pl.id).toPromise()
            .then(response => {
                this.getPeriodoLetivos();
                return response.json();
            });
    }
}
