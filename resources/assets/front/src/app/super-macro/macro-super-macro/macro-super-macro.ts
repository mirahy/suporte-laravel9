import { ArrayIndexador } from '../../array-indexador';
import { Macro } from '../../macro/macro';
import { SuperMacro } from '../super-macro';

export class MacroSuperMacro {
    id: number;
    ordem:number;
    campo:string;
    operador:string;
    valor:string;

    macro:number|Macro;
    super_macro:number|SuperMacro;
    
	constructor(id:(number|any), ordem?:number, campo?:string, operador?:string, valor?:string, macro?:(number|Macro), super_macro?:(number|SuperMacro)) {
        if (typeof id == "number") {
            this.id = id;
            this.ordem = ordem;
            this.campo = campo;
            this.operador = operador;
            this.valor = valor;
            this.macro = macro;
            this.super_macro = super_macro;
        }
        else {
            this.id = id['id'];
            this.ordem = id['ordem'];
            this.campo = id['campo'];
            this.operador = id['operador'];
            this.valor = id['valor'];
            this.macro = id['macro_id'];
            this.super_macro = id['super_macro_id'];
        }
    }
    
    static generate () {
        return new MacroSuperMacro(0,0,"","","",0,0);
    }

    static generateList(list) {
        var macroSuperMacros:Array<MacroSuperMacro> = [];
        for(var i = 0; i < list.length; i++) {
            var sm = new MacroSuperMacro(list[i]);
            macroSuperMacros.push(sm);
        }
        return macroSuperMacros;
    }
    static generateListPlus(list, macros, macrosIndex, superMacroIndex: ArrayIndexador<SuperMacro>) {
        var macroSuperMacros:Array<MacroSuperMacro> = [];
        for(var i = 0; i < list.length; i++) {
            var msm = new MacroSuperMacro(list[i]);
            msm.super_macro = superMacroIndex.get(<number>msm.super_macro);
            msm.macro = macros[ macrosIndex[<number>msm.macro]];
            macroSuperMacros.push(msm);
        }
        return macroSuperMacros;
    }

    getPost() {
        var macro_id = this.macro;
        var super_macro_id = this.super_macro;
        if (this.macro && typeof this.macro == 'object')
            macro_id = this.macro.id;
        if (this.super_macro && typeof this.super_macro == 'object')
            super_macro_id = this.super_macro.id;
        return {
            id: this.id,
            ordem: this.ordem,
            campo: this.campo,
            operador: this.operador,
            valor: this.valor,
            macro_id : macro_id,
            super_macro_id : super_macro_id,
        }
    }
    
    clone() {
        return new MacroSuperMacro(this.id, this.ordem, this.campo, this.operador, this.valor, this.macro, this.super_macro);
    }
}