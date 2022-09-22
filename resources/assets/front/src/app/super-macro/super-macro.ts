import { Macro } from '../macro/macro';

export class SuperMacro {
    id: number;
    descricao:string;
    macro_padrao:number|Macro;

    
	constructor(id:(number|any), descricao?:string, macro_padrao?:(number|Macro)) {
        if (typeof id == "number") {
            this.id = id;
            this.descricao = descricao;
            this.macro_padrao = macro_padrao;
        }
        else {
            this.id = id['id'];
            this.descricao = id['descricao'];
            this.macro_padrao = id['macro_padrao_id'];
        }
    }
    
    static generate () {
        return new SuperMacro(0,"",0);
    }

    static generateList(list) {
        var superMacros:Array<SuperMacro> = [];
        for(var i = 0; i < list.length; i++) {
            var sm = new SuperMacro(list[i]);
            superMacros.push(sm);
        }
        return superMacros;
    }
    static generateListPlus(list, macros, macrosIndex) {
        var superMacros:Array<SuperMacro> = [];
        for(var i = 0; i < list.length; i++) {
            var sm = new SuperMacro(list[i]);
            sm.macro_padrao = macros[ macrosIndex[<number>sm.macro_padrao]];
            superMacros.push(sm);
        }
        return superMacros;
    }

    
    clone() {
        return new SuperMacro(this.id, this.descricao, this.macro_padrao);
    }
}