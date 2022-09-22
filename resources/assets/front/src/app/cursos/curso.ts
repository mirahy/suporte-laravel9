import { Faculdade } from '../faculdades/faculdade';

export class Curso {
    id: number;
    nome:string;
    curso_key: string;
    auto_increment_ref:number;
    faculdade:Faculdade|number;
    ativo:boolean;
    
    
	constructor(id:(number|any), nome?:string, curso_key?:string, auto_increment_ref?:number,faculdade?:Faculdade|number,ativo?:boolean) {
        if (typeof id == "number") {
            this.id = id;
            this.nome = nome;
            this.curso_key = curso_key;
            this.auto_increment_ref = auto_increment_ref;
            this.faculdade = faculdade;
            this.ativo = ativo;
        }
        else {
            this.id = parseInt(id['id']);
            this.nome = id['nome'];
            this.curso_key = id['curso_key'];
            this.auto_increment_ref = id['auto_increment_ref'];
            this.faculdade = id['faculdade_id'];
            this.ativo = id['ativo'] ? (eval (id['ativo']) ? true : false ) : false;
        }
    }
    
    static generateList(list, faculdade?:Faculdade) {
        var cursos:Array<Curso> = [];
        for(var i = 0; i < list.length; i++) {
            var curso = new Curso(list[i]);
            if (faculdade)
                curso.faculdade = faculdade;
            cursos.push(curso);
        }
        return cursos;
    }

    static generateCurso() {
        return new Curso(0,"","",null,0,true);
    }
    
    clone() {
        return new Curso(this.id, this.nome, this.curso_key, this.auto_increment_ref, this.faculdade, this.ativo);
    }
}