import { Component, OnInit } from '@angular/core';
import { MacroService } from '../macro.service';
import { AbstractComponent } from '../abstract-component';
import { Macro } from './macro';
import { Buscador } from '../buscadores/buscador';
import { PeriodoLetivo } from '../periodo-letivos/periodo-letivo';
import { PeriodoLetivosService } from '../periodo-letivos.service';
import { ServidoresMoodleService } from '../servidores-moodle.service';
declare var jQuery: any;

@Component({
    selector: 'app-macro',
    templateUrl: './macro.component.html',
    styleUrls: ['./macro.component.less']
})
export class MacroComponent extends AbstractComponent implements OnInit {

    constructor(private macroService: MacroService, private periodoLetivosService:PeriodoLetivosService, private servidoresMoodleService: ServidoresMoodleService) {
        super();
    }

    periodoLetivoTempIndex:number = 0;

    linksMoodles = [];

    get files() {
        return this.macroService.files;
    }
    set files(files) {
        this.macroService.files = files;
    }
    get macros() {
        return this.macroService.macros;
    }
    get macroSelecionada(): Macro {
        return this.macroService.macroSelecionada;
    }
    set macroSelecionada(macro: Macro) {
        this.macroService.macroSelecionada = macro;
    }
    get entradas() {
        return this.macroService.ENTRADAS;
    }
    get periodoLetivos() { 
        return this.periodoLetivosService.periodoLetivos;
    }

    get eu() {
        return this;
    }


    criarAlterarMacro() {
        var posSelecionar = this.macroSelecionada.id ? this.macroService.macrosIndex[this.macroSelecionada.id] : this.macroService.macros.length;
        this.macroService.criarAlterarMacro ()
            .then(response => {
                this.macroService.getMacros()
                    .then(response => {
                        this.selecionarMacro(this.macroService.macros[posSelecionar]);
                        jQuery('#dialogCriar').modal('hide');
                    })
                    .catch(r => {
                        alert(this.erroHttp(r));
                    });
            })
            .catch(r => {
                alert(this.erroHttp(r));
            });
    }

    aplicarPeriodoLetivo() {
        this.macroSelecionada.periodo_letivo = this.periodoLetivosService.periodoLetivos[this.periodoLetivosService.periodoLetivosIndex[this.periodoLetivoTempIndex]];
    }

    delMacro(macro: Macro) {
        if (confirm ("Confirmar exclusão da macro '"+macro.nome+"'?"))
            this.macroService.delMacro(macro.id)
                .then (response => {
                    this.macroService.getMacros()
                })
                .catch(r => {
                    alert(this.erroHttp(r));
                });
    }
    
    selecionarMacro(macro: Macro) {
        if (macro == null){
            this.macroService.resetMacroSelecionada();
            this.periodoLetivoTempIndex = 0;
        }
        else {
            this.macroSelecionada = macro.clone();
            this.editavel = false;
            this.periodoLetivoTempIndex = (<PeriodoLetivo>this.macroSelecionada.periodo_letivo).id;
            this.macroService.getBuscadores()
                .then (response => {
                    this.editavel = true;
                })
                .catch(r => {
                    alert(this.erroHttp(r));
                });
        }
    }

    ngOnInit() {
        this.editavel = true;
        this.carregarLista();

    }

    carregarLista(): Promise<any> {
        return this.macroService.getEntradasBuscadores()
            .then(response => {
                this.macroService.getListFiles()
                    .then(response => {
                        this.periodoLetivosService.getPeriodoLetivos()
                            .then(response => {
                                this.servidoresMoodleService.getLinksServidoresMoodle()
                                    .then(response => {
                                        this.linksMoodles = response;
                                        this.macroService.getMacros()
                                            .then(response => {
                                                this.status = this.COMPLETE;
                                                return response;
                                            })
                                            .catch(r => {
                                                alert(this.erroHttp(r));
                                            });
                                    })
                                    .catch(r => {
                                        alert(this.erroHttp(r));
                                    });
                            })
                            .catch(r => {
                                alert(this.erroHttp(r));
                            });
                       
                    })
                    .catch(r => {
                        alert(this.erroHttp(r));
                    });
            })
            .catch(r => {
                alert(this.erroHttp(r));
            });


    }

}
