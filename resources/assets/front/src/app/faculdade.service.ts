import { Injectable } from '@angular/core';
import { Http } from '@angular/http';
import { Faculdade } from './faculdades/faculdade';
import { ArrayIndexador } from './array-indexador';
import { CursosService } from './cursos.service';

@Injectable()
export class FaculdadeService {

    faculdades:Array<Faculdade> = [];
    faculdadesIndex: ArrayIndexador<Faculdade> = null;
    constructor(private http: Http, private cursosService:CursosService) { }

    listar() {
        return this.http.get("/faculdades/all")
            .toPromise()
            .then(response => {
                this.faculdades = Faculdade.generateList(response.json());
                this.faculdadesIndex = new ArrayIndexador<Faculdade>(this.faculdades);
                this.cursosService.obterCursos(this.faculdades);
                return this.faculdades;
            });
    }

    private getPostFaculdade(faculdade:Faculdade) {
        return {
            id: faculdade.id,
            sigla: faculdade.sigla,
            nome: faculdade.nome,
            auto_increment_ref: faculdade.auto_increment_ref == null ? "" : faculdade.auto_increment_ref,
            ativo: faculdade.ativo
        }
    }

    create(faculdade:Faculdade) {
        return this.http.post("/faculdades",this.getPostFaculdade(faculdade))
            .toPromise()
            .then(response => {
                return this.listar();
            });
    }
    update(faculdade:Faculdade) {
        return this.http.put("/faculdades/"+faculdade.id,this.getPostFaculdade(faculdade))
            .toPromise()
            .then(response => {
                return this.listar();
            });
    }
    delete(faculdade:Faculdade) {
        return this.http.delete("/faculdades/"+faculdade.id)
            .toPromise()
            .then(response => {
                return this.listar();
            });
    }
}
