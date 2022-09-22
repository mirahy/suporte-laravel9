import { Injectable } from '@angular/core';
import { Redimensionavel } from './redimensionavel';
import { Http } from '@angular/http';
import { Status } from './status';
import { ArrayIndexador } from './array-indexador';
declare var jQuery: any;

@Injectable()
export class DadosService {

    componentesHeight: Array<Redimensionavel> = [];
    componentesWidth:Array<Redimensionavel> = [];
    componentesPersonalizados:Array<Redimensionavel> = [];

    statuses: Array<Status>;
    statusesIndex: ArrayIndexador<Status>;

    constructor(private http:Http) {
        var _this = this;
        window.onresize = function () {
            _this.resizeFull ();
        };
        jQuery(document).ready(function(){
            _this.resizeFull ();
        });
    }

    statusList() {
        return this.http.get("/statuslist")
            .toPromise()
            .then(response => {
                this.statuses = Status.generateList(response.json());
                this.statusesIndex = new ArrayIndexador<Status>(this.statuses,'chave');
                return this.statuses;
            });
    }
    resizeTabelaLinhaFullHeight (redimensionavel: Redimensionavel) {
        var width = jQuery(window).height() -redimensionavel.base;
        if (redimensionavel.min != null && width < redimensionavel.min)
            width = redimensionavel.min;
        if (redimensionavel.max != null && width > redimensionavel.max)
            width = redimensionavel.max;

        if (jQuery(redimensionavel.elemento).hasClass('modal-body'))
            jQuery(redimensionavel.elemento).css('max-height', width);
        else
            jQuery(redimensionavel.elemento).css('height', width);
    }
    resizeTabelaLinhaFullWidth (redimensionavel: Redimensionavel) {
        if (jQuery(window).width() >= 1200) {
            jQuery(redimensionavel.elemento).css('width', 780 - redimensionavel.base);
        }
        else if (jQuery(window).width() > 990) {
            jQuery(redimensionavel.elemento).css('width', 646 - redimensionavel.base);
        }
        else if (jQuery(window).width() > 748) {
            jQuery(redimensionavel.elemento).css('width', 750 - redimensionavel.base);
        }
        else
            jQuery(redimensionavel.elemento).css('width', jQuery(window).width() -redimensionavel.base);
    }
    resizePersonalizado(redimensionavel:Redimensionavel) {
        var width = jQuery(window).height() -redimensionavel.base;
        if (redimensionavel.min != null && width < redimensionavel.min)
            width = redimensionavel.min;
        if (redimensionavel.max != null && width > redimensionavel.max)
            width = redimensionavel.max;
        redimensionavel.personalizado.parametros.tamanho = width;
        redimensionavel.executarPersonalizado();
    }
    resizeFull () {
        var i:number;
        for(i = 0; i < this.componentesHeight.length; i++) {
            this.resizeTabelaLinhaFullHeight(this.componentesHeight[i]);
        }
        for(i = 0; i < this.componentesWidth.length; i++) {
            this.resizeTabelaLinhaFullWidth(this.componentesWidth[i]);
        }
        for(i = 0; i < this.componentesPersonalizados.length; i++) {
            this.resizePersonalizado(this.componentesPersonalizados[i]);
        }
    }
}
