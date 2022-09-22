import { Component, OnInit } from '@angular/core';
import { Faculdade } from '../faculdades/faculdade';
import { CursosService } from '../cursos.service';
import { AbstractComponentChild } from '../abstract-component-child';
import { Curso } from './curso';
declare var jQuery: any;

@Component({
    selector: 'app-cursos',
    templateUrl: './cursos.component.html',
    styleUrls: ['./cursos.component.less']
})
export class CursosComponent extends AbstractComponentChild implements OnInit {

    constructor( private cursosService:CursosService) {
        super();
    }

    cursoTemp:Curso = Curso.generateCurso();

    get faculdadeSelecionada():Faculdade {
        return this.cursosService.faculdadeSelecionada;
    }
    set faculdadeSelecionada(faculdade:Faculdade) {
        this.cursosService.faculdadeSelecionada = faculdade
    }

    criaAltera(ev){
        ev.preventDefault();
        var cursoForm = jQuery('#cursoForm')[0];
		if (cursoForm.reportValidity()) {
            if (this.cursoTemp.id)
                this.altera();
            else
                this.cria();
		}
        
    }

    novo() {
        this.cursoTemp = Curso.generateCurso();
        this.cursoTemp.faculdade = this.faculdadeSelecionada;
        this.aviso = "";
    }

    seleciona (curso:Curso) {
        this.cursoTemp = curso.clone();
        this.aviso = "";
    }

    cria() {
        this.editavel = false;
        this.cursosService.create(this.cursoTemp)
            .then(response => {
                jQuery('#dialogCreateCurso').modal('hide');
                this.editavel = true;
            })
            .catch(response => {
                this.aviso = this.erroHttp(response);
                alert(this.aviso);
                this.editavel = true;
            });
    }

    altera () {
        this.editavel = false;
        this.cursosService.update(this.cursoTemp)
            .then(response => {
                jQuery('#dialogCreateCurso').modal('hide');
                this.editavel = true;
            })
            .catch(response => {
                this.aviso = this.erroHttp(response);
                alert(this.aviso);
                this.editavel = true;
            });
    }

    remove (curso:Curso) {
        if (confirm("Confirmar exclusão deste curso?")) {
            this.editavel = false;
            this.cursosService.delete(this.cursoTemp)
                .then(response => {
                    this.editavel = true;
                })
                .catch(response => {
                    this.aviso = this.erroHttp(response);
                    alert(this.aviso);
                    this.editavel = true;
                });
        }
        
    }

    ngOnInit() {
        this.editavel = true;
    }

}
