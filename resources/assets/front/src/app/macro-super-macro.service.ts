import { Injectable } from '@angular/core';
import { Http } from '@angular/http';
import { ArrayIndexador } from './array-indexador';
import { MacroService } from './macro.service';
import { PeriodoLetivosService } from './periodo-letivos.service';
import { SuperMacroService } from './super-macro.service';
import { MacroSuperMacro } from './super-macro/macro-super-macro/macro-super-macro';
import { SuperMacro } from './super-macro/super-macro';

@Injectable()
export class MacroSuperMacroService {

	macroSuperMacros:Array<MacroSuperMacro> = [];
    msmIndex: ArrayIndexador<MacroSuperMacro> = null;
    msmIndexOrder: ArrayIndexador<MacroSuperMacro> = null;

	CAMPOS = [];
	OPERADORES = [];

    constructor(private http: Http, private periodoLetivosService: PeriodoLetivosService, private macroService: MacroService, private superMacroService: SuperMacroService) {
        this.getCampos();
        this.getOperadores();
    }

    listar(superMacro:number|SuperMacro) {
		var superMacroId = typeof superMacro == 'object' ? superMacro.id : superMacro;
        return this.http.get("/super-macro/msm/" + superMacroId)
            .toPromise()
            .then(response => {
                this.macroSuperMacros = MacroSuperMacro.generateListPlus(response.json(), this.macroService.macros, this.macroService.macrosIndex, this.superMacroService.superMacrosIndex);
                this.msmIndex = new ArrayIndexador<MacroSuperMacro>(this.macroSuperMacros);
                this.msmIndexOrder = new ArrayIndexador<MacroSuperMacro>(this.macroSuperMacros, 'ordem');
                return this.macroSuperMacros;
            });
    }
    create(msm:MacroSuperMacro) {
        return this.http.post("/super-macro/msm",msm.getPost())
            .toPromise()
            .then(response => {
                return this.listar(msm.super_macro);
            });
    }
    update(msm:MacroSuperMacro) {
        return this.http.put("/super-macro/msm/"+msm.id,msm.getPost())
            .toPromise()
            .then(response => {
                return this.listar(msm.super_macro);
            });
    }
    delete(msm:MacroSuperMacro) {
        return this.http.delete("/super-macro/msm/"+msm.id)
            .toPromise()
            .then(response => {
                return this.listar(msm.super_macro);
            });
    }
	order(msm1:number|MacroSuperMacro, msm2:number|MacroSuperMacro, superMacro:SuperMacro) {
        var m1id = typeof msm1 == 'object' ? msm1.id : this.msmIndexOrder.get(msm1).id;
        var m2id = typeof msm2 == 'object' ? msm2.id : this.msmIndexOrder.get(msm2).id;
		return this.http.get('/super-macro/msm/order/' + m1id + "/" + m2id)
            .toPromise()
            .then(response => {
                return this.listar(superMacro);
            });
	}
	getCampos() {
		return this.http.get('/super-macro/msm/campos')
            .toPromise()
            .then(response => {
                return this.CAMPOS = response.json();
            });
	}
	getOperadores() {
		return this.http.get('/super-macro/msm/operadores')
            .toPromise()
            .then(response => {
                return this.OPERADORES = response.json();
            });
	}
}
