import { Component, OnInit } from '@angular/core';
import { AbstractComponent } from '../abstract-component';
import { PeriodoLetivosService } from '../periodo-letivos.service';
import { PeriodoLetivo } from './periodo-letivo';
declare var jQuery: any;

@Component({
    selector: 'app-periodo-letivos',
    templateUrl: './periodo-letivos.component.html',
    styleUrls: ['./periodo-letivos.component.less']
})
export class PeriodoLetivosComponent extends AbstractComponent implements OnInit {

    constructor(private periodoLetivosService:PeriodoLetivosService) { 
        super();
    }

    get periodoLetivos() {
        return this.periodoLetivosService.periodoLetivos;
    }

    periodoLetivo:PeriodoLetivo = PeriodoLetivo.generatePeriodoLetivo();
    periodoLetivosSigecad:Array<PeriodoLetivo> = [];
    statusPLS = this.LOADING;

    novoPeriodoLetivo() {
        this.periodoLetivo = PeriodoLetivo.generatePeriodoLetivo();
        this.aviso = "";
        this.erroAviso = false;
        this.carregarListaSigecad();
    }

    criaAlteraPeriodoLetivo(ev){
        ev.preventDefault();
        var periodoLetivoForm = jQuery('#periodoLetivoForm')[0];
		if (periodoLetivoForm.reportValidity()) {
            this.periodoLetivosService.createUpdate(this.periodoLetivo)
                .then(r => {
                    jQuery('#dialogCreate').modal('hide');
                    
                }).catch(response => {
                    this.erroAviso = true;
                    this.aviso = this.erroHttp(response);
                });
		}
    }

    selecionaPeriodoLetivo(pl:PeriodoLetivo) {
        this.aviso = "";
        this.erroAviso = false;
        this.periodoLetivo = pl.clone();
    }

    removePeriodoLetivo(pl:PeriodoLetivo) {
        if (confirm("Confirmar Exclusão deste Período Letivo")) {
            this.periodoLetivosService.delete(pl)
                .then(r => {
                                        
                }).catch(response => {
                    this.erroAviso = true;
                    this.aviso = this.erroHttp(response);
                    alert(this.aviso);
                });
        }
    }

    carregarListaSigecad() {
        this.statusPLS = this.LOADING;
        this.periodoLetivosSigecad = [];
        this.periodoLetivosService.getPeriodoLetivosSigecad()
            .then(r => {
                this.statusPLS = this.COMPLETE;
                this.periodoLetivosSigecad = r;
                //jQuery('#dialogLoadSigecad').modal('hide');
                //jQuery('#dialogCreate').modal('show');
            }).catch(response => {
                this.erroAviso = true;
                this.aviso = this.erroHttp(response);
                this.statusPLS = this.ERROR;
                alert(this.aviso);
            });
    }

    ngOnInit() {
        this.periodoLetivosService.getPeriodoLetivos()
            .then(r => {
                this.status = this.COMPLETE;
                this.editavel = true;
            }).catch(response => {
                this.erroAviso = true;
                this.aviso = this.erroHttp(response);
                this.status = this.ERROR;
                alert(this.aviso);
            });
    }

}
