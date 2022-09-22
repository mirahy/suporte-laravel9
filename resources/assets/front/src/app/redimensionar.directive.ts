import { Directive, ElementRef, Input } from '@angular/core';
import { DadosService } from './dados.service';
import { Redimensionavel } from './redimensionavel';
declare var jQuery: any;

@Directive({
    selector: '[redimensionar]'
})
export class RedimensionarDirective {

    private _redimensionar:number;
    redimensionavel:Redimensionavel = null;

    constructor(private el:ElementRef, private dadosService:DadosService) {
        jQuery(this.el.nativeElement).addClass('linha-full-height');
    }

    get redimensionar():number {
        return this._redimensionar;
    }
    @Input()
    set redimensionar(value:number) {
        this._redimensionar = value;
        this.processa();
    }

    processa() {
        if (this.redimensionavel == null) {
            this.redimensionavel = new Redimensionavel(this.el.nativeElement, this.redimensionar);
            this.dadosService.componentesHeight.push(this.redimensionavel);
        }
        else
            this.redimensionavel.base = this.redimensionar;
        this.dadosService.resizeFull();
    }
}
