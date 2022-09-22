import { Pipe, PipeTransform } from '@angular/core';
import { Faculdade } from './faculdades/faculdade';
import { Curso } from './cursos/curso';

@Pipe({
    name: 'filtroCursos'
})
export class FiltroCursosPipe implements PipeTransform {

    transform(value: Array<Faculdade>, criteria: string): Array<Faculdade> {
        if (criteria == "")
            return value;

        var faculdades: Array<Faculdade> = [];

        for (var i in value) {
            var faculdade = this.filtroFaculdades(value[i], criteria.toUpperCase())
            if (faculdade)
                faculdades.push(faculdade);
        }
        return faculdades;
    }

    filtroFaculdades (faculdade:Faculdade, criteria) :Faculdade {
        if (faculdade.nome.toUpperCase().search(criteria) >= 0 ||
                faculdade.sigla.toUpperCase().startsWith(criteria))
                return faculdade;

        var cursos: Array<Curso> = [];
        for (var i in faculdade.cursos) {
            var curso = this.filtroCursos(faculdade.cursos[i], criteria.toUpperCase());
            if (this.filtroCursos(faculdade.cursos[i], criteria.toUpperCase()))
                cursos.push(faculdade.cursos[i]);
        }
        if (cursos.length > 0) {
            faculdade = faculdade.clone();
            faculdade.cursos = cursos;
            return faculdade;
        }
        return null;
    }

    filtroCursos (curso:Curso, criteria):boolean {
        if (curso.nome.toUpperCase().search(criteria) >= 0 )
            return true;
        return false;
    }

}
