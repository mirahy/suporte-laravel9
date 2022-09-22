import { Component, OnInit } from '@angular/core';
import { AbstractComponentChild } from 'src/app/abstract-component-child';
import { MacroSuperMacroService } from 'src/app/macro-super-macro.service';
import { MacroService } from 'src/app/macro.service';
import { Macro } from 'src/app/macro/macro';
import { SuperMacroService } from 'src/app/super-macro.service';
import { MacroSuperMacro } from './macro-super-macro';

@Component({
	selector: 'app-macro-super-macro',
	templateUrl: './macro-super-macro.component.html',
	styleUrls: ['./macro-super-macro.component.less']
})
export class MacroSuperMacroComponent extends AbstractComponentChild implements OnInit {

	constructor(private macroSuperMacroService:MacroSuperMacroService, private superMacroService:SuperMacroService, private macroService:MacroService) {
		super();
	}

	msmTemp = MacroSuperMacro.generate();
	macroTemp = Macro.generate()
	emCriacao = false;

	get macroSuperMacros() {
		return this.macroSuperMacroService.macroSuperMacros;
	}

	get superMacro () {
        return this.superMacroService.superMacroSelecionada;
    }
	get macros() {
		return this.macroService.macros;
	}
    get campos() {
        return this.macroSuperMacroService.CAMPOS;
    }
    get operadores() {
        return this.macroSuperMacroService.OPERADORES;
    }

    get invalid() {
        return this.msmTemp.campo == "" || this.msmTemp.valor == "" || !this.msmTemp.macro;
    }

    novo() {
        this.msmTemp  = MacroSuperMacro.generate();
		this.msmTemp.super_macro = this.superMacro;
        this.emCriacao = true;
    }

    editar(msm:MacroSuperMacro) {
        this.msmTemp = msm.clone();
		if (typeof this.msmTemp.macro == "object")
			this.msmTemp.macro = this.msmTemp.macro.id;
        this.emCriacao = false;
    }

    reset() {
        this.emCriacao = false;
        this.msmTemp = MacroSuperMacro.generate();
    }

    deletar(msm:MacroSuperMacro) {
        if (confirm("Deseja excuir esta Diretiva?")) {
            this.editavel = false;
            this.macroSuperMacroService.delete(msm)
            .then (response => {
				this.msmTemp = MacroSuperMacro.generate();
				this.editavel = true;
				this.emCriacao = false;
            })
			.catch(response => {
				//this.erroAviso = true;
				this.aviso = this.erroHttp(response);
				this.status = this.ERROR;
				alert(this.aviso);
			})
        }
        
    }

    concluirEdicao() {
        this.editavel = false;
		if (this.msmTemp.id) {
			this.macroSuperMacroService.update(this.msmTemp)
				.then (response => {
					this.msmTemp = MacroSuperMacro.generate();
					this.editavel = true;
					this.emCriacao = false;
				})
				.catch(response => {
					//this.erroAviso = true;
					this.aviso = this.erroHttp(response);
					this.status = this.ERROR;
					alert(this.aviso);
				})
		}
		else {
			 this.macroSuperMacroService.create(this.msmTemp)
				.then (response => {
					this.msmTemp = MacroSuperMacro.generate();
					this.editavel = true;
					this.emCriacao = false;
				})
				.catch(response => {
					//this.erroAviso = true;
					this.aviso = this.erroHttp(response);
					this.status = this.ERROR;
					alert(this.aviso);
				})
		}
       
    }

	changeOrder(ord1, ord2) {
		this.editavel = false;
		this.macroSuperMacroService.order(ord1, ord2, this.superMacro)
            .then (response => {
				this.editavel = true;
            })
			.catch(response => {
				//this.erroAviso = true;
				this.aviso = this.erroHttp(response);
				this.status = this.ERROR;
				alert(this.aviso);
			})
	}

    ngOnInit() {
        this.reset();
		// requer ancestral chamar "macroService.getMacros()"		
    }

}
