export class ServidorMoodle {
    id: number;
    nome:string;
    url:string;
    ativo: boolean;
    
	constructor(id:(number|any), nome?:string, url?:string, ativo?:boolean) {
        if (typeof id == "number") {
            this.id = id;
            this.nome = nome;
            this.url = url;
            this.ativo = ativo;
        }
        else {
            this.id = parseInt(id['id']);
            this.nome = id['nome'];
            this.url = id['url'];
            this.ativo = id['ativo'];
        }
    }
    
    static generateServidorMoodle() {
        return new ServidorMoodle(0,"","", true);
    }
    
    clone() {
        return new ServidorMoodle(this.id, this.nome, this.url, this.ativo);
    }
}