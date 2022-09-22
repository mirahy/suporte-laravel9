import { Component, OnInit } from '@angular/core';
import { AbstractComponent } from '../abstract-component';
import { MacroSuperMacroService } from '../macro-super-macro.service';
import { MacroService } from '../macro.service';
import { Macro } from '../macro/macro';
import { PeriodoLetivosService } from '../periodo-letivos.service';
import { SuperMacroService } from '../super-macro.service';
import { SuperMacro } from './super-macro';
declare var jQuery: any;

@Component({
    selector: 'app-super-macro',
    templateUrl: './super-macro.component.html',
    styleUrls: ['./super-macro.component.less']
})
export class SuperMacroComponent extends AbstractComponent implements OnInit {

    constructor(private superMacroService: SuperMacroService, private macroService: MacroService, 
                private periodoLetivosService: PeriodoLetivosService, private macroSuperMacroService: MacroSuperMacroService) {
        super();
        this.superMacroSelecionada = SuperMacro.generate();
    }

    macroPadraoIdTemp = 0;

    get superMacroSelecionada() {
        return this.superMacroService.superMacroSelecionada;
    }

    set superMacroSelecionada(superMacroSelecionada) {
        this.superMacroService.superMacroSelecionada = superMacroSelecionada;
    }

    get superMacros() :Array<SuperMacro> {
        return this.superMacroService.superMacros;
    }
    get macros() {
        return this.macroService.macros;
    }

    get eu() {
        return this;
    }

    criarAlterarSuperMacro(ev) {
        ev.preventDefault();
        var superMacroForm = jQuery('#superMacroForm')[0];
		if (superMacroForm.reportValidity()) {
            if (this.superMacroSelecionada.id)
                this.alteraSM();
            else
                this.criaSM();
		}
    }

    aplicarMacroPadrao() {
        this.superMacroSelecionada.macro_padrao = this.macroService.macros[this.macroService.macrosIndex[this.macroPadraoIdTemp]];
    }

    novaSM() {
        this.superMacroSelecionada = SuperMacro.generate();
        this.macroPadraoIdTemp = 0;
        this.aviso = "";
    }

    selecionar(superMacro:SuperMacro) {
        this.superMacroSelecionada = superMacro.clone();
        this.macroPadraoIdTemp = (<Macro>(superMacro.macro_padrao)).id;
        this.aviso = "";
        this.macroSuperMacroService.listar(this.superMacroSelecionada)
			.then(response => {
				
			})
			.catch(response => {
				alert(this.erroHttp(response));
                this.status = this.ERROR;
			})
    }

    criaSM() {
        this.editavel = false;
        this.superMacroService.create(this.superMacroSelecionada)
            .then(response => {
                jQuery('#dialogCreateSM').modal('hide');
                this.editavel = true;
            })
            .catch(response => {
                this.aviso = this.erroHttp(response);
                alert(this.aviso);
                this.editavel = true;
            });
    }

    alteraSM () {
        this.editavel = false;
        this.superMacroService.update(this.superMacroSelecionada)
            .then(response => {
                jQuery('#dialogCreateSM').modal('hide');
                this.editavel = true;
            })
            .catch(response => {
                this.aviso = this.erroHttp(response);
                alert(this.aviso);
                this.editavel = true;
            });
    }

    removeSM (superMacro:SuperMacro) {
        if (confirm ("Confirmar exclusão da Super Macro '"+superMacro.descricao+"'?"))
            this.superMacroService.delete(superMacro)
                .then (response => {
                    this.superMacroService.listar()
                    this.novaSM();
                })
                .catch(r => {
                    alert(this.erroHttp(r));
                });
    }

    ngOnInit() {
        this.periodoLetivosService.getPeriodoLetivos()
            .then(response => {
                this.macroService.getMacros()
                    .then(response => {
                        this.superMacroService.listar()
                            .then(response => {
                                this.status = this.COMPLETE;
                                this.editavel = true;
                            })
                            .catch(response => {
                                this.erroAviso = true;
                                this.aviso = this.erroHttp(response);
                                this.status = this.ERROR;
                                alert(this.aviso);
                            })
                    })
                    .catch(response => {
                        this.erroAviso = true;
                        this.aviso = this.erroHttp(response);
                        this.status = this.ERROR;
                        alert(this.aviso);
                    })
            })
            .catch(response => {
                this.erroAviso = true;
                this.aviso = this.erroHttp(response);
                this.status = this.ERROR;
                alert(this.aviso);
            })
        
        
    }

}
