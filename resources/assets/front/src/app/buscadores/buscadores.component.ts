import { Component, OnInit, Input } from '@angular/core';
import { Macro } from '../macro/macro';
import { MacroService } from '../macro.service';
import { AbstractComponent } from '../abstract-component';
import { AbstractComponentChild } from '../abstract-component-child';
import { Buscador } from './buscador';

@Component({
    selector: 'app-buscadores',
    templateUrl: './buscadores.component.html',
    styleUrls: ['./buscadores.component.less']
})
export class BuscadoresComponent extends AbstractComponentChild implements OnInit {


    buscadorTemp:Buscador = new Buscador(0, "","");
    emCriacao:boolean = false;

    constructor(private macroService:MacroService) {
        super();
    }

    get macro () {
        return this.macroService.macroSelecionada;
    }
    get entradas() {
        return this.macroService.ENTRADAS;
    }

    get invalid() {
        return this.buscadorTemp.chave == "" || this.buscadorTemp.entrada == "";
    }

    novo() {
        this.buscadorTemp = new Buscador(0,"","");
        this.emCriacao = true;
    }

    editar(buscador:Buscador) {
        this.buscadorTemp = buscador.clone();
        this.emCriacao = false;
    }

    reset() {
        this.emCriacao = false;
        this.buscadorTemp = new Buscador(0,"","");
    }

    deletar(buscador:Buscador) {
        if (confirm("Deseja excuir este buscador?")) {
            this.editavel = false;
            this.macroService.delBuscador(buscador)
            .then (response => {
                this.macroService.getBuscadores()
                    .then (response => {
                        this.buscadorTemp = new Buscador(0, "","");
                        this.editavel = true;
                        this.emCriacao = false;
                    }) 
            })
        }
        
    }

    concluirEdicao() {
        this.editavel = false;
        this.macroService.addBuscador(this.buscadorTemp)
            .then (response => {
                this.macroService.getBuscadores()
                    .then (response => {
                        this.buscadorTemp = new Buscador(0, "","");
                        this.editavel = true;
                        this.emCriacao = false;
                    }) 
            })
    }

    ngOnInit() {
        this.reset();
    }

}
