export class PeriodoLetivo {
    id: number;
    nome:string;
    id_sigecad;
    descricao:string;
    sufixo:string;
    inicio_auto_increment:number;
    ativo: boolean;
    
	constructor(id:(number|any), nome?:string, id_sigecad?, descricao?:string, sufixo?:string, inicio_auto_increment?:number, ativo?:boolean) {
        if (typeof id == "number") {
            this.id = id;
            this.nome = nome;
            this.id_sigecad = id_sigecad;
            this.descricao = descricao;
            this.sufixo = sufixo;
            this.inicio_auto_increment = inicio_auto_increment;
            this.ativo = ativo;
        }
        else {
            this.id = parseInt(id['id']);
            this.nome = id['nome'];
            this.id_sigecad = id['id_sigecad'];
            this.descricao = id['descricao'];
            this.sufixo = id['sufixo'];
            this.inicio_auto_increment = id['inicio_auto_increment'];
            this.ativo = id['ativo'];
        }
    }
    
    static generatePeriodoLetivo() {
        return new PeriodoLetivo(0,"","","","", 0, true);
    }
    
    clone() {
        return new PeriodoLetivo(this.id, this.nome, this.id_sigecad, this.descricao, this.sufixo, this.inicio_auto_increment, this.ativo);
    }
}