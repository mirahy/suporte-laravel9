export class UnidadeOrganizacional {

    id: number;
    nome:string;
    valor:string;

    
	constructor(id:(number|any), nome?:string, valor?:string) {
        if (typeof id == "number") {
            this.id = id;
            this.nome = nome;
            this.valor = valor;
        }
        else {
            this.id = id['id'];
            this.nome = id['nome'];
            this.valor = id['valor'];
        }
    }
    
    static generate () {
        return new UnidadeOrganizacional(0, "", "");
    }

    static generateList(list) {
        var unidadesOrganizacionais:Array<UnidadeOrganizacional> = [];
        for(var i = 0; i < list.length; i++) {
            var ou = new UnidadeOrganizacional(list[i]);
            unidadesOrganizacionais.push(ou);
        }
        return unidadesOrganizacionais;
    }
    /*static generateListPlus(list, macros, macrosIndex) {
        var unidadesOrganizacionais:Array<UnidadeOrganizacional> = [];
        for(var i = 0; i < list.length; i++) {
            var sm = new UnidadeOrganizacional(list[i]);
            sm.macro_padrao = macros[ macrosIndex[<number>sm.macro_padrao]];
            unidadesOrganizacionais.push(sm);
        }
        return unidadesOrganizacionais;
    }*/

    
    clone() {
        return new UnidadeOrganizacional(this.id, this.nome, this.valor);
    }
}