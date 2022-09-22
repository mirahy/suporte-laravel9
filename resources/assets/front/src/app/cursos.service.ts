import { Injectable } from '@angular/core';
import { Curso } from './cursos/curso';
import { ArrayIndexador } from './array-indexador';
import { Faculdade } from './faculdades/faculdade';
import { Http } from '@angular/http';

@Injectable()
export class CursosService {

    cursos:Array<Curso> = [];
    cursosIndex:ArrayIndexador<Curso> = null;
    cursosKeyIndex: ArrayIndexador<Curso> = null;
    faculdadeSelecionada:Faculdade = Faculdade.generateFaculdade();

    constructor(private http: Http) { }

    obterCursos(listaFaculdades:Array<Faculdade>) {
        if (listaFaculdades.length) {
            this.cursosIndex = new ArrayIndexador<Curso>(listaFaculdades[0].cursos);
            this.cursosKeyIndex = new ArrayIndexador<Curso>(listaFaculdades[0].cursos,'curso_key');
            for(var i = 1; i < listaFaculdades.length; i++) {
                this.cursosIndex.append(listaFaculdades[i].cursos);
                this.cursosKeyIndex.append(listaFaculdades[i].cursos);
            }
        }
    }

    
    private getPostCurso(curso) {
        return {
            id: curso.id,
            nome: curso.nome,
            curso_key: curso.curso_key,
            auto_increment_ref: curso.auto_increment_ref == null ? "" : curso.auto_increment_ref,
            faculdade_id: (typeof curso.faculdade == 'object' && curso.faculdade instanceof Faculdade ? curso.faculdade.id : curso.faculdade),
            ativo: curso.ativo
        }
    }

    create(curso:Curso) {
        return this.http.post("/cursos",this.getPostCurso(curso))
            .toPromise()
            .then(response => {
                curso.id = response.json().id;
                this.cursosIndex.add(curso);
                (<Faculdade>curso.faculdade).cursos.push(curso);
                return curso;
            });
    }
    update(curso:Curso) {
        return this.http.put("/cursos/"+curso.id,this.getPostCurso(curso))
            .toPromise()
            .then(response => {
                var cursoRef = this.cursosIndex.get(curso.id);
                cursoRef.nome = curso.nome;
                cursoRef.curso_key = curso.curso_key;
                cursoRef.auto_increment_ref = curso.auto_increment_ref;
                cursoRef.ativo = curso.ativo;
                return cursoRef;
            });
    }
    delete(curso:Curso) {
        return this.http.delete("/cursos/"+curso.id)
            .toPromise()
            .then(response => {
                this.cursosIndex.remove(curso);
                var fac = (<Faculdade>curso.faculdade);
                for (var i = 0; i < fac.cursos.length; i++) {
                    if (fac.cursos[i].id == curso.id) {
                        fac.cursos.splice(i,1);
                        break;
                    }
                }
                return curso;
            });
    }
}
