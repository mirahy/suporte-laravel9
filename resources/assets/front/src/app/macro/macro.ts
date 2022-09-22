import { Arquivo } from '../arquivo/arquivo';
import { Buscador } from '../buscadores/buscador';
import { PeriodoLetivo } from '../periodo-letivos/periodo-letivo';

export class Macro {
    id: number;
    nome:string;
    arquivo: Arquivo;
    periodo_letivo:PeriodoLetivo|number;
    link_servidor_moodle:string;
    buscadores: Array<Buscador>;

    
	constructor(id:(number|any), nome?:string, arquivo?:Arquivo, periodo_letivo?:PeriodoLetivo|number, link_servidor_moodle?:string, buscadores?: Array<Buscador>) {
        if (typeof id == "number") {
            this.id = id;
            this.nome = nome;
            this.arquivo = arquivo == null ? null : new Arquivo (arquivo);
            this.periodo_letivo = periodo_letivo == null ? null : new PeriodoLetivo (periodo_letivo);
            this.link_servidor_moodle = link_servidor_moodle;
            this.buscadores = [];
            for(var i in buscadores) {
                this.buscadores.push(new Buscador(buscadores[i]));
            }
        }
        else {
            this.id = id.id;
            this.nome = id.nome;
            this.arquivo = id.arquivo == null ? null : new Arquivo (id.arquivo);
            this.periodo_letivo = id.periodo_letivo_id == null ? null : id.periodo_letivo_id;
            this.link_servidor_moodle = id.link_servidor_moodle;
            this.buscadores = [];
            for(var i in id.buscadores) {
                this.buscadores.push(new Buscador(id.buscadores[i]));
            }
        }
	}

    static generate() {
        return new Macro(0,"",null,0,"",[]);
    }
    
    clone() {
        return new Macro(this.id, this.nome, this.arquivo, this.periodo_letivo, this.link_servidor_moodle, this.buscadores);
    }
}