import { Curso } from '../cursos/curso';
import { Persistente } from '../persistente';

export class Faculdade implements Persistente{
    id: number;
    sigla:string;
    nome:string;
    auto_increment_ref:number;
    ativo:boolean;

    cursos:Array<Curso>;
    
    constructor(id:(number|any), sigla?:string, nome?:string, auto_increment_ref?:number, ativo?:boolean) {
        if (typeof id == "number") {
            this.id = id;
            this.sigla = sigla;
            this.nome = nome;
            this.auto_increment_ref = auto_increment_ref;
            this.ativo = ativo;
            this.cursos = [];
        }
        else {
            this.id = parseInt(id['id']);
            this.sigla = id['sigla'];
            this.nome = id['nome'];
            this.auto_increment_ref = id['auto_increment_ref'];
            this.ativo = id['ativo'] ? (eval (id['ativo']) ? true : false ) : false;
            this.cursos = [];
        }
    }
    
    static generateList(list) {
        var faculdades:Array<Faculdade> = [];
        for(var i = 0; i < list.length; i++) {
            var fac = new Faculdade(list[i]);
            var cursos = Curso.generateList(list[i].cursos, fac);
            fac.cursos = cursos;
            faculdades.push(fac);
        }
        return faculdades;
    }

    static generateFaculdade() {
        return new Faculdade(0,"","",null,true);
    }
    
    clone() {
        var f = new Faculdade(this.id, this.sigla, this.nome, this.auto_increment_ref, this.ativo);
        f.cursos = this.cursos;
        return f;
    }
}