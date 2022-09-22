import { Component, OnInit } from '@angular/core';
import { AbstractComponent } from '../abstract-component';
import { PeriodoLetivosService } from '../periodo-letivos.service';
import { PeriodoLetivosCategoriasService } from '../periodo-letivos-categorias.service';
import { PeriodoLetivo } from '../periodo-letivos/periodo-letivo';
import { FaculdadeService } from '../faculdade.service';
import { CursosService } from '../cursos.service';
import { Curso } from '../cursos/curso';

@Component({
    selector: 'app-periodo-letivos-categorias',
    templateUrl: './periodo-letivos-categorias.component.html',
    styleUrls: ['./periodo-letivos-categorias.component.less']
})
export class PeriodoLetivosCategoriasComponent extends AbstractComponent implements OnInit {

    constructor(private periodoLetivosService:PeriodoLetivosService, private periodoLetivoCategoriasService:PeriodoLetivosCategoriasService, 
        private faculdadeService:FaculdadeService, private cursosService:CursosService) { 
        super();
    }

    periodoLetivo:PeriodoLetivo = PeriodoLetivo.generatePeriodoLetivo();
    emAlteracao:boolean = false;
    cursoSelecionado:Curso = Curso.generateCurso();
    categoriaIdTemp:number;
    criteria:string = "";
    
    get periodoLetivos() {
        return this.periodoLetivosService.periodoLetivos;
    }
    get faculdades() {
        return this.faculdadeService.faculdades;
    }
    get periodoLetivosCategoriasIndex() {
        return this.periodoLetivoCategoriasService.periodoLetivosCategoriasIndex;
    }

    selecionarPeriodoLetivo(pl) {
        this.periodoLetivo = pl;
        this.editavel = false;
        this.getPeriodoLetivosCategorias();
        this.resetCursoSelecionado();
    }

    getCategoriaId(curso:Curso) {
        if(this.periodoLetivo.id && curso.id && this.periodoLetivosCategoriasIndex){
            return this.periodoLetivosCategoriasIndex.get(curso.id).categoria_id;
        }
        return 0;
    }

    selecionarCurso(curso:Curso) {
        this.cursoSelecionado = curso;
        this.categoriaIdTemp = this.getCategoriaId(curso);
        setTimeout ( function () {
            var categoriaIdInput = document.getElementById('categoriaIdInput');
            categoriaIdInput.focus();
            (<HTMLInputElement>categoriaIdInput).setSelectionRange(0,(this.categoriaIdTemp+"").length);
        },150);
    }
    anularCategoria(curso:Curso){
        if(confirm("Confirmar Anulação desta Categoria?") && this.periodoLetivo.id && curso.id && this.periodoLetivosCategoriasIndex){
            this.editavel = false;
            this.periodoLetivoCategoriasService.setCategoriaId(this.periodoLetivo.id, curso.id, 0)
                .then(r => {
                    this.editavel = true;
                    
                }).catch(response => {
                    this.erroAviso = true;
                    this.aviso = this.erroHttp(response);
                    alert(this.aviso);
                    this.resetCursoSelecionado();
                    this.editavel = true;
                });
        }
    }
    concluirEdicaoCategoria(curso:Curso) {
        if(this.periodoLetivo.id && curso.id && this.periodoLetivosCategoriasIndex){
            this.editavel = false;
            this.periodoLetivoCategoriasService.setCategoriaId(this.periodoLetivo.id, curso.id, this.categoriaIdTemp)
                .then(r => {
                    this.editavel = true;
                    this.resetCursoSelecionado();
                }).catch(response => {
                    this.erroAviso = true;
                    this.aviso = this.erroHttp(response);
                    alert(this.aviso);
                    this.resetCursoSelecionado();
                    this.editavel = true;
                });
        }
    }
    resetCursoSelecionado() {
        this.cursoSelecionado = Curso.generateCurso();
    }

    getPeriodoLetivosCategorias () {
        this.periodoLetivoCategoriasService.getPeriodoLetivosCategrias(this.periodoLetivo.id)
            .then(r => {
                this.editavel = true;
                
            }).catch(response => {
                this.erroAviso = true;
                this.aviso = this.erroHttp(response);
                alert(this.aviso);
            });
    }

    ngOnInit() {
        this.faculdadeService.listar()
            .then(response => {
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
            })
            .catch(response => {
                this.erroAviso = true;
                this.aviso = this.erroHttp(response);
                this.status = this.ERROR;
                alert(this.aviso);
            })
        
    }

}
