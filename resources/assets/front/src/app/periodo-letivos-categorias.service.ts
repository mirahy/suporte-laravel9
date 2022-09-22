import { Injectable } from '@angular/core';
import { Http } from '@angular/http';
import { PeriodoLetivoCategoria } from './periodo-letivos-categorias/periodo-letivo-categoria';
import { ArrayIndexador } from './array-indexador';

@Injectable()
export class PeriodoLetivosCategoriasService {

    periodoLetivosCategorias:Array<PeriodoLetivoCategoria> = [];
    periodoLetivosCategoriasIndex:ArrayIndexador<PeriodoLetivoCategoria> = null;

    constructor(private http: Http) { }

    getPeriodoLetivosCategrias(periodoLetivoId:number) {
        return this.http.get("/periodo-letivos-categorias/all/"+periodoLetivoId).toPromise()
            .then(response => {
                var plcs = response.json();

                this.periodoLetivosCategorias = PeriodoLetivoCategoria.generateList(response.json());
                this.periodoLetivosCategoriasIndex = new ArrayIndexador<PeriodoLetivoCategoria>(this.periodoLetivosCategorias,"curso");
                return this.periodoLetivosCategoriasIndex;
            });
    }

    setCategoriaId(periodoLetivoId, cursoId, categoriaId) {
        return this.http.post("/periodo-letivos-categorias",{'periodo_letivo_id': periodoLetivoId, 'curso_id': cursoId, 'categoria_id': categoriaId}).toPromise()
            .then(response => {
                this.periodoLetivosCategoriasIndex.get(cursoId).categoria_id = categoriaId;
                return categoriaId;
            });
    }
}
