import { Usuario } from '../usuarios/usuario';

export class Recurso {
    id:number;
    nome:string;
    descricao:string;

    gestores:Array<Usuario>

    constructor (id:number|object, nome?:string, descricao?:string) {
        if (typeof id == "object") {
            this.id = id['id'];
            this.nome = id['nome'];
            this.descricao = id['descricao'];
        }
        else {
            this.id = id;
            this.nome = nome;
            this.descricao = descricao;
        }
        this.gestores = [];
    }
}