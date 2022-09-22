import { Pipe, PipeTransform } from '@angular/core';
import { Sala } from './salas/sala';
import { SalaOld } from './salas-old/sala-old';

@Pipe({
    name: 'filtroSalas'
})
export class FiltroSalasPipe implements PipeTransform {

    transform(value: Array<Sala | SalaOld>, criteria: string): Array<Sala | SalaOld> {
        if (criteria == "")
            return value;

        var salas: Array<Sala | SalaOld> = [];

        for (var i in value) {
            if (this.filtro(value[i], criteria.toUpperCase()))
                salas.push(value[i])
        }
        return salas;
    }

    filtro(sala: Sala | SalaOld, criteria: string): boolean {
        if (sala instanceof Sala) {
            if (sala.nome_sala.toUpperCase().search(criteria) >= 0 ||
                sala.nome_professor.toUpperCase().search(criteria) >= 0 ||
                sala.curso.faculdade.nome.toUpperCase().search(criteria) >= 0 ||
                sala.curso.nome.toUpperCase().search(criteria) >= 0 ||
                sala.status.descricao.toUpperCase().startsWith(criteria))
                return true;
        }
        else if (sala instanceof SalaOld) {
            if (sala.nome_sala.toUpperCase().search(criteria) >= 0 ||
                sala.nome_professor.toUpperCase().search(criteria) >= 0 ||
                sala.faculdade.toUpperCase().search(criteria) >= 0 ||
                sala.curso.toUpperCase().search(criteria) >= 0 ||
                sala.status.descricao.toUpperCase().startsWith(criteria))
                return true;
        }
        
        return false;
    }

}
