import { Input } from '@angular/core';
import { AbstractComponentInterface } from './abstract-component-interface';

export abstract class AbstractComponentChild implements AbstractComponentInterface {

    
    private _ancestral:AbstractComponentInterface;

    aviso:string = "";
    erroAviso:boolean;

    get LOADING () {
        return this._ancestral.LOADING;
    }
    get COMPLETE () {
        return this._ancestral.COMPLETE;
    }
    get ERROR () {
        return this._ancestral.ERROR;
    }
    get editavel() {
        return this._ancestral.editavel;
    }
    set editavel (editavel:boolean) {
        this._ancestral.editavel = editavel;
    }
    get status() {
        return this._ancestral.status;
    }
    set status(status:number) {
        this._ancestral.status = status
    }

    @Input() 
    set ancestral(ancestral:AbstractComponentInterface) {
        this._ancestral = ancestral;
    }
    get ancestral():AbstractComponentInterface {
        return this._ancestral;
    }

    constructor() { }

    erroHttp (response):string {
        var mensagem:string;
        if (response.status == 0) {
            mensagem = "Falha na Conexão!";
        }
        else {
            if (response.json) 
                mensagem = response.json().message;
            else 
                mensagem = response
        }
            
        return mensagem;
    }
}