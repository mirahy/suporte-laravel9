import { ArrayIndexador } from "../array-indexador";
import { ServidorMoodle } from "../servidores-moodle/servidor-moodle";
import { SuperMacro } from "../super-macro/super-macro";

export class LoteSalasSimplificado {
    id: number;
    grupo_id:number;
    descricao:string;
    sala_provao_id:any;
    super_macro:SuperMacro;
    servidor_moodle: ServidorMoodle;
    sufixo:string;

    
	constructor(id:(number|any), grupo_id?:number, descricao?:string, sala_provao_id?:any, super_macro?:SuperMacro, servidor_moodle?: ServidorMoodle, sufixo?:string) {
        if (typeof id == "number") {
            this.id = id;
            this.grupo_id = grupo_id;
            this.descricao = descricao;
            this.sala_provao_id = sala_provao_id;
            this.super_macro = super_macro;
            this.servidor_moodle = servidor_moodle;
            this.sufixo = sufixo;
        }
        else {
            this.id = id['id'];
            this.grupo_id = id['grupo_id'];
            this.descricao = id['descricao'];
            this.sala_provao_id = id['sala_provao_id'];
            this.super_macro = id['super_macro_id'];
            this.servidor_moodle = id['servidor_moodle_id'];
            this.sufixo = id['sufixo'];
        }
    }

    getPost() {
        return {
            'id' : this.id,
            'grupo_id' : this.grupo_id,
            'descricao' : this.descricao,
            'sala_provao_id' : this.sala_provao_id,
            'super_macro_id' : (this.super_macro ? this.super_macro.id : null),
            'servidor_moodle_id' : (this.servidor_moodle ? this.servidor_moodle.id : null),
            'sufixo' : (this.sufixo ? this.sufixo : null)
        }
    }
    
    static generate () {
        return new LoteSalasSimplificado(0, 0, "", "", null, null, null);
    }

    static generateList(list) {
        var lotes:Array<LoteSalasSimplificado> = [];
        for(var i = 0; i < list.length; i++) {
            var lote = new LoteSalasSimplificado(list[i]);
            lotes.push(lote);
        }
        return lotes;
    }

    static generateListPlus(list, superMacrosIndex:ArrayIndexador<SuperMacro>, servidoresMoodleIndex:ArrayIndexador<ServidorMoodle>) {
        var lotes:Array<LoteSalasSimplificado> = [];
        for(var i = 0; i < list.length; i++) {
            list[i]['super_macro_id'] = superMacrosIndex.get (list[i]['super_macro_id']);
            list[i]['servidor_moodle_id'] = servidoresMoodleIndex.get (list[i]['servidor_moodle_id']);
            var lote = new LoteSalasSimplificado(list[i]);
            lotes.push(lote);
        }
        return lotes;
    }
    
    clone() {
        return new LoteSalasSimplificado(this.id, this.grupo_id, this.descricao, this.sala_provao_id, this.super_macro, this.servidor_moodle, this.sufixo);
    }
}